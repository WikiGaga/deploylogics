<?php

namespace App\Http\Controllers\Sales;

use Exception;
use Validator;
use Dompdf\Dompdf;
use App\Models\TblScheme;
use App\Library\Utilities;
use Illuminate\Http\Request;
use App\Models\TblSchemeSlab;
use App\Models\TblSoftBranch;
use App\Models\TblSaleSchemes;
use App\Models\TblSchemeAvail;
use App\Models\TblSchemeSlabDtl;
use App\Models\TblSchemeBranches;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SaleSchemesController extends Controller
{
    public static $page_title = 'Sale Schemes';
    public static $redirect_url = 'sale-schemes';
    public static $menu_dtl_id = '225';

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
            if(TblScheme::where('scheme_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] =  TblScheme::with(['schemeAvail' , 'schemeSlab' , 'schemeSlabDtl','schemeBranches'])->where(Utilities::currentBC())->where('scheme_id',$id)->first();
                $data['document_code'] = $data['current']->schemes_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
                // dd($data['current']);
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblScheme',
                'code_field'        => 'schemes_code',
                'code_prefix'       => strtoupper('SCH')
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }
        $data['branch'] = TblSoftBranch::where('branch_active_status' , 1)->get();
        
        return view('sales.sale_schemes.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request , $id = null)
    {   
        // dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'scheme_name' => 'required',
            'scheme_start_date' => 'required',
            'scheme_end_date' => 'required',
            'scheme_slab_data.*.sldtl' => 'required|array'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        
        // if(!isset($request->pd) || count($request->pd) == 0){
        //     return $this->jsonErrorResponse($data, trans('message.fill_the_grid'), 200);
        // }

        if(!isset($request->scheme_slab_branches)){
            return $this->jsonErrorResponse($data, "Please Select Branches!", 200);
        }

        DB::beginTransaction();
        try{
            if(isset($id)){
                $sales_scheme = TblScheme::where('scheme_id',$id)->first();
            }else{
                $sales_scheme = new TblScheme();
                $sales_scheme->scheme_id = Utilities::uuid();
                $sales_scheme->schemes_code = $this->documentCode(TblScheme::where(Utilities::currentBCB())->max('schemes_code'),'SCH');
            }
            $form_id = $sales_scheme->scheme_id;
            $sales_scheme->entry_date = date('Y-m-d', strtotime($request->scheme_date));
            $sales_scheme->scheme_name = $request->scheme_name;
            $sales_scheme->remarks = isset($request->scheme_remarks) ? $request->scheme_remarks : "";
            $sales_scheme->is_active = isset($request->is_active) ? "YES":"NO";
            $sales_scheme->start_date = date('Y-m-d', strtotime($request->scheme_start_date));
            $sales_scheme->end_date = date('Y-m-d', strtotime($request->scheme_end_date));
            $sales_scheme->business_id = auth()->user()->business_id;
            $sales_scheme->company_id = auth()->user()->company_id;
            $sales_scheme->branch_id = auth()->user()->branch_id;
            $sales_scheme->save();

            // If ID is Set Delete the Scheme Avail Data
            if(isset($id)){
                TblSchemeAvail::where('scheme_id',$id)->delete();
            }
            if(isset($request->pd)){
                foreach($request->pd as $pd){
                    $avail = new TblSchemeAvail();
                    $avail->scheme_avail_id = Utilities::uuid();
                    $avail->scheme_id = $sales_scheme->scheme_id;
                    // If User Select Product
                    if($pd['pd_scheme_type'] == 'Product'){
                        $avail->product_id = isset($pd['product_id']) ? $pd['product_id'] : 0;
                        $avail->product_barcode_id = isset($pd['product_barcode_id']) ? $pd['product_barcode_id'] : 0;
                        $avail->uom_id = isset($pd['pd_uom']) ? $pd['pd_uom'] : 0;
                    }
                    // If User Select Group
                    if($pd['pd_scheme_type'] == 'Product Group'){
                        $avail->group_item_id = $pd['pd_barcode'] ?? -1;
                        $avail->group_name = $pd['product_name'] ?? 'Product Group';
                    }

                    $avail->foc_qty = $pd['foc_qty'] ?? 0;
                    $avail->disc_perc = $pd['dis_perc'] ?? 0;
                    $avail->disc = $pd['dis_amount'] ?? 0;
                    $avail->save();
                }
            }

            // Delete The Already Assigned Branches In edit
            if(isset($id)){
                TblSchemeBranches::where('scheme_id',$id)->delete();
            }
            if(isset($request->scheme_slab_branches)){
                foreach($request->scheme_slab_branches as $branch){
                    $schemeBranch = new TblSchemeBranches();
                    $schemeBranch->scheme_branch_uuid = Utilities::uuid();
                    $schemeBranch->scheme_id = $sales_scheme->scheme_id;
                    $schemeBranch->branch_id = $branch;
                    $schemeBranch->save();
                }
            }

            // If ID is Set Delete the Scheme Slab Data
            if(isset($id)){
                TblSchemeSlab::where('scheme_id',$id)->delete();
                TblSchemeSlabDtl::where('scheme_id' , $id)->delete();
            }
            $sr_no = 0;
            if(isset($request->scheme_slab_data)){
                foreach($request->scheme_slab_data as $slab){
                    $slabs = new TblSchemeSlab();
                    $slabs->slab_id = Utilities::uuid();
                    $slabs->scheme_id = $sales_scheme->scheme_id;
                    $slabs->SLAB_NAME = $slab['slab_name'] ?? "";
                    $slabs->min_sale = isset($slab['slab_min_sale']) ? $slab['slab_min_sale'] : "";
                    $slabs->max_sale = isset($slab['slab_max_sale']) ? $slab['slab_max_sale'] : "";
                    $slabs->disc_perc = isset($slab['slab_disc_per']) ? $slab['slab_disc_per'] : "";
                    $slabs->disc = isset($slab['slab_disc']) ? $slab['slab_disc'] : 0;
                    $slabs->expiry_days = isset($slab['slab_expiry_days']) ? $slab['slab_expiry_days'] : "";
                    $slabs->expiry_date = date('Y-m-d', strtotime($slab['slab_expiry_date']));
                    $slabs->generate_coupon = isset($slab['generate_coupon']) ? "YES":"NO";
                    $slabs->sr_no = $sr_no++;
                    $slabs->save();

                    if(isset($slab['sldtl'])){
                        foreach($slab['sldtl'] as $slabdtl){
                            $dtl = new TblSchemeSlabDtl();
                            $dtl->slab_dtl_id = Utilities::uuid();
                            $dtl->slab_id = $slabs->slab_id;
                            $dtl->scheme_id = $sales_scheme->scheme_id;
                            $dtl->group_item_id = $slabdtl['pd_groupitem_id'] ?? "";
                            $dtl->product_id = $slabdtl['sldtl_product_id'] ?? "";
                            $dtl->product_barcode_id = $slabdtl['sldtl_product_barcode_id'] ?? "";
                            $dtl->disc_perc = $slabdtl['sldtl_disc_per'] ? $this->addNo($slabdtl['sldtl_disc_per']) : 0;
                            $dtl->disc = $slabdtl['sldtl_disc'] ? $this->addNo($slabdtl['sldtl_disc']) : 0;
                            $dtl->foc_qty = $slabdtl['sldtl_foc_qty'] ? $this->addNo($slabdtl['sldtl_foc_qty']) : 0;
                            $dtl->save();
                        }
                    }

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

    public function print($id,$type = null)
    {
        $data['title'] = 'Sales Scheme';
        $data['type'] = $type;
        $data['permission'] = self::$menu_dtl_id.'-print';
        $data['print_link'] = '/'.self::$redirect_url.'/print/'.$id.'/pdf';
        if(isset($id)){
            if(TblScheme::where('scheme_id',$id)->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblScheme::with('schemeAvail','schemeSlab','schemeSlabDtl')->where('scheme_id',$id)->first();
                $data['document_code'] = $data['current']->schemes_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }else{
                abort('404');
            }
        }

        $data['rate_types'] = config('constants.rate_type');
        if(isset($type) && $type=='pdf'){
            $view = view('prints.sales_schemes_print', compact('data'))->render();
            //dd($view);
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set('dpi', 100);
            $options->set('isPhpEnabled', TRUE);
            $options->set('isHtml5ParserEnabled', TRUE);
            $options->setDefaultFont('roboto');
            $dompdf->setOptions($options);
            $dompdf->loadHtml($view,'UTF-8');
            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'landscape');
            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            return $dompdf->stream();
        }else{
            return view('prints.sales_schemes_print',compact('data'));
        }
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
            $scheme = TblScheme::where('scheme_id',$id)->where(Utilities::currentBCB())->first();
            $scheme->schemeAvail()->delete();
            $scheme->schemeSlab()->delete();
            $scheme->schemeSlabDtl()->delete();
            $scheme->delete();
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
