<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Defi\TblDefiSeason;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SeasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Season';
    public static $redirect_url = 'season';
    public static $menu_dtl_id = '248';

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
            if(TblDefiSeason::where('season_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblDefiSeason::where('season_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['season'] = TblDefiSeason::where('season_entry_status',1)->get();
        return view('setting.season.form',compact('data'));
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
            if(TblDefiSeason::where('season_name','LIKE',$request->name)->where('business_id', auth()->user()->business_id)->exists()){
                return $this->jsonErrorResponse($data, trans('message.duplicate_record'), 422);
            }
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $season = TblDefiSeason::where('season_id',$id)->first();
            }else{
                $season = new TblDefiSeason();
                $season->season_id = Utilities::uuid();
            }
            $form_id = $season->season_id;
            $season->season_name = $request->name;
            $season->season_entry_status = isset($request->season_entry_status)?"1":"0";
            $season->business_id = auth()->user()->business_id;
            $season->company_id = auth()->user()->company_id;
            $season->branch_id = auth()->user()->branch_id;
            $season->season_user_id = auth()->user()->id;
            $season->save();

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
        //
    }
}
