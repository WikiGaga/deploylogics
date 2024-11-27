<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblSaleSalesOrder;
use App\Models\TblSaleSalesOrderDtl;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class CustomerSalesOrderController extends Controller
{
    public static $page_title = 'Customer Sales Order';
    public static $redirect_url = 'customer-sales-order';
    public static $menu_dtl_id = '142';
    public static $type = 'cso';
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
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblSaleSalesOrder::where('sales_order_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblSaleSalesOrder::with('dtls')->where('sales_order_id',$id)->where(Utilities::currentBC())->first();
                $data['document_code'] = $data['current']->sales_order_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'business',
                'model'             => 'TblSaleSalesOrder',
                'code_field'        => 'sales_order_code',
                'code_prefix'       => strtoupper(self::$type),
                'code_type_field'   => 'sales_order_code_type',
                'code_type'         => self::$type

            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }
        $arr = [
            'biz_type' => 'business',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_sale_sales_order',
            'col_id' => 'sales_order_id',
            'col_code' => 'sales_order_code',
            'code_type_field'   => 'sales_order_code_type',
            'code_type'         => self::$type,
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('sales.customer_sales_order.form',compact('data'));
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
            'logged_customer' => 'required',
            'pd.*.product_id' => 'nullable|numeric',
            'pd.*.product_barcode_id' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $sales_order = TblSaleSalesOrder::where('sales_order_id',$id)->where(Utilities::currentBC())->first();
            }else{
                $sales_order = new TblSaleSalesOrder();
                $sales_order->sales_order_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'business',
                    'model'             => 'TblSaleSalesOrder',
                    'code_field'        => 'sales_order_code',
                    'code_prefix'       => strtoupper(self::$type),
                    'code_type_field'   => 'sales_order_code_type',
                    'code_type'         => self::$type

                ];
                $sales_order->sales_order_code = Utilities::documentCode($doc_data);
            }
            $form_id = $sales_order->sales_order_id;
            $sales_order->sales_order_date = date('Y-m-d', strtotime($request->sales_order_date));;
            $sales_order->customer_id = auth()->user()->id;
            $sales_order->sales_order_remarks = $request->sales_order_remarks;
            $sales_order->sales_order_entry_status = 1;
            $sales_order->business_id = auth()->user()->business_id;
            $sales_order->company_id = auth()->user()->company_id;
            $sales_order->branch_id = auth()->user()->branch_id;
            $sales_order->sales_order_user_id = auth()->user()->id;
            $sales_order->sales_order_code_type = self::$type;
            $sales_order->save();

            if(isset($request->pd)){
                TblSaleSalesOrderDtl::where('sales_order_id',$id)->where(Utilities::currentBC())->delete();
                foreach($request->pd as $pd){
                    $dtl = new TblSaleSalesOrderDtl();
                    if(isset($id) && isset($pd['sales_order_dtl_id']) && $pd['sales_order_dtl_id'] != 'undefined' ){
                        $dtl->sales_order_dtl_id = $pd['sales_order_dtl_id'];
                        $dtl->sales_order_id = $id;
                    }else{
                        $dtl->sales_order_dtl_id = Utilities::uuid();
                        $dtl->sales_order_id = $sales_order->sales_order_id;
                    }
                    $dtl->sales_order_dtl_barcode = $pd['pd_barcode'];
                    $dtl->product_id = $pd['product_id'];
                    $dtl->product_barcode_id = $pd['product_barcode_id'];
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->sales_order_dtl_packing = $this->addNo(isset($pd['pd_packing'])?$pd['pd_packing']:0);
                    $dtl->sales_order_dtl_quantity = $this->addNo(isset($pd['quantity'])?$pd['quantity']:0);
                    $dtl->sales_order_dtl_rate = $this->addNo(isset($pd['rate'])?$pd['rate']:0);
                    $dtl->sales_order_dtl_amount = $this->addNo(isset($pd['amount'])?$pd['amount']:0);
                    $dtl->sales_order_dtl_disc_per = $this->addNo(isset($pd['dis_perc'])?$pd['dis_perc']:0);
                    $dtl->sales_order_dtl_disc_amount = $this->addNo(isset($pd['dis_amount'])?$pd['dis_amount']:0);
                    $dtl->sales_order_dtl_vat_per = $this->addNo(isset($pd['vat_perc'])?$pd['vat_perc']:0);
                    $dtl->sales_order_dtl_vat_amount = $this->addNo(isset($pd['vat_amount'])?$pd['vat_amount']:0);
                    $dtl->sales_order_dtl_total_amount = $this->addNo(isset($pd['gross_amount'])?$pd['gross_amount']:0);
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->sales_order_dtl_user_id = auth()->user()->id;
                    $dtl->save();
                }
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
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;;
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

            $cso = TblSaleSalesOrder::with('dtls')->where('sales_order_id',$id)->where(Utilities::currentBC())->first();
            $cso->dtls()->delete();
            $cso->delete();

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

    public function print($id)
    {
        $data['title'] = self::$page_title;
        $data['permission'] = self::$menu_dtl_id.'-print';
        if(isset($id)){
            if(TblSaleSalesOrder::where('sales_order_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                $data['current'] = TblSaleSalesOrder::with('dtls')->where('sales_order_id',$id)->where(Utilities::currentBC())->first();
            }else{
                abort('404');
            }
        }
        return view('prints.customer_sales_order_print',compact('data'));
    }
}
