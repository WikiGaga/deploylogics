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

        // $tables = DB::connection('oracle_live')
        //     ->table('user_tables')
        //     ->select('table_name')
        //     ->get();

        return view('development.import_data.form' , compact('data'));
    }
    

    // public function dumpTable(Request $request)
    // {
    //     // dd('f',$request->all());
    //     $tableName = $request->table_name;

    //     // 1. Clear the local table first
    //     DB::connection('oracle_local')->table($tableName)->truncate();

    //     // 2. Pull from Live and Push to Local
    //     // Start with table(), THEN orderBy(), THEN chunk()
    //     // $a= DB::connection('oracle_live')
    //     //     ->table($tableName)->get();
    //     // dd($a);
    //           DB::connection('oracle_live')
    //             ->table($tableName)
    //             ->orderBy(DB::connection('oracle_live')->getSchemaBuilder()->getColumnListing($tableName)[0])
    //             ->chunk(100, function ($rows) use ($tableName) {
    //                 $data = [];
    //                 foreach ($rows as $row) {
    //                     $array = (array) $row;
                        
    //                     // REMOVE the virtual Row Number column added by the Oracle driver
    //                     unset($array['rn']); 
    //                     unset($array['RN']); // Check for both cases
                        
    //                     $data[] = $array;
    //                 }
                    
    //                 // Now the insert will work because the columns match your table exactly
    //                 DB::connection('oracle_local')->table($tableName)->insert($data);
    //             });

    //     return back()->with('success', "Data for $tableName dumped successfully!");
    // }

     public function dumpTable(Request $request)
    {
        $tableName = $request->table_name;
        $liveConn = DB::connection('oracle_live');
        $localConn = DB::connection('oracle_local');

        // Start Transaction on Local DB
        $localConn->beginTransaction();

        try {
            // 1. Truncate local table
            $localConn->table($tableName)->truncate();

            // 2. Fetch from Live
            $liveConn->table($tableName)
                ->orderBy($liveConn->getSchemaBuilder()->getColumnListing($tableName)[0])
                ->chunk(100, function ($rows) use ($tableName, $localConn) {
                    $data = [];
                    foreach ($rows as $row) {
                        $array = (array) $row;
                        unset($array['rn'], $array['RN']); // Remove virtual row columns
                        $data[] = $array;
                    }
                    $localConn->table($tableName)->insert($data);
                });

            // If we reached here, everything is good. Save changes.
            $localConn->commit();
            return back()->with('success', "Table $tableName dumped successfully!");

        } catch (\Exception $e) {
            // Something went wrong. Undo the truncate and the partial inserts!
            $localConn->rollBack();
            return back()->with('error', "Error: " . $e->getMessage());
        }
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
