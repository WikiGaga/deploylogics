<?php
namespace App\Http\Controllers\Accounts;
use App\Library\Utilities;
use App\Models\TblAccCoaBranches;
use App\Models\TblSoftBranch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TblAccCoa;
// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Session;

class TreeController extends Controller {

    public function treeView(){
        $tree = "";
        /*$parents = TblAccCoa::where('chart_level','1')->where(Utilities::currentBC())->orderby('chart_code')->get();
        $tree='<ul id="browser" class="filetree">';
        foreach ($parents as $parent) {
            $tree .='<li class="tree-view closed"<a class="tree-name">['.$parent->chart_code.'] '.$parent->chart_name.'</a>';
            $tree .=$this->childView($parent);
        }
        $tree .='</li></ul>';*/
        return view('accounts.chart_of_account_tree.treeview',compact('tree'));
    }
    public function getAccTree(){
       $tree = [];
       $parents = TblAccCoa::with('children')->where('chart_level','1')
           ->where(Utilities::currentBC())->orderby('chart_code')
           ->select('chart_account_id as id','chart_code','chart_name')->get();
       // dd($parents->toArray());
       foreach ($parents as $first){
           $secondLevelArr = [];
           foreach ($first['children'] as $secondItem){
               $thirdLevelArr = [];
               foreach ($secondItem['children'] as $thirdItem){
                   $forthLevelArr = [];
                   foreach ($thirdItem['children'] as $forthItem){
                       $forthLevel = [
                           "text" => "[".$forthItem->chart_code."] ".$this->strUcWords($forthItem->chart_name),
                           "id" => $forthItem->chart_code,
                           "main_id" => $forthItem->id,
                           "parent_main_id" => $thirdItem->id,
                           "level" => 4,
                       ];
                       array_push($forthLevelArr, $forthLevel);
                   }
                   $thirdLevel = [
                       "text" => "[".$thirdItem->chart_code."] ".$this->strUcWords($thirdItem->chart_name),
                       "id" => $thirdItem->chart_code,
                       "main_id" => $thirdItem->id,
                       "level" => 3,
                       "parent_main_id" => $secondItem->id,
                       "children" => $forthLevelArr
                   ];
                   array_push($thirdLevelArr, $thirdLevel);
               }
               $secondLevel = [
                   "text" => "[".$secondItem->chart_code."] ".$this->strUcWords($secondItem->chart_name),
                   "id" => $secondItem->chart_code,
                   "main_id" => $secondItem->id,
                   "level" => 2,
                   "parent_main_id" => $first->id,
                   "children" => $thirdLevelArr
               ];
               array_push($secondLevelArr, $secondLevel);
           }
           $firstLevel = [
               "text" => "[".$first->chart_code."] ".$this->strUcWords($first->chart_name),
               "id" => $first->chart_code,
               "main_id" => $first->id,
               "level" => 1,
               "children" => $secondLevelArr
           ];
           array_push($tree, $firstLevel);
       }
       $newTree = mb_convert_encoding($tree, "UTF-8", "auto");
       return response()->json($newTree);
   }
    public function childView($parent){
            $childs = TblAccCoa::where('chart_level','2')->where(Utilities::currentBC())->where('parent_account_code',$parent->chart_code)->get();
            $childTree = '';
            if(count($childs)>0){
                $childTree ='<ul>';
                foreach ($childs as $child) {
                    $childTree .='<li class="tree-view"><a class="tree-name">['.$child->chart_code.'] '.$child->chart_name.'</a>';
                    $childTree .=$this->innerChildView($child);
                }
                $childTree .="</li></ul>";
            }
            return $childTree;
    }
    public function innerChildView($child){
        $innerChilds = TblAccCoa::where('chart_level','3')->where(Utilities::currentBC())->where('parent_account_code',$child->chart_code)->get();
        $innerChildTree = '';
        if(count($innerChilds)>0){
            $innerChildTree ='<ul>';
            foreach ($innerChilds as $innerChild) {
                $innerChildTree .='<li class="tree-view"><a class="tree-name">['.$innerChild->chart_code.'] '.$innerChild->chart_name.'</a>';
                $innerChildTree .=$this->mostInnerChildView($innerChild);
            }
            $innerChildTree .="</li></ul>";
        }
        return $innerChildTree;
    }
    public function mostInnerChildView($innerChild){
        $mostInnerChilds = TblAccCoa::where('chart_level','4')->where(Utilities::currentBC())->where('parent_account_code',$innerChild->chart_code)->get();
        $mostInnerChildTree = '';
        if(count($mostInnerChilds)>0){
            $mostInnerChildTree ='<ul>';
            foreach ($mostInnerChilds as $mostInnerChild) {
                $mostInnerChildTree .='<li class="tree-view"><a class="tree-name">['.$mostInnerChild->chart_code.'] '.$mostInnerChild->chart_name.'</a></li>';
            }
            $mostInnerChildTree .="</ul>";
        }
        return $mostInnerChildTree;
    }
}
