<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblSoftDashWidgetGraph;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class DashboardStudioGraphBar extends Controller
{
    public static $page_title = 'Graph Bar';
    public static $redirect_url = 'graph-bar';
    public static $menu_dtl_id = '60';
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
            if(TblSoftDashWidgetGraph::where('dash_widget_graph_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblSoftDashWidgetGraph::where('dash_widget_graph_id',$id)->first();
                $data['qry'] = [];
                 array_push($data['qry'], $data['current']->query_1);
                 array_push($data['qry'], $data['current']->query_2);
                 array_push($data['qry'], $data['current']->query_3);
                 array_push($data['qry'], $data['current']->query_4);
                 array_push($data['qry'], $data['current']->query_5);
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        // dd($data);
        return view('dashboard.graph_bar.form',compact('data'));
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
        $validator = Validator::make($request->all(), [
            'widget_name' => 'required|max:100',
            'widget_case_name' => 'required|max:100',
            'y_axis' => 'required',
            'x_axis_titles_qry' => 'required'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try {
            if(isset($id)){
                $DashWidgetGraph = TblSoftDashWidgetGraph::where('dash_widget_graph_id',$id)->first();
            }else{
                $DashWidgetGraph = new TblSoftDashWidgetGraph();
                $DashWidgetGraph->dash_widget_graph_id = Utilities::uuid();
            }
            $form_id = $DashWidgetGraph->dash_widget_graph_id;
            $DashWidgetGraph->dash_widget_case_name = $request->widget_case_name;
            $DashWidgetGraph->dash_widget_graph_name = $request->widget_name;
            $DashWidgetGraph->y_axis = $request->y_axis;
            $DashWidgetGraph->x_axis = implode(", ", $request->x_axis);
            $DashWidgetGraph->x_axis_titles_qry = $request->x_axis_titles_qry;
            $i = 1;
            foreach ($request->x_axis_values_query as $x_axis_values_query){
                $q = 'query_'.$i;
                $DashWidgetGraph->$q =  $x_axis_values_query['x_axis_values_qry'];
                $i++;
            }
            $DashWidgetGraph->dash_widget_graph_entry_status = 1;
            $DashWidgetGraph->business_id = auth()->user()->business_id;
            $DashWidgetGraph->company_id = auth()->user()->company_id;
            $DashWidgetGraph->branch_id = auth()->user()->branch_id;
            $DashWidgetGraph->dash_widget_graph_user_id = auth()->user()->id;
            $DashWidgetGraph->save();

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

            $DashWidgetGraph = TblSoftDashWidgetGraph::where('dash_widget_graph_id',$id)->first();
            $DashWidgetGraph->delete();

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
