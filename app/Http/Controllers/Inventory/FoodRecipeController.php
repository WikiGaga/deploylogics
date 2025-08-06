<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\FoodRecipe;
use App\Library\Utilities;

class FoodRecipeController extends Controller
{
    public static $page_title = 'Food Recipes';
    public static $menu_dtl_id = '341';
    public static $redirect_url = 'food-recipes';

    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['form_type'] = 'food_recipes';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage . self::$redirect_url;
        ;
        $data['page_data']['create'] = '/' . self::$redirect_url . $this->prefixCreatePage;
        if (isset($id)) {
            if (FoodRecipe::where('id', 'LIKE', $id)->exists()) {
                $data['permission'] = self::$menu_dtl_id . '-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = FoodRecipe::with('dtls', 'food')->where('id', $id)->first();
                // dd($data['current']);
                $data['page_data']['print'] = '/' . self::$redirect_url . '/print/' . $id;
                $data['document_code'] = $data['current']->id;
            } else {
                abort('404');
            }
        } else {
            $data['permission'] = self::$menu_dtl_id . '-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());

            $maxId = FoodRecipe::max('id') ?? 0;
            $data['document_code'] = $maxId + 1;
        }

        return view('inventory.food-recipe.form', compact('data'));
    }


    public function getFoodDetailData($request){
        dd($request->all());
    }

}
