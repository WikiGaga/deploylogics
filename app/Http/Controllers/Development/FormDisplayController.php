<?php

namespace App\Http\Controllers\Development;

use App\Http\Controllers\Controller;
use App\Models\TblSoftFormCases;
use App\Models\TblSoftMenuDtl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;




class FormDisplayController extends Controller
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
       // $data ['menu']    = TblSoftMenuDtl::all();
        $data ['formcases']    = TblSoftFormCases::all();
        return view('development.formdisplay.add', compact('data'));
    }

    public function FormDisplayData($formtble)
    {
        $columns['casecolumns'] = TblSoftFormCases::select('form_cases_column_name','form_cases_heading','form_cases_orderby')->where('form_cases_table_name',$formtble)->first();
        //dd($columns['casecolumns']->toArray());
        $columns['orderby'] = explode(',',$columns['casecolumns']['form_cases_orderby']);
        $columns['dbcolumn'] = explode(',',$columns['casecolumns']['form_cases_column_name']);
        $columns['dbheading'] = explode(',',$columns['casecolumns']['form_cases_heading']);
        $columns['column'] = Schema::getColumnListing($formtble);


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
    public function update(Request $request)
    {
        
        $collection = collect($request->formDisplay);
        $sorted = $collection->sortBy('orderby');
        $column_name = "";
        $heading = "";
        $sort = "";
        foreach ($sorted as $d){
            $column_name .= $d['column'].",";
            $heading .= ucfirst($d['heading']).",";
            $sort .= isset($d['sort'])?$d['sort'].",":'';
        }
        $column_name = rtrim($column_name,',');
        $column_name = strtolower($column_name);
        $heading = rtrim($heading,',');
        $sort = rtrim($sort,',');
        $sort = strtolower($sort);
        try{
            $formcases = TblSoftFormCases::where('form_cases_table_name',$request->menu_flow_criteria_name)->first();

            $formcases->form_cases_column_name = $column_name;
            $formcases->form_cases_heading = $heading;
            $formcases->form_cases_orderby = $sort;
            $formcases->form_cases_user_id = auth()->user()->id;
            $formcases->save();

            return $this->returnJsonSucccess('Form Case successfully updated.',200);

        }catch(\Exception $e){
            return $this->returnJsonError($e->getMessage(),201);
        }
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
