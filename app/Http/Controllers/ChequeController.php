<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;



class ChequeController extends Controller
{
    // Cheque Designer Screen
    // public function designer($layout_id)
    // {

    //     // dd($layout_id);

    //     // DB::table('TBL_cheque_fields')->where('id', 1)
    //     //       ->update([ 'FIELD_NAME'=>'date', 'layout_id'=>1,'height_px'=>50,'width_px'=>250,'top_px'=>50,'left_px'=>50,'font_size'=>15]);
    //     // DB::table('TBL_cheque_fields')->where('id', 2)
    //     //       ->update([ 'FIELD_NAME'=>'ammount', 'layout_id'=>1,'height_px'=>50,'width_px'=>250,'top_px'=>100,'left_px'=>50,'font_size'=>15]);
    //     // DB::table('TBL_cheque_fields')->where('id', 3)
    //     //       ->update([ 'FIELD_NAME'=>'account title', 'layout_id'=>1,'height_px'=>50,'width_px'=>250,'top_px'=>150,'left_px'=>50,'font_size'=>15]);
    //     // DB::table('TBL_cheque_fields')->where('id', 4)
    //     //       ->update([ 'FIELD_NAME'=>'ammount in words', 'layout_id'=>1,'height_px'=>50,'width_px'=>250,'top_px'=>200,'left_px'=>50,'font_size'=>15]);

    //     $layout = DB::table('TBL_cheque_layouts')->where('id', $layout_id)->first();
    //     $fields = DB::table('TBL_cheque_fields')->where('layout_id', $layout_id)->get();

    //     // dd( $fields, $layout);
    //     return view('check/cheque_designer', compact('layout', 'fields'));
    // }

    // // Save Layout
    // public function saveLayout(Request $request)
    // {
    //     foreach ($request->fields as $field) {
    //         DB::table('TBL_cheque_fields')->updateOrInsert(
    //             [
    //                 'tem_id' => $request->layout_id,
    //                 'field_name' => $field['field_name']
    //             ],
    //             [
    //                 'top_px' => $field['top_px'],
    //                 'left_px' => $field['left_px'],
    //                 'width_px' => $field['width_px'],
    //                 'height_px' => $field['height_px'],
    //             ]
    //         );
    //     }
    //     return response()->json(['success' => true]);
    // }

    // // Show Form to enter cheque details
    // public function printForm($layout_id)
    // {

    //     dd($layout_id);
    //     $layout = DB::table('TBL_cheque_layouts')->where('id', $layout_id)->first();
    //     return view('check/cheque_print', compact('layout'));
    // }

    // // Generate PDF cheque
    // public function printCheque(Request $request, $layout_id)
    // {
    //     $layout = DB::table('TBL_cheque_layouts')->where('id', $layout_id)->first();
    //     $fields = DB::table('TBL_cheque_fields')->where('tem_id', $layout_id)->get()->keyBy('field_name');

    //     $inputs = $request->all();
    //     // $inputs['amount_words'] = convertNumberToWords($inputs['amount'] ?? 0);
    //     $inputs['amount_words'] =  'abc';

    //     $pdf = PDF::loadView('check/cheque_output_pdf', compact('layout', 'fields', 'inputs'))
    //               ->setPaper([0,0,$layout->width_px,$layout->height_px]);

    //     return $pdf->stream('check.pdf');
    // }

    // public function index()
    // {
    //     $templates = DB::table('TBL_cheque_layouts')->get();
       
    //     return view('check.templates', compact('templates'));
    // }

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
    //     dd($request->all());
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


        return view('check.design', compact('template', 'fields'));
    }

    public function saveLayout(Request $request)
    {

        // dd($request->all());

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
    
    // Ensure date is 8 digits only (e.g., 28012026) for character spacing
    // if (!empty($inputs['date'])) {
    //     $inputs['date'] = preg_replace('/[^0-9]/', '', $inputs['date']);
    // }

    if($request->date_formate==3){
        $inputs['date'] = preg_replace('/[^0-9]/', '', $inputs['date']);
    }elseif($request->date_formate==2){
        $inputs['date'] = preg_replace('/[^0-9]/', '/', $inputs['date']);
    }

    $inputs['amount_words'] = \App\Library\Utilities::AmountWords($inputs['amount']);

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
