<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblProductionConsumption;
use App\Library\Utilities;
use App\Models\Defi\TblDefiConstants;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class ProductionConsumptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Production & Consumption';
    public static $menu_dtl_id = '336';
    public static $redirect_url = 'production-consumption';


    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['form_type'] = 'production-consumption';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage . self::$redirect_url;
        $data['page_data']['create'] = '/' . self::$redirect_url . $this->prefixCreatePage;

        if (isset($id)) {
            if (TblProductionConsumption::where('code', 'LIKE', $id)->exists()) {
                $data['permission'] = self::$menu_dtl_id . '-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblProductionConsumption::where('code', $id)->first();
                $data['document_code'] = $data['current']->code;
            } else {
                abort(404);
            }
        } else {
            $data['permission'] = self::$menu_dtl_id . '-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'    => 'branch',
                'model'       => 'TblProductionConsumption',
                'code_field'  => 'code',
                'code_prefix' => strtoupper('pc'),
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }

        $arr = [
            'biz_type'   => 'branch',
            'code'       => $data['document_code'],
            'link'       => $data['page_data']['create'],
            'table_name' => 'tblproductionconsumption',
            'col_id'     => 'code',
            'col_code'   => 'code',
        ];

        return view('inventory.production_consumption.form', compact('data'));
    }


}
