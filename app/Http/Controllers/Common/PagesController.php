<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\ViewRptSaleInvoice;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblDefiCurrency;
use App\Models\TblPurcPurchaseOrder;
use App\Models\TblSoftUserPageSetting;
use App\Models\ViewSaleSalesInvoice;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public static $page_title = 'Form Layout';
    public static $redirect_url = 'form-layout';
    public static $menu_dtl_id= '67';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function WebDataRocks(){

        $data = ViewRptSaleInvoice::limit(2000)->get();

        return view('pages.web_data',compact('data'));
    }
    public function girdNew($id =null){

        $data = [];
        $id = 24278122011406;
        $data['current'] = TblPurcPurchaseOrder::with('po_details','supplier','lpo','comparative_quotation')->where('purchase_order_id',$id)->where(Utilities::currentBCB())->first();


        return view('pages.grid',compact('data'));
    }
    public function BasicFormLayout(){
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['permission'] = self::$menu_dtl_id.'-create';
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        return view('pages.basic_form_layout',compact('data'));
    }

    public function TableGridTesting($id = null)
    {
        $id = 24278122011406;
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['form_type'] = 'po';
        if(isset($id)){
            if(TblPurcPurchaseOrder::where('purchase_order_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblPurcPurchaseOrder::with('po_details','supplier','lpo','comparative_quotation')->where('purchase_order_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblPurcPurchaseOrder',
                'code_field'        => 'purchase_order_code',
                'code_prefix'       => strtoupper('po')
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }
        $data['currency'] = TblDefiCurrency::where('currency_entry_status',1)->where(Utilities::currentBC())->get();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_entry_status',1)->where(Utilities::currentBC())->get();

        $data['page_sett'] = TblSoftUserPageSetting::where('user_page_setting_document_type',$data['form_type'])->where('user_page_setting_user_id',auth()->user()->id)->first();
        // dd($data['page_sett']);
        return view('pages.table_grid', compact('data'));
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
