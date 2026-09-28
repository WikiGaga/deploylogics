<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Category;
use App\Models\Food;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FoodController extends Controller
{
    public static $page_title = 'Food';
    public static $redirect_url = 'food';
    public static $menu_dtl_id = '338';

    public function create($id = null)
    {
        $data = [];
        $data['form_type'] = 'food';
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage . self::$redirect_url;
        $data['page_data']['create'] = '/' . self::$redirect_url . $this->prefixCreatePage;
        $data['categories'] = Category::orderBy('name')->get(['id', 'name', 'parent_id']);

        if (isset($id)) {
            $food = Food::where('id', $id)
                ->where('restaurant_id', auth()->user()->branch_id)
                ->first();

            if (!$food) {
                abort(404);
            }

            $data['permission'] = self::$menu_dtl_id . '-edit';
            $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
            $data['id'] = $id;
            $data['current'] = $food;
            $data['document_code'] = $food->id;
        } else {
            $data['permission'] = self::$menu_dtl_id . '-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['document_code'] = ((int) Food::max('id')) + 1;
        }

        return view('inventory.food.form', compact('data'));
    }

    public function store(Request $request, $id = null)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'price' => 'required|numeric|min:0.01',
            'discount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:percent,amount',
            'category_id' => 'nullable|numeric',
            'description' => 'nullable|max:1000',
            'veg' => 'nullable|in:0,1',
            'status' => 'nullable|in:0,1',
            'visibility' => 'nullable|in:on,off',
            'is_halal' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

        $discount = (float) ($request->discount ?? 0);
        $price = (float) $request->price;
        $discountType = $request->discount_type ?: 'percent';
        $discountValue = $discountType === 'percent' ? ($price / 100) * $discount : $discount;

        if ($price <= $discountValue) {
            return $this->jsonErrorResponse($data, 'Discount cannot be greater than or equal to price.', 422);
        }

        DB::beginTransaction();

        try {
            if (isset($id)) {
                $food = Food::where('id', $id)
                    ->where('restaurant_id', auth()->user()->branch_id)
                    ->first();

                if (!$food) {
                    return $this->jsonErrorResponse($data, 'Food item not found.', 404);
                }
            } else {
                $food = new Food();
                $food->id = ((int) Food::max('id')) + 1;
                $food->restaurant_id = auth()->user()->branch_id;
                $food->variations = '[]';
                $food->add_ons = '[]';
                $food->attributes = '[]';
                $food->choice_options = '[]';
                $food->stock_type = 'unlimited';
                $food->item_stock = 0;
                $food->sell_count = 0;
                $food->discount_type = 'percent';
                $food->available_time_starts = date('Y-m-d') . ' 00:00:00';
                $food->available_time_ends = date('Y-m-d') . ' 23:59:00';
            }

            $food->name = trim($request->name);
            $food->description = $request->description;
            $food->price = $price;
            $food->discount = $discount;
            $food->discount_type = $discountType;
            $food->veg = $request->input('veg', 0);
            $food->status = $request->input('status', 1);
            $food->visibility = $request->input('visibility', 'on');
            $food->is_halal = $request->input('is_halal', 1);

            if ($request->filled('category_id')) {
                $category = Category::find($request->category_id);
                if ($category) {
                    $categoryPayload = [];
                    if (!empty($category->parent_id)) {
                        $categoryPayload[] = ['id' => (string) $category->parent_id, 'position' => 1];
                        $categoryPayload[] = ['id' => (string) $category->id, 'position' => 2];
                        $food->category_id = $category->id;
                    } else {
                        $categoryPayload[] = ['id' => (string) $category->id, 'position' => 1];
                        $food->category_id = $category->id;
                    }
                    $food->category_ids = json_encode($categoryPayload);
                }
            }

            if (empty($food->slug)) {
                $food->slug = Str::slug($food->name) . '-' . $food->id;
            }

            $food->save();
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }

        DB::commit();

        if (isset($id)) {
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage . self::$redirect_url;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }

        $data = array_merge($data, Utilities::returnJsonNewForm());
        $data['redirect'] = '/' . self::$redirect_url . $this->prefixCreatePage . '/' . $food->id;
        return $this->jsonSuccessResponse($data, trans('message.create'), 200);
    }

    public function destroy($id)
    {
        $data = [];
        DB::beginTransaction();

        try {
            $food = Food::where('id', $id)
                ->where('restaurant_id', auth()->user()->branch_id)
                ->first();

            if (!$food) {
                return $this->jsonErrorResponse($data, 'Food item not found.', 404);
            }

            $food->delete();
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }

        DB::commit();
        return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    }
}
