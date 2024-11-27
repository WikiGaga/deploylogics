<?php

namespace App\Http\Controllers\EServices;

use Exception;
use Validator;
use App\Models\User;
use App\Library\Utilities;
use App\Models\TblDefiArea;
use App\Models\TblDefiCity;
use Illuminate\Http\Request;
use App\Models\TblSaleSalesOrder;
use App\Models\TblDefiOrderStatus;
use Illuminate\Support\Facades\DB;
use App\Models\TblServUpdateStatus;
use App\Http\Controllers\Controller;
use App\Models\TblServManageSchedule;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateStatusController extends Controller
{
    public static $page_title = 'Services Update Status';
    public static $redirect_url = 'services-update-status';
    public static $menu_dtl_id = '216';
    public static $type = 'STU';
    
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
    public function create(Request $request , $id = null)
    {
        $data['page_data'] = [];$idsArr = [];$data['selected_areas'] = [];$data['search_applied'] = FALSE;
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;

        if(isset($id)){
            if(TblServUpdateStatus::where('update_status_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['first_current'] = TblServUpdateStatus::where('update_status_id',$id)->first();
                $data['document_code'] = $data['first_current']->update_status_code;
                $data['current'] = TblServUpdateStatus::with('dtls','quotation','order','schedule')->where('update_status_id',$id)->get();
                // Usecase : In the Update Case Show the contained Items
                foreach ($data['current'] as $or) {
                    array_push($idsArr , $or->order_id);
                }
                // dd($data['current']);
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['document_code'] = $this->documentCode(TblServUpdateStatus::max('update_status_code'),'STU');
        }
        
        // Get the Searching Results and Send them with Data
        if ($request->isMethod('get') && isset($request->search)) {
            $data['filter_cities'] = isset($request->filter_cities) ? $request->filter_cities : [];
            $data['areas'] = isset($request->filter_areas) ? $request->filter_areas : [];
            $data['schedule'] = isset($request->filter_schedule) ? $request->filter_schedule : [];
            $data['status'] = isset($request->filter_status) ? $request->filter_status : [];
            $data['salesman'] = isset($request->filter_salesman) ? $request->filter_salesman : [];

            if(count($data['filter_cities']) > 0){
                $data['selected_areas'] = TblDefiArea::whereIn('city_id' , $data['filter_cities'])->get();
            }else{
                $data['selected_areas'] = [];
            }

            $data['records'] = $this->getData($data['filter_cities'],$data['areas'],$data['schedule'],$data['status'],$data['salesman'],$data['document_code'],[]);
            $data['search_applied'] = TRUE;
        }

        $data['users']  = User::where('user_entry_status',1)->get();
        $data['cities'] = TblDefiCity::where('city_entry_status' , 1)->get();
        $data['order_status'] = TblDefiOrderStatus::where('order_status_entry_status' , 1)->get();
        return view('e_services.update_status.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request , $id = null)
    {   
        $data = [];
        $validator = Validator::make($request->all(), [
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(count($request->pd) <= 0){
                return $this->jsonErrorResponse($data, 'Please Select Entries to Update Status.', 200);
            }
            if(isset($id)){
                $updateStatus = TblServUpdateStatus::where('update_status_id' , $id);
                $updateStatus = $updateStatus->first(['update_status_code','update_status_id']);
                $updateStatus_id = $updateStatus->update_status_id;
                $updateStatus_code = $updateStatus->update_status_code;
                $updateStatus_date = date('Y-m-d' , strtotime($request->schedule_date));
                $updateStatus_notes = $request->notes;
                TblServUpdateStatus::where('update_status_id' , $id)->delete();
            }else{
                $updateStatus_id = Utilities::uuid();
                $updateStatus_code = $this->documentCode(TblServUpdateStatus::max('update_status_code'),'STU');
                $updateStatus_date = date('Y-m-d' , strtotime($request->schedule_date));
                $updateStatus_notes = $request->notes;
            }
            foreach ($request->pd as $pd) {
                if(isset($pd['checkRow']) && $pd['checkRow'] == 'on'){
                    $updateStatus = new TblServUpdateStatus();
                    $updateStatus->update_status_id = $updateStatus_id;
                    $updateStatus->update_status_code = $updateStatus_code;
                    $updateStatus->update_status_code_type = 'STU';
                    $updateStatus->quotation_id = $pd['sales_quotation_id'];
                    $updateStatus->order_id = $pd['sales_order_id'];
                    $updateStatus->status_id = $pd['order_status'];
                    $updateStatus->schedule_id = $pd['schedule_id'];
                    $updateStatus->status_date = $updateStatus_date;
                    $updateStatus->notes = $updateStatus_notes;
                    $updateStatus->business_id = auth()->user()->business_id;
                    $updateStatus->company_id = auth()->user()->company_id;
                    $updateStatus->branch_id = auth()->user()->branch_id;
                    $updateStatus->update_status_user_id = auth()->user()->id;
                    $updateStatus->save();
                    //Updating the Order Status in Sales Table
                    TblSaleSalesOrder::where('sales_order_id' , $pd['sales_order_id'])->update([
                        'sales_order_status' => $pd['order_status']
                    ]);
                    TblSaleSalesOrder::where('sales_order_id' , $pd['sales_quotation_id'])->update([
                        'sales_order_status' => $pd['order_status']
                    ]);
                }
            }
            $form_id = $updateStatus_id;
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

        if(TblServUpdateStatus::where('update_status_id',$updateStatus_id)->count() <= 0){
            return $this->jsonErrorResponse($data, 'Please Select Entries to Update.', 200);
        }

        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            // $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage;
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

    public function getData($cities,$areas,$schedule,$status,$salesman,$code,$ids){
        $data = [];$index = -1;
        
        $rQuotations = TblSaleSalesOrder::with('dtls','expense','customer','sales_contract','sale_booking','city','area','status','quotation','statusServiceSchedule')
        ->where('sales_order_code_type','or')
        ->FilterCities($cities)
        ->FilterAreas($areas)
        ->FilterSchedule($schedule)
        ->FilterStatus($status)
        ->FilterSalesMan($salesman)
        ->FilterOrderIds($ids)
        ->orderBy('sales_order_code' , 'ASC')
        ->get();
        //If Quotations are Empty
        if(!count($rQuotations) > 0){
            return $data;
        }
        foreach ($rQuotations as $value) {
            $dataArr = []; 
            $dataArr['city_id'] = $value->city_id;
            $dataArr['city'] = isset($value->city) ? $value->city->city_name : "";
            $dataArr['area_id'] = $value->area_id;
            $dataArr['area'] = isset($value->area) ? $value->area->area_name : "";
            $dataArr['customer_id'] = $value->customer_id;
            $dataArr['customer_name'] = $value->customer->customer_name;
            $dataArr['phone_no'] = $value->sales_order_mobile_no;
            $dataArr['sales_order_id'] = $value->sales_order_id;
            $dataArr['sales_quotation_id'] = isset($value->quotation->sales_order_id) ? $value->quotation->sales_order_id : "";
            $dataArr['request_date'] = isset($value->quotation->sales_order_date) ? date('m/d/Y' , strtotime($value->quotation->sales_order_date)) : "";
            $dataArr['request_no'] = isset($value->quotation->sales_order_code) ? $value->quotation->sales_order_code : "";
            $dataArr['order_date'] = date('m/d/Y' , strtotime($value->sales_order_date));
            $dataArr['order_id'] = $value->sales_order_id;
            $dataArr['order_no'] = $value->sales_order_code;
            $dataArr['quoted_amount'] = isset($value->quotation->net_total) ? $value->quotation->net_total : "";
            $dataArr['actual_amount'] = $value->net_total;;
            $dataArr['status'] = $value->status->order_status_names;
            $dataArr['status_id'] = $value->status->order_status_id;

            $dataArr['schedule_id'] = isset($value->statusServiceSchedule->schedule_id) ? $value->statusServiceSchedule->schedule_id : '';
            $dataArr['schedule_status'] = isset($value->statusServiceSchedule->schedule_status) ? $value->statusServiceSchedule->schedule_status : 0;
            $dataArr['schedule_salesman_id'] = isset($value->statusServiceSchedule->schedule_assign_to) ? $value->statusServiceSchedule->schedule_assign_to : '';
            $dataArr['schedule_salesman'] = isset($value->statusServiceSchedule->user->name) ? $value->statusServiceSchedule->user->name : '';
            $dataArr['schedule_dtl_time'] = isset($value->statusServiceSchedule->schedule_dtl_schedule_time) ? $value->statusServiceSchedule->schedule_dtl_schedule_time : '';
            $dataArr['schedule_dt_date'] = isset($value->statusServiceSchedule->schedule_dtl_schedule_date) ? date('m/d/Y' , strtotime($value->statusServiceSchedule->schedule_dtl_schedule_date)) : '';
            $dataArr['schedule_code'] = isset($value->statusServiceSchedule->schedule_code) ? $value->statusServiceSchedule->schedule_code : '';

            $dataArr['for_update'] = 0;
            

            array_push($data , $dataArr);
        }
        return $data;
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
            // return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
            $saleOrder = TblServUpdateStatus::where('update_status_id',$id)->first();
            $saleOrder->delete();

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
