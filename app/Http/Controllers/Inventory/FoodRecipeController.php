<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\OptionsList;
use App\Models\FoodRecipe;
use App\Models\FoodRecipeDtl;
use App\Models\TblPurcProduct;
use App\Library\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Database\QueryException;

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
                $data['current'] = FoodRecipe::with(['dtls.product', 'dtls.uom', 'dtls.packing', 'option'])->where('id', $id)->first();
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


    public function getFoodDetailData(Request $request)
    {
        $optionId = $request->input('food_id'); // Keep same parameter name for compatibility
        $option = OptionsList::find($optionId);

        if (!$option) {
            return response()->json([
                'status' => 'error',
                'message' => 'Option not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'option' => $option,
                'grn' => null // Since options_list doesn't have grn relationship like food
            ],
            'message' => 'Option data retrieved successfully'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id = null)
    {
        dd($request->all());
        $data = [];
        $validator = Validator::make($request->all(), [
            'formulation_date' => 'required|date_format:d-m-Y',
            'food_id' => 'required|numeric',
            'pd.*.pd_barcode' => 'nullable|numeric',
            'pd.*.pd_uom' => 'nullable|numeric',
            'pd.*.quantity' => 'nullable|numeric',
            'formulation_remarks' => 'nullable|max:100',
        ]);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, 'Validation failed', 422);
        }

        DB::beginTransaction();
        try {
            if (isset($id)) {
                $foodRecipe = FoodRecipe::where('id', $id)->first();
            } else {
                $foodRecipe = new FoodRecipe();
                $foodRecipe->id = FoodRecipe::max('id') + 1;
            }

            $foodRecipe->food_id = $request->food_id;
            $foodRecipe->recipe_date = date('Y-m-d', strtotime($request->formulation_date));
            $foodRecipe->notes = $request->formulation_remarks;
            $foodRecipe->business_id = auth()->user()->business_id;
            $foodRecipe->company_id = auth()->user()->company_id;
            $foodRecipe->branch_id = auth()->user()->branch_id;
            $foodRecipe->save();

            // Delete existing details
            if (isset($id)) {
                FoodRecipeDtl::where('food_recipe_id', $id)->delete();
            }

            if (isset($request->pd)) {
                foreach ($request->pd as $pd) {
                    if (!empty($pd['pd_barcode']) && !empty($pd['quantity'])) {
                        $dtl = new FoodRecipeDtl();
                        $dtl->id = FoodRecipeDtl::max('id') + 1;
                        $dtl->food_recipe_id = $foodRecipe->id;
                        $dtl->product_id = $pd['pd_barcode'];
                        $dtl->product_name = $pd['product_name'];
                        $dtl->uom_id = isset($pd['pd_uom']) ? $pd['pd_uom'] : null;
                        $dtl->packing_id = isset($pd['pd_packing']) ? $pd['pd_packing'] : null;
                        $dtl->quantity = $pd['quantity'];
                        $dtl->save();
                    }
                }
            }

            DB::commit();

            if (isset($id)) {
                $data = array_merge($data, Utilities::returnJsonEditForm());
                $data['redirect'] = $this->prefixIndexPage . self::$redirect_url;
                return $this->jsonSuccessResponse($data, 'Food Recipe updated successfully', 200);
            } else {
                $data = array_merge($data, Utilities::returnJsonNewForm());
                $data['redirect'] = '/' . self::$redirect_url . $this->prefixCreatePage . '/' . $foodRecipe->id;
                return $this->jsonSuccessResponse($data, 'Food Recipe created successfully', 200);
            }

        } catch (QueryException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
    }

}
