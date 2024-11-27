<?php

namespace App\Http\Controllers\Development;

use App\Http\Controllers\Controller;
use App\Models\TblPurcWarrentyPeriod;
use App\Models\TblSoftFlow;
use App\Models\TblSoftEvent;
use App\Models\TblSoftAction;
use App\Models\TblSoftMenuDtl;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\Schema;




class FlowCriteriaController extends Controller
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
    public function create()
    {
        $data['menu']     = TblSoftMenuDtl::all();
        $data['flow']     = TblSoftFlow::where('menu_flow_entry_status',1)->get();
        $data['event']    = TblSoftEvent::where('menu_event_entry_status',1)->get();
        $data['action']   = TblSoftAction::where('menu_action_entry_status',1)->get();
        $data['length']   = count($data['action']);
        $data['warranty_period'] = TblPurcWarrentyPeriod::where('warrenty_period_entry_status',1)->get();
        return view('development.flow_criteria.add', compact('data'));
    }

    public function getAjaxData($formtble)
    {

        $columns = Schema::getColumnListing($formtble);
        return response()->json($columns);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
