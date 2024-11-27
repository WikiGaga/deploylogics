<?php

namespace App\Http\Controllers\Development;

use Exception;
use App\Models;
use League\Csv\Reader;
use League\Csv\Statement;
use App\Library\Utilities;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use App\Models\ViewAllColumnData;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ImportDataController extends Controller
{
    public static $page_title = 'Import Data';
    public static $redirect_url = 'importing';
    public static $menu_dtl_id = '129';

    public function index(){
        $data = [];
        $data['permission'] = self::$menu_dtl_id.'-create';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());

        // All Table Lists
        $sorted =  ViewAllColumnData::select('table_name')->groupby('table_name')->get();
        $collection = collect($sorted);
        $data['table_list'] = $collection->sortBy('table_name');

        return view('development.import_data.form' , compact('data'));
    }

    public function getModelName($table)
    {
        return Str::studly(Str::singular($table)).'()';
    }

    public function store(Request $request , $id = null){

        $validator = Validator::make($request->all(), [
            'table_name' => 'required',
            'csv_file' => 'required|file',
            'fields'   => 'required',
        ]);

        if($validator->fails()){
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }else{
            $file = $request->file('csv_file');
            $name = time().'-'.$file->getClientOriginalName();

            $destinationPath = 'uploads/csv/';
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0775, true, true);
            }
            $file->move($destinationPath,$name);

            // Uploading File
            $file = public_path($destinationPath . '/' . $name);

            $csv = Reader::createFromPath($destinationPath . '/' . $name, 'r');

            $csv->setHeaderOffset(0);
            $stmt = Statement::create();
            $records = $stmt->process($csv);
            $fields = $request->fields;

            DB::beginTransaction();
            try{
                foreach ($records as $offset => $record) {
                    $data = [];
                    $record = array_values($record);
                    foreach ($fields as $key => $value) {
                        if($value != null){
                            if(!empty($record[$key])){
                                $data[$value] = $record[$key];
                            }else{
                                $data[$value] = "";
                            } 
                        }
                    }

                    DB::table($request->table_name)->insert($data);

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
            return $this->jsonSuccessResponse($data, 'Data successfully loaded.', 200);
        }
    }

    public function getCsvOnSelect(Request $request){   

        $validator = Validator::make($request->all(), [
            'table_name' => 'required',
            'csv_file' => 'required|file',
        ]);
        if($validator->fails()){
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }else{
            $string = '';$reqCol=[];
            $path = $request->file('csv_file')->getRealPath();
            $csv = Reader::createFromPath($path , 'r');
            
            //Getting the Column
            $columns = ViewAllColumnData::where('table_name', strtoupper($request->table_name))->get()->sortBy('column_name');
            foreach ($columns as $col) {
                if($col->nullable == 'N'){
                    array_push($reqCol , $col->column_name);
                }
            }

            // Setting the header to the first Row in CSV File
            $csv->setHeaderOffset(0);
            $headers = $csv->getHeader();
            $stmt = Statement::create()
            ->limit(10);
            $records = $stmt->process($csv);

            if(count($reqCol) > 0){
                $string .= '<div class="text-sm text-danger px-3 py-2 w-100">We Required Values for:</div>';
                $string .= '<ul>';
                foreach ($reqCol as $value) {
                    $string .= '<li class="text-danger text-sm">* '.strtoupper($value).'</li>';
                }
                $string .= '</ul>';
            }
            
            $string .= '<table  class="table table-stripped table-hover table-responsive table-bordered">';
            $string .= '<thead class="thead-dark">';
            foreach($headers as $header){
                $string .= '<th>'.$header.'</th>';
            }
            $string .= '</thead>';
            
            // Insert the Top Dropdowns for the Columns
            $string .= '<tr>';
                $i = 0;
                foreach ($headers as $key => $head) { 
                    $string .= '<td>';
                        $string .= '<select class="form-control kt-select2 erp-form-control-sm" name="fields['.$i++.']">';
                            $string .= '<option value="">Select</option>';
                            foreach ($columns as $value) {
                                $string .= '<option value="'.$value->column_name.'">'.$value->column_name.'</option>';    
                            }
                        $string .= '</select>';
                    $string .= '</td>';
                }
            $string .= '</tr>';

            foreach ($records as $record) {
                $string .= '<tr>';
                    foreach($record as $column){
                        $string .= '<td>' . $column . '</td>';
                    }
                $string .= '</tr>';
            }
        }
        $string .= '</table>';
        echo $string;
    }
}
