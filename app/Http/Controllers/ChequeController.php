<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;



class ChequeController extends Controller
{
    public function create()
    {
        return view('check.create_template');
    }

    public function store(Request $request)
    {
        //     $imagePath = null;
        // if ($request->hasFile('cheque_image')) {
        //     $imagePath = $request->file('cheque_image')->store('cheques', 'public');
        // }

        $p_id= DB::table('TBL_cheque_layouts')->max('id') +1;
        $id = DB::table('TBL_cheque_layouts')->insertGetId([
            'id' => $p_id,
            'name' => $request->name,
            'width_px' => 800,
            'height_px' => 350,
            'created_at' => now(),
            'updated_at' => now(),
            'business_id' => auth()->user()->business_id,
            'company_id' => auth()->user()->company_id,
            'branch_id' => auth()->user()->branch_id,
            // 'cheque_image'=> $imagePath,
        ]);

        // Default fields
        $fields = [
            ['date', 20, 600, 150, 30],
            ['account_title', 100, 150, 300, 30],
            ['amount', 100, 550, 100, 30],
            ['amount_partision', 100, 670, 80, 30],
            ['amount_words', 150, 150, 500, 30],
        ];

        foreach ($fields as $f) {
            $p_id= DB::table('TBL_cheque_fields')->max('id') +1;
            DB::table('TBL_cheque_fields')->insert([
                'id' => $p_id,
                'layout_id' => $id,
                'field_name' => $f[0],
                'font_size' => 12,
                'top_px' => $f[1],
                'left_px' => $f[2],
                'width_px' => $f[3],
                'height_px' => $f[4],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        return redirect("Cheque_Templete/form/$id");
        // return redirect("/cheque/template/design/$id");
    }

    public function design($id)
    {
        $template = DB::table('TBL_cheque_layouts')->where('id', $id)->first();
        $fields = DB::table('TBL_cheque_fields')->where('layout_id', $id)->get();
        $font_size = DB::table('TBL_cheque_fields')->where('layout_id', $id)->value('font_size');

        return view('check.design', compact('template', 'fields','font_size'));
    }

    public function saveLayout(Request $request)
    {
        $update_array=[
            'width_px' => $request->canvas_w,
            'height_px' => $request->canvas_h,
        ];

        if ($request->hasFile('cheque_image')) {
            $imagePath = $request->file('cheque_image')->store('cheques', 'public');
            $update_array['cheque_image']= $imagePath;
        }

        DB::table('TBL_cheque_layouts')
        ->where('id', $request->template_id)
        ->update($update_array);

        $fields = json_decode($request->input('fields'), true);

        foreach ($fields as $field) {
            DB::table('TBL_cheque_fields')
                ->where('layout_id', $request->template_id)
                ->where('field_name', $field['field_name'])
                ->update([
                    'font_size' => $request->font_size,
                    'top_px' => $field['top_px'],
                    'left_px' => $field['left_px'],
                    'width_px' => $field['width_px'],
                    'height_px' => $field['height_px'],
                ]);
        }
    
        return response()->json(['success' => true]);
    }

    // public function printForm($id)
    // {
    //     $template = DB::table('TBL_cheque_layouts')->where('id', $id)->first();
    //     return view('check.print_form', compact('template'));
    // }

  

//     public function printCheque(Request $request)
// {
//     $id= $request->cheque_template_id;
//     $template = DB::table('TBL_cheque_layouts')->where('id', $id)->first();

//     $fields = DB::table('TBL_cheque_fields')
//         ->where('layout_id', $id)
//         ->get();

//     $inputs = $request->all();
//     // $inputs['amount_words'] = $this->numberToWords($inputs['amount']);
//     $inputs['amount_words'] = \App\Library\Utilities::AmountWords($inputs['amount']);
    

//     $pdf = PDF::loadView('check.print_pdf', [
//         'template' => $template,
//         'fields'   => $fields,
//         'inputs'   => $inputs
//     ])
//     ->setPaper([0, 0, $template->width_px, $template->height_px]);

//     return $pdf->stream('cheque.pdf');
// }

public function printCheque(Request $request)
{
    $id = $request->cheque_template_id;
    $template = DB::table('TBL_cheque_layouts')->where('id', $id)->first();
    $fields = DB::table('TBL_cheque_fields')->where('layout_id', $id)->get();

    $inputs = $request->all();
    
    if($request->date_formate==3){
        $inputs['date'] = preg_replace('/[^0-9]/', '', $inputs['date']);
    }elseif($request->date_formate==2){
        $inputs['date'] = preg_replace('/[^0-9]/', '/', $inputs['date']);
    }

    $inputs['amount_partision']=substr(str_pad($inputs['amount_partision'], 3, '0'),0, 3);
    $total=$inputs['amount'].".".$inputs['amount_partision'];
    $inputs['amount']=$inputs['amount'].".".$inputs['amount_partision'];
    $inputs['amount_partision']='';

    dd($total);

    $inputs['amount_words'] = \App\Library\Utilities::amountToWords($total);

    return view('check.print_pdf', compact('template', 'fields','inputs'));

    $pdf = PDF::loadView('check.print_pdf', [
        'template' => $template,
        'fields'   => $fields,
        'inputs'   => $inputs
    ])
    // Set paper size to match your template dimensions
    // ->setPaper([0, 0, $template->width_px, $template->height_px], 'landscape');
    ->setPaper([0, 0, $template->width_px * 0.75, $template->height_px* 0.75], 'portrait');

    return $pdf->stream('cheque.pdf');
}

    // private function numberToWords($number)
    // {
    //     $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    //     return ucfirst($f->format($number));
    // }
}
