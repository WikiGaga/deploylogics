<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblSoftUserActivityLog;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Browser;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function openModal(Request $request)
    {
        $data = [];
        $data['prefix_url'] = $request->prefix_url;
        $data['title'] = $request->title;
        $where = [
            'document_id' => $request->document_id,
            'document_name' => $request->document_name,
        ];
        $data['current'] = TblSoftUserActivityLog::with('user','branch')->where($where)->orderBy('created_at','desc')->get();

        return view('prints_log.log_popup',compact('data'));
    }


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
       //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function store($menu_id,$action,$document_id,$document_type)
    {
        $data = [];
        DB::beginTransaction();
        try{
            $log = new TblSoftUserActivityLog();
            $log->user_activity_log_id = Utilities::uuid();
            $log->menu_dtl_id = $menu_id;
            $log->user_activity_log_activity_type = $action;
            $log->browser_id = Browser::browserName();
            $host= gethostname();
            $log->ip_address = gethostbyname($host);
            $log->document_id = $document_id;
            $log->document_name = $document_type;
            $log->business_id = auth()->user()->business_id;
            $log->company_id = auth()->user()->company_id;
            $log->branch_id = auth()->user()->branch_id;
            $log->user_id = auth()->user()->id;
            $log->save();

        }catch (QueryException $e) {
            DB::rollback();
            return response()->json(['status'=>'success', 'data'=>$data, 'message'=>$e->getMessage()],200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['status'=>'success', 'data'=>$data, 'message'=>$e->getMessage()],200);
        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json(['status'=>'success', 'data'=>$data, 'message'=>$e->getMessage()],200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status'=>'success', 'data'=>$data, 'message'=>$e->getMessage()],200);
        }
        DB::commit();
        return response()->json(['status'=>'success', 'data'=>$data, 'message'=>trans('message.create')],200);
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
        //dd('kk');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       //

    }
}
