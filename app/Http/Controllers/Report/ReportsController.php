<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Permission;
use App\Models\TblSoftMenuDtl;
use App\Models\TblSoftReports;
use App\Models\TblSoftReportStaticCriteria;
use App\Models\TblSoftReportStyling;
use App\Models\TblSoftReportUserCriteria;
use App\Models\ViewAllColumnData;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReportsController extends Controller
{
    public static $page_title = 'Reporting';
    public static $redirect_url = 'reports';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblSoftReports::where('report_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblSoftReports::with('report_styling')->where('report_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['report_code'] = $this->documentCode(TblSoftReports::max('report_code'),'R');
        }

        $data['report_menu'] = TblSoftMenuDtl::where('menu_id',6)->where('menu_dtl_id','!=',46)->get();
        $sorted =  ViewAllColumnData::select('table_name')->groupby('table_name')->get();
        $collection = collect($sorted);
        $data['table_list'] = $collection->sortBy('table_name');
        $data['static_criteria'] = TblSoftReportStaticCriteria::where('report_static_criteria_entry_status',1)->orderBy('report_static_criteria_title')->get();
        $data['column_types'] = config('constants.column_types');
        $data['style_listing'] = config('constants.style_listing');
        return view('reports.form', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        $data = [];
        if(isset($id)){
            $validator = Validator::make($request->all(), [
                'report_title' => 'required|max:255',
                'report_static_dynamic' => ['required', Rule::in(['static', 'dynamic'])],
                'report_table_style_layout' => ['required', Rule::in(['listing', 'listing_group','tabular_report'])],
                'menu_dtl_id' => 'required|numeric',
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'report_title' => 'required|max:255',
                'report_case' => 'required|max:255|unique:tbl_soft_report',
                'report_static_dynamic' => ['required', Rule::in(['static', 'dynamic'])],
                'report_table_style_layout' => ['required', Rule::in(['listing', 'listing_group','tabular_report'])],
                'menu_dtl_id' => 'required|numeric',
            ]);
        }
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
           // dd($request->toArray());
            if(isset($id)){
                $report = TblSoftReports::where('report_id',$id)->first();
            }else{
                $report = new TblSoftReports();
                $report->report_id = Utilities::uuid();
                $report->report_code = $this->documentCode(TblSoftReports::max('report_code'),'R');;
                $report->report_case = $request->report_case;
                $report->menu_dtl_id = TblSoftMenuDtl::max('menu_dtl_id')+1;
            }
            $form_id = $report->report_id;
            $report->report_title = $request->report_title;
            $report->report_static_dynamic = $request->report_static_dynamic;
            $report->parent_menu_id = $request->menu_dtl_id;
            $report->report_date = $request->report_date;
            $report->report_entry_status = 1;
            $report->business_id = auth()->user()->business_id;
            $report->company_id = auth()->user()->company_id;
            $report->branch_id = auth()->user()->branch_id;
            $report->report_user_id = auth()->user()->id;
            if($request->report_static_criteria != null) {
                $static_criteria = implode(",",$request->report_static_criteria);
                $report->report_static_criteria = $static_criteria;
            }else{
                $report->report_static_criteria = '';
            }

            if($request->report_static_dynamic == 'dynamic'){
                $report->report_query = $request->dynamic_query;
                $report->report_column_sr_no = isset($request->report_column_sr)?1:0;
                $report->report_table_style_layout = $request->report_table_style_layout;
                $group_key_1_date = isset($request->group_key_1_date)?1:0;
                $group_key_2_date = isset($request->group_key_2_date)?1:0;
                $report->report_data_grouping_keys = $request->group_key_1.','.$group_key_1_date.','.$request->group_key_2.','.$group_key_2_date;
            }
            $report->save();
            // dd($request->user_criteria);
            if($request->report_static_dynamic == 'dynamic'){
                TblSoftReportStyling::where('report_id',$report->report_id)->delete();
                foreach ($request->user_criteria as $key=>$user_criteria){
                    // row create criteria_active
                    $arr['report_id'] = $report->report_id;
                    $arr['key'] = $key;
                    $arr['type'] = 'element';

                    $arr['colm_key'] = 'criteria_active'; // for column show hide in generate report
                    $arr['val'] = isset($user_criteria['criteria_active'][0])?1:0;
                    $this->reportStyle($arr);

                    $arr['colm_key'] = 'column_toggle'; // for column show hide in view report
                    $arr['val'] = isset($user_criteria['column_toggle'][0])?1:0;
                    $this->reportStyle($arr);

                    $arr['colm_key'] = 'heading_name'; // for column show hide in view report
                    $arr['val'] = $user_criteria['report_dynamic_heading_name'];
                    $this->reportStyle($arr);

                    $arr['colm_key'] = 'key_name';
                    $arr['val'] = $this->strLowerTrim($user_criteria['report_dynamic_key_name']);
                    $this->reportStyle($arr);

                    $arr['colm_key'] = 'column_type';
                    $report_dynamic_column_type = $this->strLowerTrim($user_criteria['report_dynamic_column_type']);
                    $arr['val'] = $report_dynamic_column_type;
                    $this->reportStyle($arr);

                    $arr['colm_key'] = 'decimal';
                    if($report_dynamic_column_type == 'float'){
                        $arr['val'] = (int)$user_criteria['report_dynamic_decimal'];
                    }else{
                        $arr['val'] = "";
                    }
                    $this->reportStyle($arr);
                    $arr['colm_key'] = 'calc';
                    if($report_dynamic_column_type == 'float' || $report_dynamic_column_type == 'number'){
                        $arr['val'] = isset($user_criteria['report_dynamic_calculation'][0])?1:"";
                    }else{
                        $arr['val'] = "";
                    }
                    $this->reportStyle($arr);

                    if(!empty($user_criteria['report_dynamic_heading_name']) && !empty($user_criteria['report_dynamic_column_type'])){
                        $arr = [];
                        $arr['report_id'] = $report->report_id;
                        $arr['key'] = $key;
                        if(!empty($user_criteria['report_dynamic_heading_style_column_align'])){
                            $arr['type'] = 'th';
                            $arr['colm_key'] = 'text-align';
                            $arr['val'] = $user_criteria['report_dynamic_heading_style_column_align'];
                            $this->reportStyle($arr);
                        }
                        if(!empty($user_criteria['report_dynamic_heading_style_font_size'])){
                            $arr['type'] = 'th';
                            $arr['colm_key'] = 'font-size';
                            $arr['val'] = $user_criteria['report_dynamic_heading_style_font_size'].'px';
                            $this->reportStyle($arr);
                        }
                        if(!isset($user_criteria['report_dynamic_heading_style_color_transparent'])){
                            $arr['type'] = 'th';
                            $arr['colm_key'] = 'color';
                            $arr['val'] = $user_criteria['report_dynamic_heading_style_color'];
                            $this->reportStyle($arr);
                        }
                        if(!isset($user_criteria['report_dynamic_heading_style_bgcolor_transparent'])){
                            $arr['type'] = 'th';
                            $arr['colm_key'] = 'background-color';
                            $arr['val'] = $user_criteria['report_dynamic_heading_style_bgcolor'];
                            $this->reportStyle($arr);
                        }
                        if(!empty($user_criteria['report_dynamic_heading_style_width'])){
                            $arr['type'] = 'th';
                            $arr['colm_key'] = 'width';
                            $arr['val'] = $user_criteria['report_dynamic_heading_style_width'].'px';
                            $this->reportStyle($arr);
                        }
                        if(!empty($user_criteria['report_dynamic_body_style_column_align'])){
                            $arr['type'] = 'td';
                            $arr['colm_key'] = 'text-align';
                            $arr['val'] = $user_criteria['report_dynamic_body_style_column_align'];
                            $this->reportStyle($arr);
                        }
                        if(!empty($user_criteria['report_dynamic_body_style_font_size'])){
                            $arr['type'] = 'td';
                            $arr['colm_key'] = 'font-size';
                            $arr['val'] = $user_criteria['report_dynamic_body_style_font_size'].'px';
                            $this->reportStyle($arr);
                        }
                        if(!isset($user_criteria['report_dynamic_body_style_color_transparent'])){
                            $arr['type'] = 'td';
                            $arr['colm_key'] = 'color';
                            $arr['val'] = $user_criteria['report_dynamic_body_style_color'];
                            $this->reportStyle($arr);
                        }
                        if(!isset($user_criteria['report_dynamic_body_style_bgcolor_transparent'])){
                            $arr['type'] = 'td';
                            $arr['colm_key'] = 'background-color';
                            $arr['val'] = $user_criteria['report_dynamic_body_style_bgcolor'];
                            $this->reportStyle($arr);
                        }
                    }
                }
            }
            if(!isset($id)) {
                $menu = new TblSoftMenuDtl();
                $menu->menu_dtl_id = $report->menu_dtl_id;
                $menu->menu_id = 10;
                $menu->menu_dtl_name = $request->report_title . ' Report';
                $menu->parent_menu_id = $request->menu_dtl_id;
                $menu->menu_dtl_description = $request->report_static_dynamic . ' report';
                $menu->menu_dtl_visibility = 1;
                $menu->business_id = auth()->user()->business_id;
                $menu->company_id = auth()->user()->company_id;
                $menu->branch_id = auth()->user()->branch_id;
                $menu->save();

                $permission = new Permission();
                $permission->id = Utilities::uuid();
                $permission->menu_id = $menu->menu_id;
                $permission->menu_dtl_id = $menu->menu_dtl_id;
                $permission->name = $menu->menu_dtl_id.'-view';
                $permission->display_name = 'view';
                $permission->description = ucwords($menu->menu_dtl_name).' '.ucwords('view');
                $permission->save();
            }
        }catch (QueryException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }
    public function reportStyle($arr){
        $styling = [
            'report_styling_id' => Utilities::uuid(),
            'report_id' => $arr['report_id'],
            'report_styling_column_no' => $arr['key'],
            'report_styling_column_type' => $arr['type'],
            'report_styling_key' => $arr['colm_key'],
            'report_styling_value' => $arr['val']
        ];
        TblSoftReportStyling::create($styling);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
