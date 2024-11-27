<?php

namespace App\Http\Controllers\PayrDepartment;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblHrAdvanceType;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class AdvanceTypeController extends Controller
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
public static $page_title = 'Advance Type';
public static $redirect_url = 'advance-type';
public static $menu_dtl_id= '113';
    public function create($id = null)
    {
        $data['page_data'] = [];
            $data['page_data']['title'] = self::$page_title;
            $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
            $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
            if(isset($id)){
                if(TblHrAdvanceType::where('advance_type_id','LIKE',$id)->exists()){
                    $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                    $data['permission'] = self::$menu_dtl_id.'-edit';
                    $data['id'] = $id;
                    $data['current'] = TblHrAdvanceType::where('advance_type_id',$id)->first();
                }else{
                    abort('404');
                }
            }else{
                $data['permission'] = self::$menu_dtl_id.'-create';
                $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            }
            return view('PayrDepartment.advance_type.form',compact('data'));     
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $advance = TblHrAdvanceType::where('advance_type_id',$id)->first();
            }else{
                $advance = new TblHrAdvanceType();
                $advance->advance_type_id = Utilities::uuid();
             
            }
            $form_id = $advance->advance_type_id;
            $advance->advance_type_name = $request->name;
            $advance->advance_type_short_name = $request->short_name;
            $advance->advance_type_notes = $request->notes;
            $advance->advance_type_entry_status = isset($request->advance_type_entry_status)?"1":"0";
            $advance->business_id = auth()->user()->business_id;
            $advance->company_id = auth()->user()->company_id;
            $advance->branch_id = auth()->user()->branch_id;
            $advance->advance_type_user_id = auth()->user()->id;
            $advance->save();

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
                $advance =TblHrAdvanceType ::where('advance_type_id',$id)->first();
                $advance->delete();
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
