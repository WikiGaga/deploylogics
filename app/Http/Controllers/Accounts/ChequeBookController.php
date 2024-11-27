<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\TblAccoChequeBook;
use App\Models\TblAccoChequeBookDtl;
use App\Models\TblAccCoa;
use Illuminate\Http\Request;
// db and Validator
use App\Library\Utilities;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class ChequeBookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Cheque Book';
    public static $redirect_url = 'cheque-book';
    public static $menu_dtl_id = '32';
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
        $data['page_data']['path_index'] =  $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblAccoChequeBook::where('cheque_book_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblAccoChequeBook::where('cheque_book_id',$id)->where(Utilities::currentBC())->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }

        $data['acc_code']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', "6-05-02%")->where(Utilities::currentBC())->get();
        return view('accounts.cheque_book.form',compact('data'));
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
            'cheque_book_name' => 'required|max:100',
            'cheque_book_no_of_cheque' => 'required'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if($request->cheque_book_serial_from >= $request->cheque_book_serial_to){
            return $this->returnjsonerror(" Enter Cheque Book Serial To Greater Then Cheque Book Serial From",201);
        }
        if($id == null){
            $bank = TblAccoChequeBook::where('chart_account_id',$request->bank_id)->where(Utilities::currentBC())->exists();
            if($bank == true){
                $dataExist = TblAccoChequeBookDtl::whereBetween('cheque_no', [$request->cheque_book_serial_from, $request->cheque_book_serial_to])->where(Utilities::currentBC())->exists();
                if($dataExist == true){
                    return $this->returnjsonerror(" Cheque Book Serial Aleady Exit",201);
                }
            }

        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $cb = TblAccoChequeBook::where('cheque_book_id',$id)->where(Utilities::currentBC())->first();
            }else{
                $cb = new TblAccoChequeBook();
                $cb->cheque_book_id = Utilities::uuid();
            }
            $form_id = $cb->cheque_book_id;
            $cb->cheque_book_name = $request->cheque_book_name;
            $cb->chart_account_id = $request->bank_id;
            $cb->cheque_book_serial_from = $request->cheque_book_serial_from;
            $cb->cheque_book_serial_to = $request->cheque_book_serial_to;
            $cb->cheque_book_no_of_cheque = $request->cheque_book_no_of_cheque;
            $cb->cheque_book_entry_status = isset($request->cheque_book_entry_status)?"1":"0";
            $cb->business_id = auth()->user()->business_id;
            $cb->company_id = auth()->user()->company_id;
            $cb->branch_id = auth()->user()->branch_id;
            $cb->cheque_book_user_id = auth()->user()->id;
            $cb->save();

            if(isset($id)){
                $chequeNo = TblAccoChequeBookDtl::where('cheque_book_id',$id)->where(Utilities::currentBC())->first();
                $cheque_book_dtl_id = $chequeNo->cheque_book_dtl_id;
                $cheque_book_id = $id;

                $cb_dtl = TblAccoChequeBookDtl::where('cheque_book_id',$id)->where(Utilities::currentBC())->get();
                foreach ($cb_dtl as $dtl){
                    TblAccoChequeBookDtl::where('cheque_book_dtl_id',$dtl->cheque_book_dtl_id)->where(Utilities::currentBC())->delete();
                }
            }else{
                $cheque_book_dtl_id = Utilities::uuid();
                $cheque_book_id = $cb->cheque_book_id;
            }
            for($i=$request->cheque_book_serial_from; $i<=$request->cheque_book_serial_to; $i++){
                $cbDtl = new TblAccoChequeBookDtl();
                $cbDtl->cheque_book_id = $cheque_book_id;
                $cbDtl->cheque_book_dtl_id = $cheque_book_dtl_id;
                $cbDtl->cheque_no = $i;
                $cbDtl->cheque_status = $cb->cheque_book_entry_status;
                $cbDtl->cheque_book_entry_status = $cb->cheque_book_entry_status;
                $cbDtl->business_id = auth()->user()->business_id;
                $cbDtl->company_id = auth()->user()->company_id;
                $cbDtl->branch_id = auth()->user()->branch_id;
                $cbDtl->cheque_book_user_id = auth()->user()->id;
                $cbDtl->save();
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
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;
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

            $cb = TblAccoChequeBook::where('cheque_book_id',$id)->where(Utilities::currentBC())->first();
            $cb->dtls()->delete();
            $cb->delete();

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
