<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblAccoVoucher;
use App\Models\TblAccCoa;
use App\Models\TblAccoVoucherBillDtl;
use App\Models\TblDefiBank;
use App\Models\TblDefiCurrency;
use App\Models\TblDefiWHT;
use App\Models\User;
use App\Models\ViewAccoVoucherListing;
use App\Models\ViewPurcGrnPayments;
use Illuminate\Http\Request;
// db and Validator
use App\Library\Utilities;
use App\Models\Rent\TblRentAgreementDtl;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use TheUmar98\BarcodeBundle\Utils\QrCode;


class VoucherListController extends Controller
{

    public function pvList(Request $request)
    {
        $data = [];
        $data['title'] = 'Vendor Payment Voucher';
        $data['path-form'] = '/accounts/pv/form';
        $data['menu_dtl_id'] = 270;
        if($request->ajax()){
            $tbl_1 = " tbl_1";
            $table = " vw_acco_voucher_listing $tbl_1 ";
            $columns = "$tbl_1.voucher_id, $tbl_1.voucher_no, $tbl_1.voucher_date, $tbl_1.amount, $tbl_1.debit_account_name, $tbl_1.credit_account_name, $tbl_1.voucher_notes, $tbl_1.user_name, $tbl_1.created_at";

            $where = " where $tbl_1.business_id = ".auth()->user()->business_id;
            $where .= " and $tbl_1.branch_id = ".auth()->user()->branch_id;
            $where .= " and lower($tbl_1.voucher_type) = 'pv' ";

            $today = date('d/m/Y');
            $time_from = '12:00:00 am';
            $time_to = '11:59:59 pm';
            $global_filter_bollean = false;
            if (isset($request['query']['globalFilters'])) {
                $globalFilters = $request['query']['globalFilters'];
                $global_search = false;
                if(isset($globalFilters['global_search']) && !empty($globalFilters['global_search'])){
                    $generalSearch = str_replace(" " , "%" , $globalFilters['global_search']);
                    $where .= " and ( ";
                    $where .= " $tbl_1.voucher_no like '%$generalSearch%' OR ";
                    $where .= " $tbl_1.amount like '%$generalSearch%' OR ";
                    $where .= " $tbl_1.debit_account_name like '%$generalSearch%' OR ";
                    $where .= " $tbl_1.credit_account_name like '%$generalSearch%' OR ";
                    $where .= " $tbl_1.voucher_notes like '%$generalSearch%' OR ";
                    $where .= " $tbl_1.user_name like '%$generalSearch%' ";
                    $where .= " ) ";

                    $from = "TO_DATE('01/01/2010 ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                    $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                    $where .=  ' AND ('.$tbl_1.'.created_at between '. $from .' AND '. $to.') ';

                    $global_search = true;
                    $global_filter_bollean = true;
                }

                if(isset($globalFilters['date']) && $global_search == false){
                    $date = $globalFilters['date'];
                    if(!empty($date)){
                        if(isset($globalFilters['time_from'])){
                            $time_from = date('h:i:s a',strtotime($globalFilters['time_from']));
                        }
                        if(isset($globalFilters['time_to'])){
                            $time_to = date('h:i:s a',strtotime($globalFilters['time_to']));
                        }
                        if($date == 'today'){
                            $from = "TO_DATE('".$today." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                        }
                        $yesterday = date('d/m/Y',strtotime(date('d-m-Y').' -1 day'));
                        if($date == 'yesterday'){
                            $from = "TO_DATE('".$yesterday." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                        }
                        $l7days = date('d/m/Y',strtotime(date('d-m-Y').' -7 day'));
                        if($date == 'last_7_days'){
                            $from = "TO_DATE('".$l7days." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                        }
                        $l30days = date('d/m/Y',strtotime(date('d-m-Y').' -30 day'));
                        if($date == 'last_30_days'){
                            $from = "TO_DATE('".$l30days." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                        }
                        if($date == 'yesterday'){
                            $to = "TO_DATE('".$yesterday." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                        }else{
                            $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                        }
                        if($date == 'custom_date'){
                            if(isset($globalFilters['from']) && isset($globalFilters['to'])){
                                $from = "TO_DATE('".date('d/m/Y',strtotime($globalFilters['from']))." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                                $to = "TO_DATE('".date('d/m/Y',strtotime($globalFilters['to']))." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                            }else{
                                $from = "TO_DATE('".$today." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                                $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                            }
                        }
                        if($date == 'all'){
                            $from = "TO_DATE('01/01/2010 ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                            $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                        }
                        $where .=  ' AND (tbl_1.created_at between '. $from .' AND '. $to.') ';
                    }
                    $global_filter_bollean = true;
                }

                if(isset($globalFilters['inline'])){
                    $inline_filter = $globalFilters['inline'];
                    $inline_where = "";
                    if(!empty($inline_filter)){
                        if(isset($inline_filter['voucher_no']) && !empty($inline_filter['voucher_no'])){
                            $inline_where .= " and $tbl_1.voucher_no like '%".$inline_filter['voucher_no']."%'";
                        }
                        if(isset($inline_filter['voucher_date']) && !empty($inline_filter['voucher_date'])){
                            $voucher_date = date('d/m/Y',strtotime($inline_filter['voucher_date']));
                            $d_from = "TO_DATE('".$voucher_date." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                            $d_to = "TO_DATE('".$voucher_date." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                            $inline_to_date = "$d_from and $d_to";
                            $inline_where .= " and ( $tbl_1.voucher_date between ".$inline_to_date.") ";
                        }
                        if(isset($inline_filter['amount']) && !empty($inline_filter['amount'])){
                            $inline_where .= " and $tbl_1.amount = '".$inline_filter['amount']."'";
                        }
                        if(isset($inline_filter['debit_account_name']) && !empty($inline_filter['debit_account_name'])){
                            $inline_where .= " and $tbl_1.debit_account_name = '".$inline_filter['debit_account_name']."'";
                        }
                        if(isset($inline_filter['credit_account_name']) && !empty($inline_filter['credit_account_name'])){
                            $inline_where .= " and $tbl_1.credit_account_name = '".$inline_filter['credit_account_name']."'";
                        }
                        if(isset($inline_filter['voucher_notes']) && !empty($inline_filter['voucher_notes'])){
                            $inline_where .= " and $tbl_1.voucher_notes like '%".$inline_filter['voucher_notes']."%'";
                        }
                        if(isset($inline_filter['user_name']) && !empty($inline_filter['user_name'])){
                            $inline_where .= " and $tbl_1.user_name like '%".$inline_filter['user_name']."%'";
                        }
                        if(isset($inline_filter['created_at']) && !empty($inline_filter['created_at'])){
                            $created_at = date('d/m/Y',strtotime($inline_filter['created_at']));
                            $d_from = "TO_DATE('".$created_at." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                            $d_to = "TO_DATE('".$created_at." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                            $inline_to_date = "$d_from and $d_to";
                            $inline_where .= " and ( $tbl_1.created_at between ".$inline_to_date.") ";
                        }
                    }
                    $where .= $inline_where;
                }
            }
            if(!$global_filter_bollean){
                $from = "TO_DATE('".$today." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                $where .=  ' AND ('.$tbl_1.'.created_at between '. $from .' AND '. $to.') ';
            }


            $sortDirection  = ($request->has('sort.sort') && $request->filled('sort.sort'))? $request->input('sort.sort') : 'desc';
            $sortField  = ($request->has('sort.field') && $request->filled('sort.field'))? $request->input('sort.field') : 'created_at';
            $meta    = [];
            $page  = ($request->has('pagination.page') && $request->filled('pagination.page'))? $request->input('pagination.page') : 1;
            $perpage  = ($request->has('pagination.perpage') && $request->filled('pagination.perpage'))? $request->input('pagination.perpage') : -1;

            $total  = DB::selectOne("select count(*) count from $table $where");
            $total  = isset($total->count)?$total->count:0;
            // $perpage 0; get all data
            if ($perpage > 0) {
                $pages  = ceil($total / $perpage); // calculate total pages
                $page   = max($page, 1); // get 1 page when $_REQUEST['page'] <= 0
                $page   = min($page, $pages); // get last page when $_REQUEST['page'] > $totalPages
                $offset = ($page - 1) * $perpage;
                if ($offset < 0) {
                    $offset = 0;
                }

                //$data = array_slice($data, $offset, $perpage, true);
            }

            $orderby = " ORDER BY $tbl_1.$sortField $sortDirection ";
            $limit = "OFFSET $offset ROWS FETCH NEXT $perpage ROWS ONLY";
            $qry = "select $columns from $table $where $orderby $limit";
            //     dd($qry);
            $entries = DB::select($qry);

            $meta = [
                'page'    => $page,
                'pages'   => $pages,
                'perpage' => $perpage,
                'total'   => $total
            ];

            $result = [
                'meta' => $meta + [
                        'sort'  => $sortDirection,
                        'field' => $sortField,
                    ],
                'data' => $entries,
            ];
            return response()->json($result);
        }

        return view('accounts.voucher_list.pv',compact('data'));
    }

}
