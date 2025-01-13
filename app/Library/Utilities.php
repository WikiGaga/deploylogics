<?php
/**
 * User: M.Umar
 * Date: 05/Aug/2016
 * Time: 04:22 PM
 */
namespace App\Library;

use App\Http\Controllers\Controller;
use App\Models\TblDefiConfigBranches;
use App\Models\TblDefiConfiguration;
use App\Models\TblDefiShortcutKeys;
use App\Models\TblPurcProductBarcode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Content;

class Utilities
{
    public static function newForm(){
        $data = [];
        $data['type'] = 'new';
        $data['action'] = 'Save';
        $data['action_id'] = 1;
        $data['back_btn_classes'] = 'check_value';
        return $data;
    }
    public static function editForm(){
        $data = [];
        $data['type'] = 'edit';
        $data['action'] = 'Update';
        $data['action_id'] = 2;
        return $data;
    }
    public static function viewForm(){
        $data = [];
        $data['type'] = 'view';
        $data['action'] = '';
        $data['action_id'] = 3;
        return $data;
    }
    public static function returnJsonNewForm(){
        $data = [];
        $data['form'] = 'new';
        return $data;
    }
    public static function returnJsonEditForm(){
        $data = [];
        $data['form'] = 'edit';
        return $data;
    }
    public static function returnJsonImportForm(){
        $data = [];
        $data['form'] = 'import';
        return $data;
    }

    public static function uuid()
    {
        $length = 6;
        $x = '';
        for($i = 1; $i <= $length; $i++){
            $x .= random_int(0,255);
        }
        $random = substr($x, 0, $length);
        $year  = date("y", time());
        $day  = date("d", time());
        $hours  = date("H", time());
        $second  = date("s", time());

        $uuid = $random.$year.$day.$hours.$second;
        return $uuid;
    }
    public static function NumFormat($num)
    {
        $num1 = str_replace( ',', '', $num);
        $num2 = (Float)$num1;
        return number_format($num2,3,'.','');
    }
    public static function documentCode($doc_data){
        $biz_type = $doc_data['biz_type'];
        $model = $doc_data['model'];
        $code_field = $doc_data['code_field'];
        $code_prefix = $doc_data['code_prefix'];
        $business_id = isset($doc_data['business_id'])?$doc_data['business_id']:auth()->user()->business_id;
        $company_id = isset($doc_data['company_id'])?$doc_data['company_id']:auth()->user()->company_id;
        $branch_id = isset($doc_data['branch_id'])?$doc_data['branch_id']:auth()->user()->branch_id;
//dd($business_id.','.$company_id.','.$branch_id);
        if(isset($doc_data['code_type_field']) && isset($doc_data['code_type'])){
            $code_type_field = $doc_data['code_type_field'];
            $code_type = $doc_data['code_type'];
        }
        $modelN = 'App\Models\\'.$model;
        if(isset($code_type_field) && isset($code_type)){
            $max = $modelN::where($code_type_field,$code_type)
                ->where('business_id',$business_id);
        }else{
            if($model == "TblSaleCustomer"){
                $max = $modelN::where('business_id',$business_id)->orderBy('created_at' , 'desc')->where('customer_code' , 'LIKE' , "%{$code_prefix}%" );
            }else{
                $max = $modelN::where('business_id',$business_id);
            }
        }
        $max = $max->where('company_id',$company_id);
        if($biz_type == 'branch'){
            $max = $max->where('branch_id',$branch_id);
        }
//        dd($max);
        $max = $max->max($code_field);
//        dd($max);
        // max = "SP-0000000", type = "SP"
        if(!empty($max)){
            $max = explode('-',$max);
            $max = end($max);
            $max = $max+1;
        }else{
            $max = 1;
        }
        $new_code =  sprintf("%'07d", $max); // return 12 to 0000012
        $code = strtoupper($code_prefix)."-".$new_code; // return "SP-0000012"
        return $code; // return "SP-0000012"
    }

    public static function customCustomerCode($doc_data){
        $biz_type = $doc_data['biz_type'];
        $model = $doc_data['model'];
        $code_field = $doc_data['code_field'];
        $code_prefix = $doc_data['code_prefix'];
        $business_id = isset($doc_data['business_id'])?$doc_data['business_id']:auth()->user()->business_id;
        $company_id = isset($doc_data['company_id'])?$doc_data['company_id']:auth()->user()->company_id;
        $branch_id = isset($doc_data['branch_id'])?$doc_data['branch_id']:auth()->user()->branch_id;

        if(isset($doc_data['code_type_field']) && isset($doc_data['code_type'])){
            $code_type_field = $doc_data['code_type_field'];
            $code_type = $doc_data['code_type'];
        }

        $modelN = 'App\Models\\'.$model;
        if(isset($code_type_field) && isset($code_type)){
            $max = $modelN::where($code_type_field,$code_type)
                ->where('business_id',$business_id)->orderBy('created_at', 'desc');
        }else{
            $max = $modelN::where('business_id',$business_id)->orderBy('created_at' , 'desc');
        }
        $max = $max->where('company_id',$company_id);
        if($biz_type == 'branch'){
            $max = $max->where('branch_id',$branch_id);
        }

        $max = $max->value($code_field);

        // max = "SP-0000000", type = "SP"
        if(!empty($max)){
            $max = explode('-',$max);
            $code_prefix = $max[0];
            $max = end($max);
            $max = (int)$max;
            $max = $max+1;
        }else{
            $max = 1;
        }
        // $new_code =  sprintf("%'07d", $max); // return 12 to 0000012
        if($max >= 1000){
            $code_prefix++;
            $max = 1;
        }
        $code = strtoupper($code_prefix)."-".$max; // return "A-1000"
        return $code; // return "SP-0000012"
    }

    public static function Singular($word)
    {
        $singular = array (
            '/(quiz)zes$/i' => '\1',
            '/(matr)ices$/i' => '\1ix',
            '/(vert|ind)ices$/i' => '\1ex',
            '/^(ox)en/i' => '\1',
            '/(alias|status)es$/i' => '\1',
            '/([octop|vir])i$/i' => '\1us',
            '/(cris|ax|test)es$/i' => '\1is',
            '/(shoe)s$/i' => '\1',
            '/(o)es$/i' => '\1',
            '/(bus)es$/i' => '\1',
            '/([m|l])ice$/i' => '\1ouse',
            '/(x|ch|ss|sh)es$/i' => '\1',
            '/(m)ovies$/i' => '\1ovie',
            '/(s)eries$/i' => '\1eries',
            '/([^aeiouy]|qu)ies$/i' => '\1y',
            '/([lr])ves$/i' => '\1f',
            '/(tive)s$/i' => '\1',
            '/(hive)s$/i' => '\1',
            '/([^f])ves$/i' => '\1fe',
            '/(^analy)ses$/i' => '\1sis',
            '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
            '/([ti])a$/i' => '\1um',
            '/(n)ews$/i' => '\1ews',
            '/s$/i' => '',
        );

        $uncountable = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep');

        $irregular = array(
            'person' => 'people',
            'man' => 'men',
            'child' => 'children',
            'sex' => 'sexes',
            'move' => 'moves');

        $lowercased_word = strtolower($word);
        foreach ($uncountable as $_uncountable){
            if(substr($lowercased_word,(-1*strlen($_uncountable))) == $_uncountable){
                return $word;
            }
        }

        foreach ($irregular as $_plural=> $_singular){
            if (preg_match('/('.$_singular.')$/i', $word, $arr)) {
                return preg_replace('/('.$_singular.')$/i', substr($arr[0],0,1).substr($_plural,1), $word);
            }
        }

        foreach ($singular as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }
    public static function isFileExists($filePath)
    {
        return is_file($filePath) && file_exists($filePath);
    }


    public static function getAllBranches($id=null)
    {
        $data  = DB::table('tbl_soft_user_branch')
            ->join('tbl_soft_branch', 'tbl_soft_branch.branch_id','=','tbl_soft_user_branch.branch_id')
            ->where('user_id', isset($id)?$id:Auth()->user()->id)
            ->select('tbl_soft_user_branch.*','tbl_soft_branch.branch_name','tbl_soft_branch.branch_short_name')
            ->get();
        return $data;
    }
    public static function getApplyBranches($id=null)
    {
        $data  = DB::table('tbl_soft_user_branch')
            ->join('tbl_soft_branch', 'tbl_soft_branch.branch_id','=','tbl_soft_user_branch.branch_id')
            ->where('user_id', isset($id)?$id:Auth()->user()->id)
            ->where('tbl_soft_branch.branch_id','!=', '9')
            ->select('tbl_soft_user_branch.*','tbl_soft_branch.branch_name','tbl_soft_branch.branch_short_name')
            ->get();
        return $data;
    }
    public static function getDefaultBranches($id=null)
    {
        $default_branch  = DB::table('tbl_soft_user_branch')
            ->where('user_id', isset($id)?$id:Auth()->user()->id)
            ->where('default_branch', 1)
            ->first();
        return $default_branch;
    }
    public static function getDefaultStoreOfBranch()
    {
        $default_store  = DB::table('tbl_defi_store')
            ->where('branch_id', Auth()->user()->branch_id)
            ->where('store_default_value', 1)
            ->first();
        return $default_store;
    }
    public static function getOptionalBranches($id=null)
    {
        $optional_branch  = DB::table('tbl_soft_user_branch')
            ->where('user_id', isset($id)?$id:Auth()->user()->id)
            ->where('default_branch', 0)
            ->get();
        return $optional_branch;
    }

    /**
     * UOMList
     *
     * @param [type] $product_id
     * @return array
     */
    public static function UOMList($product_id){
        $barcodes = TblPurcProductBarcode::with('uom')->where('product_id',$product_id)->get();
        $uom_list = [];
        foreach ($barcodes as $barcode){
            array_push($uom_list,$barcode['uom'] );
        }
        return $uom_list;
    }

    public static function getModelFromTable($table)
    {
        $classes = (new static)->getClassesList(app_path('Models'));
        dd($classes);
        foreach ($classes as $class){
            $model = new $class->classname;
            if ($model->getTable() === $table){
            dd($model->getTable());
                return $class->classname;
            }
        }
        return false;
    }

    public static function getClassesList($dir)
    {
        $classes = \File::allFiles($dir);
        foreach ($classes as $class) {
            $class->classname = str_replace(
                [app_path(), '/', '.php'],
                ['App', '\\', ''],
                $class->getRealPath()
            );
        }
        return $classes;
    }


    public static function addSession($str)
    {
        // dd("addSession: ".$str);
        /*$dataSession = (object)[
            'saveBtn' => 'alt+s',
            'createBtn' => 'alt+c',
            'backBtn' => 'alt+b',
            'discount_chart_account_id' => 12345678900000,
            'income_chart_account_id' => 13815320131017,
            'vat_payable_chart_account_id' => 12345678900002,
            'stock_chart_account_id' => 12715520050649,
        ];*/


       // dump("user_branch: ".\Illuminate\Support\Facades\Session::get('user_branch'));
        // dd(\Illuminate\Support\Facades\Session::get('user_branch'));
        if(!session()->has('user_branch')){
            Auth::logout();
            return redirect('/login');
        }
        Session::forget('dataSession');
        //session branch to bussiness convert 11-feb-2021(7:25pm)
        $config = TblDefiConfiguration::first();
        $short_keys = TblDefiShortcutKeys::first();
        $configBranch = TblDefiConfigBranches::where(Utilities::currentBC())->where('acc_branch_id',auth()->user()->branch_id)->first();
        $dataSession = [];
        if(!empty($short_keys) && !empty($config)){
            $dataSession = (object)[
                'saveBtn' => $short_keys->shortcut_keys_form_save,
                'createBtn' => $short_keys->shortcut_keys_form_create,
                'backBtn' => $short_keys->shortcut_keys_form_back,
                'customer_group' => $config->customer_group,
                'sale_income' => $config->sale_income,
                'sale_discount' => $config->sale_discount,
                'sale_vat_payable' => $config->sale_vat_payable,
                'sale_stock' => $config->sale_stock,
                'sale_stock_consumption' => $config->sale_stock_consumption,
                'supplier_group' => $config->supplier_group,
                'purchase_stock' => $config->purchase_stock,
                'purchase_discount' => $config->purchase_discount,
                'purchase_vat' => $config->purchase_vat,
                'bank_group' => $config->bank_group,
                'cash_group' => $config->cash_group,
                'sale_cash_ac' => $config->sale_cash_ac,
                'sale_return_customer_group' => $config->sale_return_customer_group,
                'sale_return_income' => $config->sale_return_income,
                'sale_return_discount' => $config->sale_return_discount,
                'sale_return_vat_payable' => $config->sale_return_vat_payable,
                'sale_return_stock' => $config->sale_return_stock,
                'sale_return_stock_consumption' => $config->sale_return_stock_consumption,
                'sale_return_cash_ac' => $config->sale_return_cash_ac,
                'sale_fee_income' => $config->sale_fee_income,
                'sale_fee_discount' => $config->sale_fee_discount,
                'sale_fee_vat_payable' => $config->sale_fee_vat_payable,
                'sale_fee_stock' => $config->sale_fee_stock,
                'sale_fee_stock_consumption' => $config->sale_fee_stock_consumption,
                'sale_fee_cash_ac' => $config->sale_fee_cash_ac,
                'payment_receive_dr_ac' => $config->payment_receive_dr_ac,
                'payment_receive_cr_ac' => $config->payment_receive_cr_ac,
                'general_cash_ac' => $config->general_cash_ac,
                'excess_cash_ac' => $config->excess_cash_ac,
                'bank_distribution_cr_ac' => $config->bank_distribution_cr_ac,
                'display_rent_fee_income' => $config->display_rent_fee_income,
                'display_rent_fee_discount' => $config->display_rent_fee_discount,
                'display_rent_fee_vat_payable' => $config->display_rent_fee_vat_payable,
                'display_rent_fee_stock' => $config->display_rent_fee_stock,
                'display_rent_fee_stock_consumption' => $config->display_rent_fee_stock_consumption,
                'display_rent_fee_cash_ac' => $config->display_rent_fee_cash_ac,
                'rebate_invoice_income' => $config->rebate_invoice_income,
                'rebate_invoice_discount' => $config->rebate_invoice_discount,
                'rebate_invoice_vat_payable' => $config->rebate_invoice_vat_payable,
                'rebate_invoice_stock' => $config->rebate_invoice_stock,
                'rebate_invoice_stock_consumption' => $config->rebate_invoice_stock_consumption,
                'rebate_invoice_cash_ac' => $config->rebate_invoice_cash_ac,
            ];
        }
        if(!empty($configBranch)){
            $dataSession->stock_transfer_income = $configBranch->stock_transfer_income;
            $dataSession->stock_transfer_stock = $configBranch->stock_transfer_stock;
            $dataSession->stock_transfer_branch = $configBranch->stock_transfer_branch;
            $dataSession->stock_transfer_cash = $configBranch->stock_transfer_cash;
            $dataSession->stock_transfer_vat = $configBranch->stock_transfer_vat;
            $dataSession->stock_transfer_discount = $configBranch->stock_transfer_discount;
            $dataSession->store_receive_stock = $configBranch->store_receive_stock;
            $dataSession->stock_receive_cash = $configBranch->stock_receive_cash;
            $dataSession->stock_receive_branch = $configBranch->stock_receive_branch;
            $dataSession->stock_receive_vat = $configBranch->stock_receive_vat;
            $dataSession->stock_receive_discount = $configBranch->stock_receive_discount;
        }
        session(['dataSession' => $dataSession]);
       // $this->getDataSession = Session::get('dataSession');
    }

    public static function getDatabaseUsername(){
        $databaseName = Config::get('database.connections');

       return $databaseName['mysql']['username'];
    }

    public static function currentBCB(){
         // current Business Company Branch
        // ->where(Utilities::currentBCB())
       $data = [
           ['business_id',auth()->user()->business_id],
           ['company_id',auth()->user()->company_id],
           ['branch_id',auth()->user()->branch_id]
       ];
       return $data;
    }
    public static function currentBC(){
        // current Business Company
        // where(Utilities::currentBC())
       $data = [
           ['business_id',auth()->user()->business_id],
           ['company_id',auth()->user()->company_id]
       ];
       return $data;
    }

    public static function currentB(){
        // current Branch Company
        // where(Utilities::currentB())
       $data = [
            ['branch_id',auth()->user()->branch_id]
       ];
       return $data;
    }

    public static function getStaticPrefix($menu_id){
        $data['path'] = Route::getCurrentRequest()->route()->getCompiled()->getStaticPrefix();
        $data['view'] = ($data['path'] == '/'.$menu_id.'/view')?true:false;
        return $data;
    }

    public static function getReferer($request,$id){
        $ref = $request->headers->get('referer');
        $origin = $request->headers->get('origin');
        $ref = str_replace($origin,'',$ref);
        $referer = str_replace('/'.$id,'',$ref);
        return $referer;
    }

    public static function userLoggedSession($id,$status){
        $user = User::where('id', $id)->first();
        if($status == 1){
            $user->last_session = 1;
        }else{
            $user->last_session = 0;
        }
        $user->save();
    }

    //convert no's into words
    public static function AmountWords($number,$curreny = 'Only'){
        $number = sprintf("%.3f", $number);
       $res = Self::convertNumberToWords($number,$curreny);
        if (strpos($number, '.') == false) {
            $res ='Rupees '.$res.' '.$curreny;
        }else{
            $fraction = explode('.', $number);
            if($fraction[1] < 1){
                $res ='Rupees '.$res.' '.$curreny;
            }else{
                $res ='Rupees '.$res.' '.$curreny;
            }
        }
        return $res;
    }
    public static function convertNumberToWords($number,$curreny = 'Only') {
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' ' . $curreny . ' and ';
        $decimalEnd     = ' PAISA';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'fourty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convertNumberToWords only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative .  Self::convertNumberToWords(abs($number));
        }

        $string = $fraction = null;
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 1:
                $string = $dictionary[$number];
                break;
            case $number < 21:
                $string .= $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction .  Self::convertNumberToWords($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string =  Self::convertNumberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .=  Self::convertNumberToWords($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            if($fraction > 0){
                $numbr = (int)$fraction;
                $string .= $decimal.Self::convertNumberToWords($numbr).$decimalEnd ;
            }
        }
        $res = str_replace("-"," ",$string);
        return ucwords($res);
    }

    public static function convert_base64_to_image( $base64_code, $path, $image_name = null )
    {
        if ( !empty($base64_code) && !empty($path) ) {
            // split the string to get extension and remove not required part
            // $string_pieces[0] = to get image extension
            // $string_pieces[1] = actual string to convert into image
            $string_pieces = explode( ";base64,", $base64_code);

            /*@ Get type of image ex. png, jpg, etc. */
            // $image_type[1] will return type
            $image_type_pieces = explode( "image/", $string_pieces[0] );

            $image_type = $image_type_pieces[1];

            /*@ Create full path with image name and extension */
            $img_name = md5(uniqid()).'.'.$image_type;
            $store_at = $path.$img_name;

            /*@ If image name available then use that  */
            if ( !empty($image_name) ) {
                $store_at = $path.$image_name.'.'.$image_type;
            }
            $path = public_path($store_at);
            $decoded_string = base64_decode( $string_pieces[1] );
            // dd($store_at);
            file_put_contents( $path, $decoded_string );

            return $img_name;
        }

    }
    // Physical Stock is Store Stock Here
    public static function SuggestedQty1($maxLimit = null , $physicalStock = null){
        if(is_null($maxLimit)) $maxLimit = 0;
        if(is_null($physicalStock)) $physicalStock = 0;

        if($physicalStock < 0 && $physicalStock < $maxLimit) return 0;

        $qty = $maxLimit - abs($physicalStock); // Store Stocl
        return $qty;
    }

    public static function SuggestedQty2($consumptionDays = null , $physicalStock = null, $product_id = null,$branch_id){
        if(is_null($consumptionDays)) $consumptionDays = 0;
        if(is_null($physicalStock)) $physicalStock = 0;
        if(is_null($product_id)) $barcode_id = 0;

        $fromdate   = date('Y-m-d' , strtotime("-$consumptionDays day" , time()));
        $todate     = date('Y-m-d' , time());

        $qry = "SELECT SUM(SALES_DTL_QUANTITY) qty from vw_sale_sales_invoice
                where branch_id = " . $branch_id . "
                and sales_type IN ('SI','POS')
                AND product_id = $product_id
                AND SALES_DATE between to_date ('".$fromdate."', 'yyyy/mm/dd') AND to_date ('".$todate."', 'yyyy/mm/dd')";

        $consumptionQty = DB::selectOne($qry);

         $retQry = "SELECT SUM(SALES_DTL_QUANTITY) qty from vw_sale_sales_invoice
                where branch_id = " . $branch_id . "
                and sales_type IN ('SR','RPOS')
                AND product_id = $product_id
                AND SALES_DATE between to_date ('".$fromdate."', 'yyyy/mm/dd') AND to_date ('".$todate."', 'yyyy/mm/dd')";

        $consumptionReturnQty = DB::selectOne($retQry);

         $invStockQry = "SELECT SUM(STOCK_DTL_QUANTITY) qty from VW_INVE_STOCK
                where branch_id = " . $branch_id . "
                and lower(STOCK_CODE_TYPE) IN ('st')
                AND product_id = $product_id
                AND STOCK_DATE between to_date ('".$fromdate."', 'yyyy/mm/dd') AND to_date ('".$todate."', 'yyyy/mm/dd')";

        $consumptionInvStockQry = DB::selectOne($invStockQry);

        $consumptionQty = isset($consumptionQty->qty)?$consumptionQty->qty:0;
        $consumptionReturnQty = isset($consumptionReturnQty->qty)?$consumptionReturnQty->qty:0;
        $consumptionInvStockQry = isset($consumptionInvStockQry->qty)?$consumptionInvStockQry->qty:0;

        $qty = (($consumptionQty + $consumptionInvStockQry ) - $consumptionReturnQty) - abs($physicalStock);

        return $qty;
    }

    public static function lopQty($arr){
        $product_barcode_id = $arr['product_barcode_id'];
        $branch_id = $arr['branch_id'];

        $lopqty_Qry1 = "select sum(pod.PURCHASE_ORDER_DTLQUANTITY) qty from tbl_purc_purchase_order po
                join tbl_purc_purchase_order_dtl pod on pod.PURCHASE_ORDER_ID = po.PURCHASE_ORDER_ID
                where pod.PRODUCT_BARCODE_ID = $product_barcode_id and po.branch_id = $branch_id";

        $lopqty_1 = DB::selectOne($lopqty_Qry1);

        $lopqty_Qry2 = "select sum(grnd.TBL_PURC_GRN_DTL_QUANTITY) qty from tbl_purc_grn grn
                            join tbl_purc_grn_dtl grnd on grnd.GRN_ID = grn.GRN_ID
                            where grn.grn_type = 'GRN' and  grnd.PRODUCT_BARCODE_ID = $product_barcode_id and grn.branch_id = $branch_id and grn.PURCHASE_ORDER_ID IS NOT NULL";

        $lopqty_2 = DB::selectOne($lopqty_Qry2);

        $lopqty1 = (isset($lopqty_1->qty) && !empty($lopqty_1->qty))?$lopqty_1->qty:0;
        $lopqty2 = (isset($lopqty_2->qty) && !empty($lopqty_2->qty))?$lopqty_2->qty:0;
        $lpo = (float)$lopqty1 - (float)$lopqty2;

        return $lpo;
    }

    public static function purcRetWaitingQty($arr){
        $product_barcode_id = $arr['product_barcode_id'];
        $branch_id = $arr['branch_id'];


        $waitingQty_Qry1 = "select sum(grnd.TBL_PURC_GRN_DTL_QUANTITY) qty from tbl_purc_grn grn
                            join tbl_purc_grn_dtl grnd on grnd.GRN_ID = grn.GRN_ID
                            where grn.grn_type = 'PRT' and grnd.PRODUCT_BARCODE_ID = $product_barcode_id and grn.branch_id = $branch_id";

        $waitingQty_1 = DB::selectOne($waitingQty_Qry1);

        $waitingQty_Qry2 = "select sum(grnd.TBL_PURC_GRN_DTL_QUANTITY) qty from tbl_purc_grn grn
                            join tbl_purc_grn_dtl grnd on grnd.GRN_ID = grn.GRN_ID
                            where grn.grn_type = 'PR' and grnd.PRODUCT_BARCODE_ID = $product_barcode_id and grn.branch_id = $branch_id and grn.PURCHASE_ORDER_ID != null";

        $waitingQty_2 = DB::selectOne($waitingQty_Qry2);

        $waitingQty1 = (isset($waitingQty_1->qty) && !empty($waitingQty_1->qty))?$waitingQty_1->qty:0;
        $waitingQty2 = (isset($waitingQty_2->qty) && !empty($waitingQty_2->qty))?$waitingQty_2->qty:0;

        $waitingQty = (float)$waitingQty1 - (float)$waitingQty2;

        return $waitingQty;
    }
}
