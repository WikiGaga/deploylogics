<?php

namespace App\Http\Controllers\Common;

use Image;
use Exception;
use Validator;
use App\Library\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Database\QueryException;
use App\Models\Defi\TblSaleBankDistribution;
use App\Models\Defi\TblDefiDocumentUpload;
use App\Models\TblSoftMenuDtl;
use Illuminate\Validation\ValidationException;
use App\Models\Defi\TblDefiDocumentUploadFiles;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as FaceResponse;

class DocumentsUploadController extends Controller
{
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
    public function create(Request $request)
    {
        // dd($request->toArray());
        $data = [];
        $data['document_types'] = [];
        $data['current'] = [];
        $data['can_upload'] = true;
        if(isset($request->form_id) && !empty($request->form_id) && isset($request->form_type) && !empty($request->form_type)){
            $data['current'] = TblDefiDocumentUpload::with('files')
                                ->where('document_upload_form_id',$request->form_id)
                                ->where('document_upload_form_type',$request->form_type)
                                ->where(Utilities::currentBCB())->orderBy('sr_no')->get();
            // dd($data['current']);
            $data['form_code'] = $request->form_code;
            $data['form_id'] = $request->form_id;
            $data['form_type'] = $request->form_type;
            $data['menu_id'] = isset($request->menu_id)?$request->menu_id:"";
            $data['can_upload'] = $this->checkFormPermission($request->form_type, $request->menu_id);
        //    dd($data['current']->toArray());
        }
        return view('common.upload_document',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'form_id' => 'required',
            'form_type' => 'required',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        if (!$this->checkFormPermission($request->form_type, $request->menu_id)) {
            return $this->jsonErrorResponse($data, 'You do not have permission to upload documents for this form.', 200);
        }
        DB::beginTransaction();
        try{
            $duExists = TblDefiDocumentUpload::where('document_upload_form_id',$request->form_id)->where('document_upload_form_type',$request->form_type)->exists();
            if($duExists){
                $du = TblDefiDocumentUpload::where('document_upload_form_id',$request->form_id)->where('document_upload_form_type',$request->form_type)->delete();
            }
            foreach ($request->document_list as $document) {
                $du = new TblDefiDocumentUpload();
                $du->document_upload_id = Utilities::uuid();
                $du_count = TblDefiDocumentUpload::where('document_upload_form_id',$request->form_id)->where('document_upload_form_type',$request->form_type)->count();
                $du->sr_no = (int)$du_count + 1;
                $du->document_upload_form_id = $request->form_id;
                $du->document_upload_form_type = $request->form_type;
                $du->menu_id = $request->menu_id;
                $du->document_upload_name = $document['doc_name'];
                $du->document_upload_remarks = $document['remarks'];
                $du->document_refrence_number = isset($document['reference_num']) ? $document['reference_num'] : '';
                $du->document_place_of_issue = isset($document['place_of_issue']) ? $document['place_of_issue'] : '';
                $du->document_date_of_issue = isset($document['issue_date']) ? date('Y-m-d', strtotime($document['issue_date'])) : '';
                $du->document_date_of_expiry = isset($document['expiry_date']) ? date('Y-m-d', strtotime($document['expiry_date'])) : '';
                $du->document_upload_entry_status = 1;
                $du->business_id = auth()->user()->business_id;
                $du->company_id = auth()->user()->company_id;
                $du->branch_id = auth()->user()->branch_id;
                $du->document_upload_user_id = auth()->user()->id;
                $du->save();

                if(isset($document['files'])){
                    foreach ($document['files'] as $file) {
                        $duf = new TblDefiDocumentUploadFiles();
                        $duf->document_upload_files_id = Utilities::uuid();
                        $duf->document_upload_id = $du->document_upload_id;
                        $duf->document_upload_files_name = $file;
                        $duf->document_upload_files_path = isset($file)?$file:'';
                        $duf->document_upload_files_size = random_int(10000, 99999);
                        $duf->business_id = auth()->user()->business_id;
                        $duf->company_id = auth()->user()->company_id;
                        $duf->branch_id = auth()->user()->branch_id;
                        $duf->document_upload_files_user_id = auth()->user()->id;
                        $duf->save();
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
        return $this->jsonSuccessResponse($data, 'Upload files successfully', 200);
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

            $fileRecord = TblDefiDocumentUploadFiles::where('document_upload_files_id', $id)->first();

            if($fileRecord){
                $filePath = public_path('/user_documents/' . $fileRecord->document_upload_files_name);
                if(File::exists($filePath)){
                    File::delete($filePath);
                }
                $fileRecord->delete();
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
        return $this->jsonSuccessResponse($data, 'File deleted successfully.', 200);
    }

    public function destroyByFilename(Request $request)
    {
        $data = [];
        try{
            $filename = $request->filename;
            if($filename){
                $filePath = public_path('/user_documents/' . $filename);
                if(File::exists($filePath)){
                    File::delete($filePath);
                    return $this->jsonSuccessResponse($data, 'File deleted successfully.', 200);
                }
            }
            return $this->jsonErrorResponse($data, 'File not found.', 200);
        }catch (Exception $e) {
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
    }

    public function uploadDocumentAttachment(Request $request){

        $validator = Validator::make($request->all() , [
            'file' => 'required|max:1000',
        ]);

        if($validator->fails()){
            return FaceResponse::make($validator->errors()->first(), 400);
        }else{
            $folder = '/user_documents/';
            $publicFolder = public_path($folder);
            if (! File::exists($publicFolder)) {
                File::makeDirectory($publicFolder, 0775, true, true);
            }
            $all_files_list = [];
            foreach ($request->file('file') as $file){
                $uuid_image = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 16);
                $filename = $uuid_image . '.' . $file->getClientOriginalExtension();
                if($file->getClientOriginalExtension() == 'pdf'){
                    $path = public_path('/user_documents');
                    $upload_success = $file->move($path, $filename);
                }else{
                    $path = public_path($folder . $filename);
                    $upload_success = Image::make($file->getRealPath())->save($path);
                }
                if( $upload_success ) {
                    $all_files_list[] = $filename;
                }
            }
            if( count($all_files_list) > 0 ) {
                return $all_files_list;
            } else {
                return 'failed';
            }
        }
    }
    public function verifyDocument(Request $request){
        $validator = Validator::make($request->all() , [
            'document_id' => 'required',
        ]);
        if($validator->fails()){
            return FaceResponse::make($validator->errors()->first(), 200);
        }
        $data = [];
        DB::beginTransaction();
        try{
            $current = TblSaleBankDistribution::where('bd_id',$request->document_id)->first();
            $current->document_verified_status = $request->document_verified_status;
            $current->save();

        }catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();

        return $this->jsonSuccessResponse($data, 'Document verified successfully.', 200);
    }
    public function verifyDocumentView(Request $request){

        $validator = Validator::make($request->all() , [
            'document_id' => 'required',
        ]);
        if($validator->fails()){
            return FaceResponse::make($validator->errors()->first(), 200);
        }
        $data = [];
        $data['current'] = TblDefiDocumentUpload::with('files')->where('document_upload_form_id',$request->document_id)->first();



        return view('upload_docs.verify_docs',compact('data'));
    }

    public function checkFormPermission($formType = null, $menuId = null)
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        // Admin / Super Admin bypass
        if ($user->id == 1 || $user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('administrator')) {
            return true;
        }

        // 1. Determine menu_dtl_id from menuId or fallback
        $menuDtlId = null;

        if (!empty($menuId) && is_numeric($menuId)) {
            $menuDtlId = (int)$menuId;
        }

        if (empty($menuDtlId) && !empty($formType)) {
            if (is_numeric($formType)) {
                $menuDtlId = (int)$formType;
            } else {
                $menuDtl = TblSoftMenuDtl::where('menu_dtl_id', $formType)
                    ->orWhere('menu_dtl_name', $formType)
                    ->orWhere('menu_dtl_name', 'LIKE', $formType)
                    ->orWhere('menu_dtl_link', 'LIKE', '%' . $formType . '%')
                    ->first();
                if ($menuDtl) {
                    $menuDtlId = $menuDtl->menu_dtl_id;
                }
            }
        }

        if (empty($menuDtlId) && !empty($formType)) {
            $mappings = [
                'grn' => 23,
                'grn-register' => 23,
                'purchase-order' => 38,
                'sales-invoice' => 60,
                'pos-sales-invoice' => 60,
                'stock-audit' => 267,
            ];
            $lowerFormType = strtolower($formType);
            if (isset($mappings[$lowerFormType])) {
                $menuDtlId = $mappings[$lowerFormType];
            }
        }

        // 2. Check permission for menu_dtl_id and actions 'edit', 'upload', 'create'
        if (!empty($menuDtlId)) {
            $targetActions = ['edit', 'upload', 'create'];

            foreach ($targetActions as $action) {
                $permName = $menuDtlId . '-' . $action;
                if ($user->isAbleTo($permName) || $user->can($permName)) {
                    return true;
                }
            }

            $hasMenuPerm = DB::table('permission_user')
                ->join('permissions', 'permissions.id', '=', 'permission_user.permission_id')
                ->where('permission_user.user_id', $user->id)
                ->where('permissions.menu_dtl_id', $menuDtlId)
                ->whereIn('permissions.display_name', $targetActions)
                ->exists();

            if ($hasMenuPerm) {
                return true;
            }

            $hasRoleMenuPerm = DB::table('role_user')
                ->join('permission_role', 'permission_role.role_id', '=', 'role_user.role_id')
                ->join('permissions', 'permissions.id', '=', 'permission_role.permission_id')
                ->where('role_user.user_id', $user->id)
                ->where('permissions.menu_dtl_id', $menuDtlId)
                ->whereIn('permissions.display_name', $targetActions)
                ->exists();

            if ($hasRoleMenuPerm) {
                return true;
            }

            // If permissions exist for this menu_dtl_id in DB, but user has no edit/upload/create action
            $menuHasPermissions = DB::table('permissions')->where('menu_dtl_id', $menuDtlId)->exists();
            if ($menuHasPermissions) {
                return false;
            }
        }

        return true;
    }
}
