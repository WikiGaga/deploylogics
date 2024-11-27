<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblDefiBrochure;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblDefiCurrency;
use App\Models\TblPurcComparativeQuotation;
use App\Models\TblPurcComparativeQuotationDtl;
use App\Models\TblPurcLpo;
use App\Models\TblPurcLpoDtl;
use App\Models\TblPurcGrn;
use App\Models\TblPurcProductBarcode;
use App\Models\TblDefiBrochureDtl;
use App\Models\TblSoftBranch;
use App\Models\TblSoftUserPageSetting;
use App\Models\User;
use App\Models\ViewPurcLpoDetail;
use Dompdf\Dompdf;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PDF;

class BrochureController extends Controller
{
    public static $page_title = 'Brochure';
    public static $redirect_url = 'brochure';
    public static $menu_dtl_id = '192';
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
        $data['form_type'] = 'brochure';
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage . self::$redirect_url;;
        $data['page_data']['create'] = '/' . self::$redirect_url . $this->prefixCreatePage;
        if (isset($id)) {
            if (TblDefiBrochure::where('brochure_id', 'LIKE', $id)->where(Utilities::currentBCB())->exists()) {
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id . '-edit';
                $data['id'] = $id;
                $data['current'] = TblDefiBrochure::with('brochures_dtl')->where('brochure_id', $id)->where(Utilities::currentBCB())->first();
                // dd($data['current']);
                $data['page_data']['print'] = '/' . self::$redirect_url . '/print/' . $id;
            } else {
                abort('404');
            }
        } else {
            $data['permission'] = self::$menu_dtl_id . '-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['branches'] = TblSoftBranch::get();
        return view('setting.brochure.form', compact('data'));
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
        if (isset($request->pd)) {
            foreach ($request->pd as $pd) {
                if (!empty($pd['barcode'])) {
                    $exits = TblPurcProductBarcode::where('product_barcode_barcode', $pd['barcode'])->exists();
                    if (!$exits) {
                        return $this->jsonErrorResponse($data, trans('message.not_barcode'), 200);
                    }
                }
            }
            if(count($request->pd) > 9){
                return $this->jsonErrorResponse($data, "Please Enter Only 9 Products.", 200);
            }
        } else {
            return $this->jsonErrorResponse($data, 'Fill The Grid', 200);
        }
        $validator = Validator::make($request->all(), [
            'brochure_name' => 'required',
            'branch_profile' => 'mimes:png,jpg,jpeg|max:2048',
            'background_image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        DB::beginTransaction();
        try {
            if (isset($id)) {
                $brochure = TblDefiBrochure::where('brochure_id', $id)->where(Utilities::currentBCB())->first();
            } else {
                $brochure = new TblDefiBrochure();
                $brochure->brochure_id = Utilities::uuid();
            }

            if($request->file('branch_profile') != "" || $request->file('branch_profile') != null){
                $branch_logo = $request->file('branch_profile');
                $branch_logo = time().'.'.$request->file('branch_profile')->extension();
                $request->file('branch_profile')->move(public_path('uploads'), $branch_logo);

                $brochure->branch_logo = $branch_logo;
            }

            if($request->file('background_image') != "" || $request->file('background_image') != null){
                $background_image = $request->file('background_image');
                $background_image = time().'.'.$request->file('background_image')->extension();
                $request->file('background_image')->move(public_path('uploads'), $background_image);

                $brochure->background_image = $background_image;
            }

            if(isset($request->bro_branches)){
                $bro_branches = implode("," , $request->bro_branches);

                $brochure->branches = $bro_branches;
            }


            $form_id = $brochure->brochure_id;
            $brochure->brochure_date = date('Y-m-d', strtotime($request->brochure_date));;
            $brochure->brochure_name = $request->brochure_name;
            $brochure->header_heading = $request->header_heading ?? "";
            $brochure->brochure_entry_status = "1";
            $brochure->start_date = date('Y-m-d' , strtotime($request->start_date));
            $brochure->end_date = date('Y-m-d' , strtotime($request->end_date));
            $brochure->background_type = $request->bg_type;
            $brochure->background_color = $request->bg_color ?? "";
            $brochure->business_id = auth()->user()->business_id;
            $brochure->company_id = auth()->user()->company_id;
            $brochure->branch_id = auth()->user()->branch_id;
            $brochure->user_id = auth()->user()->id;
            $brochure->save();

            if (isset($id)) {
                $del_Dtls = TblDefiBrochureDtl::where('brochure_id', $id)->where(Utilities::currentBCB())->get();
                foreach ($del_Dtls as $del_Dtl) {
                    TblDefiBrochureDtl::where('brochure_dtl_id', $del_Dtl->brochure_dtl_id)->where(Utilities::currentBCB())->delete();
                }
            }
            if (isset($request->pd)) {
                $sr_no = 1;
                foreach ($request->pd as $pd) {
                    $dtl = new TblDefiBrochureDtl();
                    if (isset($pd['brochure_dtl_id'])) {
                        $dtl->brochure_dtl_id = $pd['brochure_dtl_id'];
                        $dtl->brochure_id = $id;
                    } else {
                        $dtl->brochure_dtl_id = Utilities::uuid();
                        $dtl->brochure_id = $brochure->brochure_id;
                    }
                    $dtl->sr_no = $sr_no++;
                    $dtl->brochure_dtl_barcode = isset($pd['pd_barcode']) ? $pd['pd_barcode'] : "";
                    $dtl->product_id = $pd['product_id'];
                    $dtl->product_barcode_id = isset($pd['product_barcode_id']) ? $pd['product_barcode_id'] : "";
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->brochure_dtl_packing = isset($pd['pd_packing']) ? $pd['pd_packing'] : "";
                    $dtl->brochure_dtl_remarks = isset($pd['remarks']) ? $pd['remarks'] : "";
                    $dtl->brochure_dtl_qty = $this->addNo($pd['quantity']);
                    $dtl->brochure_dtl_rate = $this->addNo($pd['rate']);
                    $dtl->brochure_dtl_amount = $this->addNo($pd['amount']);
                    $dtl->brochure_dtl_disc_percent = $this->addNo($pd['dis_perc']);
                    $dtl->brochure_dtl_disc_amount = $this->addNo($pd['dis_amount']);
                    $dtl->brochure_dtl_vat_perc = $this->addNo($pd['vat_perc']);
                    $dtl->brochure_dtl_vat_amount = $this->addNo($pd['vat_amount']);
                    $dtl->brochure_dtl_gross_amount = $this->addNo($pd['gross_amount']);
                    $dtl->brochure_dtl_bg_color = isset($pd['bg_color']) ? $pd['bg_color'] : "";
                    $dtl->brochure_dtl_images = isset($pd['image']) ? $pd['image'] : "";
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->user_id = auth()->user()->id;
                    $dtl->save();
                }
            }
        } catch (QueryException $e) {
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
        if (isset($id)) {
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage . self::$redirect_url;;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        } else {
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/' . self::$redirect_url . $this->prefixCreatePage . '/' . $form_id;
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
        $data['filename'] = $id;
        $data['title'] = 'BROUCHER';
        return view('setting.brochure.brochure_pdf_view', compact('data'));
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
    // print function
    public function print($id, $type = null)
    {
        $data['title'] = 'BROUCHER';
        $data['type'] = $type;
        $data['hide_date'] = 1;

        $data['permission'] = self::$menu_dtl_id . '-print';
        $data['print_link'] = '/brochure/print/' . $id . '/pdf';
        $data['branches'] = TblSoftBranch::get();
        
        if (isset($id)) {
            if (TblDefiBrochure::where('brochure_id', 'LIKE', $id)->where(Utilities::currentBCB())->exists()) {
                $data['current'] = TblDefiBrochure::with('brochures_dtl')->where('brochure_id', $id)->where(Utilities::currentBCB())->first();
            } else {
                abort('404');
            }
        }
        if (isset($type) && $type == 'pdf') {
            $data['current'] = TblDefiBrochure::with('brochures_dtl')->where('brochure_id', $id)->where(Utilities::currentBCB())->first();

            // $dompdf = PDF::loadView('prints.brochure_print', ['data'=>$data]);
            // $filename = $id.'.pdf';
            // if (! File::exists('brochure')) {
            //     File::makeDirectory('brochure', 0775, true,true);
            // }
            // $dompdf->save("brochure/".$filename);
            // $data['filename'] = $filename;
            // return view('prints.brochure_print', compact('data'));

            $view = view('prints.brochure_print', compact('data'))->render();
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set('dpi', 300);
            $options->set('isPhpEnabled', TRUE);
            // $options->setIsHtml5ParserEnabled(TRUE);
            // $options->setIsRemoteEnabled(TRUE);
            $options->setDefaultFont('roboto');
            $dompdf->setOptions($options);
            $dompdf->loadHtml($view, 'UTF-8');
            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'landscape');
            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            return $dompdf->stream();
        } else {
            return view('prints.brochure_print', compact('data'));
        }
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
        try {
                $po = TblDefiBrochure::where('brochure_id', $id)->where(Utilities::currentBCB())->first();
                $po->brochures_dtl()->delete();
                $po->delete();

        } catch (QueryException $e) {
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
