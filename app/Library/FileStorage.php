<?php

namespace App\Library;


use App\Models\TblFileUpload;
use Image;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class FileStorage
{

    public static function fileUpload($fileData,$files)
    {

        if($files->hasFile('myFiles')) {
            $image = $files->file('myFiles');
            for($i=0;$i<sizeof($image);$i++){
                $filename = time().$i. '.' . $image[$i]->getClientOriginalExtension();
                $path = public_path('documents-upload/' . $filename);
                $file_upload_name = substr($image[$i]->getClientOriginalName(), 0, strpos($image[$i]->getClientOriginalName(), "."));
                Image::make(file_get_contents($image[$i]->getRealPath()))->save($path);
                $fu = new TblFileUpload();
                $fu->file_upload_id = Utilities::uuid();
                $fu->file_upload_name = $file_upload_name;
                $fu->documet_type_id = $fileData['document_type_id'];
                $fu->menu_dtl_id = $fileData['menu_dtl_id'];
                $fu->document_form_id = $fileData['form_id'];
                $fu->file_upload_path = $filename;
                $fu->save();
            }
        }




        /*$data = [];
        try{

        }catch (Exception $e) {
            DB::rollback();
            $message = $e->getMessage();
            $statusCode = 200;
            return response()->json(['status'=>'error', 'data'=>$data, 'message'=>$message], $statusCode);
        }*/

    }

}
