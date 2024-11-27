<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblSoftDashWidget;
use App\Models\TblSoftDashWidgetBadge;
use App\Models\TblSoftSvg;
use Illuminate\Http\Request;
use Image;
// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class DashboardStudioBadges extends Controller
{
    public static $page_title = 'Badges';
    public static $redirect_url = 'badge';
    public static $menu_dtl_id = '61';
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
            if(TblSoftDashWidget::where('dash_widget_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblSoftDashWidget::with('badgeDtl')->where('dash_widget_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['svg'] = TblSoftSvg::get();
        //dd($data);
        return view('dashboard.badges.form',compact('data'));
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

            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'dash_widget_name' => 'required|unique:tbl_soft_dash_widget',
            ]);
        }
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try {
            if(isset($id)){
                $DashWidget = TblSoftDashWidget::where('dash_widget_id',$id)->first();
            }else{
                $DashWidget = new TblSoftDashWidget();
                $DashWidget->dash_widget_id = Utilities::uuid();
            }
            $form_id = $DashWidget->dash_widget_id;
            $DashWidget->dash_widget_name = $request->dash_widget_name;
            $DashWidget->business_id = auth()->user()->business_id;
            $DashWidget->company_id = auth()->user()->company_id;
            $DashWidget->branch_id = auth()->user()->branch_id;
            $DashWidget->dash_widget_user_id = auth()->user()->id;
            $DashWidget->save();

            if(isset($id)){
                $del_Dtls = TblSoftDashWidgetBadge::where('dash_widget_id',$id)->get();
                foreach ($del_Dtls as $del_Dtls){
                    TblSoftDashWidgetBadge::where('dash_widget_id',$del_Dtls->dash_widget_id)->delete();
                }
            }

            foreach($request->badges as $badge){
                $DashWidgetBadge = new TblSoftDashWidgetBadge();
                if(isset($id)){
                    $DashWidgetBadge->dash_widget_id = $id;
                }else{
                    $DashWidgetBadge->dash_widget_id = $DashWidget->dash_widget_id;
                }
                $DashWidgetBadge->dash_widget_badge_id = Utilities::uuid();
                $DashWidgetBadge->dash_widget_badge_name = $badge['dash_widget_badge_name'];
                $DashWidgetBadge->dash_widget_case_name = $badge['dash_widget_case_name'];
                $DashWidgetBadge->dash_widget_badge_color = $badge['dash_widget_badge_color'];
                $DashWidgetBadge->dash_widget_badge_detail = $badge['dash_widget_badge_detail'];
                $DashWidgetBadge->dash_widget_badge_bg_color = $badge['dash_widget_badge_bg_color'];
                $DashWidgetBadge->dash_widget_badge_svg = $badge['dash_widget_badge_svg'];
                $DashWidgetBadge->dash_widget_badge_query = $badge['dash_widget_badge_query'];
                $DashWidgetBadge->dash_widget_badge_svg_color = $badge['dash_widget_badge_svg_color'];
                $DashWidgetBadge->dash_widget_badge_svg_name = $badge['dash_widget_badge_svg_name'];
                if(isset($badge['dash_widget_badge_bg_img']))
                {
                    //  $image = $badge->file('dash_widget_badge_bg_img');
                    $filename = time() . '.' . $badge['dash_widget_badge_bg_img']->getClientOriginalExtension();
                    $path = public_path('/images/' . $filename);
                    Image::make($badge['dash_widget_badge_bg_img']->getRealPath())->resize(200, 200)->save($path);
                    $DashWidgetBadge->dash_widget_badge_bg_img = isset($filename)?$filename:'';
                }
                $DashWidgetBadge->dash_widget_badge_entry_status = 1;
                $DashWidgetBadge->save();
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
        $data = [];
        DB::beginTransaction();
        try{

            $DashWidgetBadge = TblSoftDashWidget::where('dash_widget_id',$id)->first();
            $DashWidgetBadge->badgeDtl()->delete();
            $DashWidgetBadge->delete();

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
        return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    }
}
