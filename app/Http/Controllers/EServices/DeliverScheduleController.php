<?php

namespace App\Http\Controllers\EServices;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Validator;
use App\Models\User;
use App\Library\Utilities;
use App\Models\TblDefiCity;
use App\Models\TblSaleSalesOrder;
use App\Models\TblDefiOrderStatus;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Purchase\QuotationController;
use App\Models\TblServDeliveryChargesDtl;
use App\Models\TblServManageSchedule;
use App\Models\TblServDeliverySchedule;

use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class DeliverScheduleController extends Controller
{

    
    public static $page_title   = 'Delivery Schedule';
    public static $redirect_url = 'delivery-schedule';
    public static $menu_dtl_id  = '218';
    public static $type         = 'sch'; 
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
        $data['page_data'] = [];$tempArr = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        
        if(isset($id)){
            if(TblServManageSchedule::where('schedule_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['first_current'] = TblServManageSchedule::where('schedule_id',$id)->first();
                $data['current'] = TblServManageSchedule::with('orderDtls','user','quotation','order')->where('schedule_id',$id)->get();
                foreach($data['current'] as $order){
                    $id = $order->order_id;
                    array_push($tempArr , $id);    
                }
                $data['a_scheduled'] = $tempArr;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['document_code'] = $this->documentCode(TblServManageSchedule::max('schedule_code'),'SCH');
        }
        $data['users']  = User::where('user_entry_status',1)->get();
        $data['cities'] = TblDefiCity::where('city_entry_status' , 1)->get();
        $data['order_status'] = TblDefiOrderStatus::where('order_status_entry_status' , 1)->get();
        return view('e_services.delivery_schedule.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request , $id = null)
    {
        // dd($request->all());
        $data = [];$invalid = false;
        $validator = Validator::make($request->all(), [
            'interval' => 'required',
            'salesman' => 'required',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($request->pd)){

                if(isset($id)){
                    $schedule = TblServManageSchedule::where('schedule_id' , $id);
                    $schedule_code = $schedule->first(['schedule_code','schedule_id']);
                    $schedule_id = $schedule_code->schedule_id;
                    $schedule_code = $schedule_code->schedule_code;
                    TblServManageSchedule::where('schedule_id' , $id)->delete();
                }else{
                    $schedule_id = Utilities::uuid();
                    $schedule_code = $this->documentCode(TblServManageSchedule::max('schedule_code'),'SCH');
                }
                foreach ($request->pd as $entry) {
                    $schedule_dtl_id = Utilities::uuid();
                    if(isset($entry['checkRow']) && $entry['checkRow'] == 'on'){
                        
                        if(empty($entry['schedule_date']) || empty($entry['schedule_time'])){
                            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
                        }
                        $schedule = new TblServManageSchedule();
                        $schedule->schedule_id = $schedule_id;
                        $schedule->schedule_code = $schedule_code;
                        $schedule->schedule_code_type = self::$type;
                        $schedule->schedule_date = date('Y-m-d' , strtotime($request->schedule_date));
                        $schedule->schedule_start_time = $request->start_time;
                        $schedule->schedule_interval_minutes = $request->interval;
                        $schedule->schedule_assign_to = $entry['sales_man_id'];
                        $schedule->notes= $request->notes;
                        $schedule->request_quotation_id = $entry['sales_quotation_id'];
                        $schedule->sales_order_id = $entry['sales_order_id'];
                        $schedule->schedule_dtl_schedule_date = date('Y-m-d' , strtotime($entry['schedule_date']));
                        $schedule->schedule_dtl_schedule_time = $entry['schedule_time'];
                        $schedule->schedule_status = 1;
                        $schedule->business_id = auth()->user()->business_id;
                        $schedule->company_id = auth()->user()->company_id;
                        $schedule->branch_id = auth()->user()->branch_id;
                        $schedule->schedule_user_id = auth()->user()->id;
                        $schedule->schedule_uuid = $schedule_dtl_id;
                        $schedule->schedule_dtl_id = $schedule_dtl_id;
                        $schedule->save();

                        $form_id = $schedule->schedule_id;  
                        // Update Quotation to Scheduled
                        if(isset($entry['sales_quotation_id']) && !empty($entry['sales_quotation_id'])){
                            TblSaleSalesOrder::where('sales_order_id' , $entry['sales_quotation_id'])->update([
                                'schedule_status' => 1,
                                'assigned_sales_man' => $entry['sales_man_id'],
                                'service_order_id' => $entry['sales_order_id']
                            ]);
                        }
                        // Update Order to Scheduled
                        if(isset($entry['sales_order_id']) && !empty($entry['sales_order_id'])){
                            TblSaleSalesOrder::where('sales_order_id' , $entry['sales_order_id'])->update([
                                'schedule_status' => 1,
                                'assigned_sales_man' => $entry['sales_man_id'],
                                'sales_quotation_id' => $entry['sales_quotation_id'],
                            ]);
                        }
                    }
                }
                $form_id = $schedule_id;
            }else{
                return $this->jsonErrorResponse($data, 'Please Select Entries to Schedule.', 200);
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
        
        if(TblServManageSchedule::where('schedule_id',$schedule_id)->count() <= 0){
            return $this->jsonErrorResponse($data, 'Please Select Entries to Schedule.', 200);
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


    public function getData(Request $request){
        $data = [];$index = -1;

        $cities = isset($request->filter_cities) ? $request->filter_cities : [];
        $areas = isset($request->filter_areas) ? $request->filter_areas : [];
        $schedule = isset($request->filter_schedule) ? $request->filter_schedule : [];
        $status = isset($request->filter_status) ? $request->filter_status : [];
        $code   = isset($request->form_code) ? $request->form_code : '';
        
        $rQuotations = TblSaleSalesOrder::with('dtls','orderSchedule','quotation')
        ->where('sales_order_code_type','or')
        // ->where('sales_order_status','1') // 1 Means Pending
        ->FilterCities($cities) 
        ->FilterAreas($areas)
        ->FilterSchedule($schedule)
        ->FilterStatus($status)
        ->orderBy('sales_order_code' , 'DESC')
        ->get();

        //If Orders are Empty
        if(!count($rQuotations) > 0){
            return $this->jsonErrorResponse($data , 'All Orders are Scheduled' , 200);
        }
        $interval = (int)$request->interval;
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
            $dataArr['order_date'] = date('m/d/Y' , strtotime($value->sales_order_date));
            $dataArr['order_no' ] = $value->sales_order_code;

            $dataArr['sales_quotation_id'] = isset($value->quotation->sales_order_id) ? $value->quotation->sales_order_id : '';
            $dataArr['request_date'] = isset($value->quotation->sales_order_date) ? date('m/d/Y' , strtotime($value->quotation->sales_order_date)) : '';
            $dataArr['request_no'] = isset($value->quotation->sales_order_code) ? $value->quotation->sales_order_code : '';
            
            $dataArr['quoted_amount'] = isset($value->net_total) ? $value->net_total : "";
            $dataArr['actual_amount'] = $value->net_total;
            $dataArr['status'] = isset($value->status->order_status_names) ? $value->status->order_status_names : 'Pending';
            $dataArr['status_id'] = isset($value->status->order_status_id) ? $value->status->order_status_id : '1';

            $dataArr['schedule_status'] = isset($value->orderSchedule->schedule_status) ? $value->orderSchedule->schedule_status : 0;
            $dataArr['schedule_salesman_id'] = isset($value->orderSchedule->schedule_assign_to) ? $value->orderSchedule->schedule_assign_to : '';
            $dataArr['schedule_salesman'] = isset($value->orderSchedule->user->name) ? $value->orderSchedule->user->name : '';
            $dataArr['schedule_dtl_time'] = isset($value->orderSchedule->schedule_dtl_schedule_time) ? $value->orderSchedule->schedule_dtl_schedule_time : '';
            $dataArr['schedule_dt_date'] = isset($value->orderSchedule->schedule_dtl_schedule_date) ? date('m/d/Y' , strtotime($value->orderSchedule->schedule_dtl_schedule_date)) : '';
            $dataArr['schedule_code'] = isset($value->orderSchedule->schedule_code) ? $value->orderSchedule->schedule_code : '';

            if(!empty($dataArr['schedule_code']) && $dataArr['schedule_code'] == $code){
                $dataArr['for_update'] = 1;    
            }else{
                $dataArr['for_update'] = 0;
            }

            array_push($data , $dataArr);
            $interval = $interval + $interval;
        }
        return $this->jsonSuccessResponse($data , 'Data Successfully Loaded' , 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $data = [];
        DB::beginTransaction();
        try{
            // return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
            $saleOrder = TblServManageSchedule::where('schedule_id',$id)->first();
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
