<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblAccCoa;
use App\Models\TblPurcGroupItem;
use App\Models\TblPurcProduct;
use App\Models\ViewPurcGroupItem;
use Illuminate\Http\Request;

class ProductTreeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('purchase.product_tree.list');
    }
    public function productGroupTreeList(){
        $tree = [];
        $parents = ViewPurcGroupItem::with('children')->where('group_item_level','1')
            ->where(Utilities::currentBC())->orderby('group_item_code')
            ->select('group_item_id as id','group_item_name_code_string as group_item_code','group_item_name','group_item_level')->get();
        foreach ($parents as $first){
            $firstLevel = [
                "text" => "[".$first->group_item_code."] ".$this->strUcWords($first->group_item_name),
                "id" => $first->group_item_code,
                "main_id" => $first->id,
                "level" => $first->group_item_level,
                "children" => $this->childLevelProductGroup($first->id,$first->children)
            ];
            array_push($tree, $firstLevel);
        }
        $newTree = mb_convert_encoding($tree, "UTF-8", "auto");
        return response()->json($newTree);
    }
    public function childLevelProductGroup($parent_main_id,$childArr){
        $childLevelArr = [];
        if(!empty($childArr)){
            foreach ($childArr as $child){
                $childLevel = [
                    "text" => "[".$child->group_item_code."] ".$this->strUcWords($child->group_item_name),
                    "id" => $child->group_item_code,
                    "main_id" => $child->id,
                    "level" => $child->group_item_level,
                    "parent_main_id" => $parent_main_id,
                    "children" => $this->childLevelProductGroup($child->id,$child->children)
                ];
                array_push($childLevelArr, $childLevel);
            }
        }
        return $childLevelArr;
    }

    public function productTreeList($id){
        $tree = [];
        $prods = TblPurcProduct::where(Utilities::currentBC())
            ->where('group_item_id',$id)
            ->orderby('product_code')->select('product_id as id','product_name','product_code')->get();

        foreach ($prods as $prod){
            $firstLevel = [
                "text" => "[".$prod->product_code."] ".$this->strUcWords($prod->product_name),
                "main_id" => $prod->id,
                "parent_main_id" => $id,
                'icon' => 'fa fa-file  kt-font-danger'
            ];
            array_push($tree, $firstLevel);
        }
        $newTree = mb_convert_encoding($tree, "UTF-8", "auto");
        return response()->json($newTree);
    }

    public function productTreeListWithChild($id = null){
        // product get product_group and its product_child_group
        if(isset($id)){
            $parents = ViewPurcGroupItem::with('children')
                ->where('group_item_id',$id)
                ->where(Utilities::currentBC())->orderby('group_item_code')
                ->select('group_item_id as id','group_item_name_code_string as group_item_code','group_item_name','group_item_level')->get();
        }
        $tree = [];
       /* $parents = TblPurcProduct::where(Utilities::currentBC());
        if(isset($id)){
            $parents =  $parents->where('group_item_id',$id);
        }
        $parents = $parents->orderby('product_name')->select('product_id as id','product_name')->get();*/
        foreach ($parents as $first){
            $firstLevel = [
                "text" => "[".$first->group_item_code."] ".$this->strUcWords($first->group_item_name),
                "id" => $first->group_item_code,
                "main_id" => $first->id,
                "level" => $first->group_item_level,
                "children" => $this->childLevelProduct($first->id,$first->children)
            ];
            if(count($first->children) == 0){
                $prods = TblPurcProduct::where(Utilities::currentBC())
                    ->where('group_item_id',$first->id)
                    ->orderby('product_name')->select('product_id as id','product_name')->get();
                $firstLevel['text'] = "[".$first->group_item_code."] ".$this->strUcWords($first->group_item_name).'<span class="total-product">{'.count($prods).'}</span>';
                foreach ($prods as $prod){
                    $firstLevel['children'][] = [
                        "text" => $this->strUcWords($prod->product_name),
                        "main_id" => $prod->id,
                        "parent_main_id" => $first->id,
                        'icon' => 'fa fa-file  kt-font-danger'
                    ];
                }
            }
            array_push($tree, $firstLevel);
        }
        $newTree = mb_convert_encoding($tree, "UTF-8", "auto");
        return response()->json($newTree);
    }
    public function childLevelProduct($parent_main_id,$childArr){
        $childLevelArr = [];
        if(!empty($childArr)){
            foreach ($childArr as $child){
                // dump($child->children->toArray());
                $childLevel = [
                    "id" => $child->group_item_code,
                    "main_id" => $child->id,
                    "level" => $child->group_item_level,
                    "parent_main_id" => $parent_main_id,
                    "children" => $this->childLevelProduct($child->id,$child->children)
                ];
                if(count($child->children) == 0){
                    $prods = TblPurcProduct::where(Utilities::currentBC())
                            ->where('group_item_id',$child->id)
                            ->orderby('product_name')->select('product_id as id','product_name')->get();

                    $childLevel['text'] = "[".$child->group_item_code."] ".$this->strUcWords($child->group_item_name).'<span class="total-product">{'.count($prods).'}</span>';
                    foreach ($prods as $prod){
                        $childLevel['children'][] = [
                            "text" => $this->strUcWords($prod->product_name),
                            "main_id" => $prod->id,
                            "parent_main_id" => $child->id,
                            'icon' => 'fa fa-file  kt-font-danger'
                        ];
                    }
                }else{
                    $childLevel['text'] = "[".$child->group_item_code."] ".$this->strUcWords($child->group_item_name);
                }
                array_push($childLevelArr, $childLevel);
            }
        }
        return $childLevelArr;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createProductGroup()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
