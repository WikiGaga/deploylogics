<?php

namespace App\Http\Controllers\WhatsApp;

use Exception;
use Validator;
use App\Library\Utilities;
use Illuminate\Http\Request;
use App\Models\TblWhatsAppGroup;
use App\Models\TblWhatsAppWords;
use App\Models\TblWhatsAppChannel;
use App\Models\TblWhatsAppMessage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TblWhatsAppWordsDtl;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WAWordController extends Controller
{
    public static $page_title = 'WhatsApp Word';
    public static $redirect_url = 'wa-words';
    public static $menu_dtl_id = '223';

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
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblWhatsAppWords::where('word_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblWhatsAppWords::with('dtl')->where('word_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['messages'] = TblWhatsAppMessage::where('MSG_STATUS' , 'Y')->get();
        $data['channels'] = TblWhatsAppChannel::where('CHANNEL_STATUS','YES')->get();
        $data['groups'] = TblWhatsAppGroup::where('is_active',1)->get();
        return view('whatsapp.words.form',compact('data'));
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
        if(!isset($id)){
            if(TblWhatsAppWords::where('word_id','LIKE',$request->name)->exists()){
                return $this->jsonErrorResponse($data, trans('message.duplicate_record'), 422);
            }
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $word = TblWhatsAppWords::where('word_id',$id)->first();
            }else{
                $word = new TblWhatsAppWords();
                $word->word_id = Utilities::uuid();
            }
            $form_id = $word->word_id;
            $word->word_name = $request->name;
            $word->cnt_grp_id = $request->group;
            $word->channel_id = $request->channel;
            $word->is_active = isset($request->is_active)?"YES":"NO";
            $word->business_id = auth()->user()->business_id;
            $word->company_id = auth()->user()->company_id;
            $word->branch_id = auth()->user()->branch_id;
            $word->save();

            if(isset($id)){
                TblWhatsAppWordsDtl::where('word_id' , $id)->delete();
            }

            if(isset($request->pd)){
                $sr_no = 1;
                foreach ($request->pd as $pd) {
                    $dtl = new TblWhatsAppWordsDtl();
                    $dtl->word_id_dtl = Utilities::uuid();
                    $dtl->word_id = $word->word_id;
                    $dtl->msg_id = $pd['description'];
                    $dtl->is_active = $pd['status'];
                    $dtl->sr_no = $sr_no++;
                    $dtl->save();
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
            $group =TblWhatsAppWords::where('word_id',$id)->first();
            $group->dtl()->delete();
            $group->delete();
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
