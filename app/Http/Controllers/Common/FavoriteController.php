<?php

namespace App\Http\Controllers\Common;

use App\Library\Utilities;
use App\Models\TblPurchaseFavorite;
use App\Models\TblPurchaseFavoriteItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{

    public function getFavorites(Request $request)
    {
        $data = [];

        $query = TblPurchaseFavorite::where('user_id', auth()->user()->id)
            ->where('is_active', 1);

        if ($request->has('module_type') && !empty($request->module_type)) {
            $query->where('module_type', $request->module_type);
        }

        $favorites = $query->orderBy('created_at', 'desc')->get();

        $data['favorites'] = $favorites;

        return $this->jsonSuccessResponse($data, 'Favorites retrieved successfully');
    }


    public function getFavoriteItems($favorite_id)
    {
        $data = [];

        $favorite = TblPurchaseFavorite::where('favorite_id', $favorite_id)
            ->where('user_id', auth()->user()->id)
            ->where('is_active', 1)
            ->first();

        if (!$favorite) {
            return $this->jsonErrorResponse($data, 'Favorite not found or access denied', 404);
        }

        $items = TblPurchaseFavoriteItem::where('favorite_id', $favorite_id)
            ->orderBy('sr_no', 'asc')
            ->get();

        $data['favorite_name'] = $favorite->favorite_name;
        $data['favorite_description'] = $favorite->favorite_description;
        $data['items'] = $items;

        return $this->jsonSuccessResponse($data, 'Favorite items retrieved successfully');
    }

    public function saveFavorite(Request $request)
    {
        $data = [];

        $validator = Validator::make($request->all(), [
            'favorite_name' => 'required|string|max:255',
            'favorite_description' => 'nullable|string',
            'module_type' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|numeric',
            'items.*.product_barcode_id' => 'required|numeric',
            'items.*.sr_no' => 'required|numeric',
            'items.*.uom_id' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, 'Validation failed', 422);
        }

        try {
            $favorite = new TblPurchaseFavorite();
            $favorite->favorite_id = Utilities::uuid();
            $favorite->favorite_name = $request->favorite_name;
            $favorite->favorite_description = $request->favorite_description ?? null;
            $favorite->module_type = $request->module_type ?? null;
            $favorite->user_id = auth()->user()->id;
            $favorite->business_id = auth()->user()->business_id;
            $favorite->company_id = auth()->user()->company_id;
            $favorite->branch_id = auth()->user()->branch_id;
            $favorite->is_active = 1;
            $favorite->created_by = auth()->user()->name ?? 'System';
            $favorite->save();

            foreach ($request->items as $item) {
                $favoriteItem = new TblPurchaseFavoriteItem();
                $favoriteItem->favorite_item_id = Utilities::uuid();
                $favoriteItem->favorite_id = $favorite->favorite_id;
                $favoriteItem->sr_no = $item['sr_no'];
                $favoriteItem->product_id = $item['product_id'];
                $favoriteItem->product_barcode_id = $item['product_barcode_id'];
                $favoriteItem->uom_id = $item['uom_id'] ?? null;
                $favoriteItem->save();
            }

            $data['favorite_id'] = $favorite->favorite_id;
            $data['favorite_name'] = $favorite->favorite_name;

            return $this->jsonSuccessResponse($data, 'Favorite saved successfully');
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($data, 'Error saving favorite: ' . $e->getMessage(), 500);
        }
    }

    public function updateFavorite(Request $request, $favorite_id)
    {
        $data = [];

        $validator = Validator::make($request->all(), [
            'favorite_name' => 'required|string|max:255',
            'favorite_description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, 'Validation failed', 422);
        }

        $favorite = TblPurchaseFavorite::where('favorite_id', $favorite_id)
            ->where('user_id', auth()->user()->id)
            ->first();

        if (!$favorite) {
            return $this->jsonErrorResponse($data, 'Favorite not found or access denied', 404);
        }

        $favorite->favorite_name = $request->favorite_name;
        $favorite->favorite_description = $request->favorite_description ?? null;
        $favorite->updated_by = auth()->user()->name ?? 'System';
        $favorite->save();

        $data['favorite_id'] = $favorite->favorite_id;
        $data['favorite_name'] = $favorite->favorite_name;

        return $this->jsonSuccessResponse($data, 'Favorite updated successfully');
    }

    public function deleteFavorite($favorite_id)
    {
        $data = [];

        $favorite = TblPurchaseFavorite::where('favorite_id', $favorite_id)
            ->where('user_id', auth()->user()->id)
            ->first();

        if (!$favorite) {
            return $this->jsonErrorResponse($data, 'Favorite not found or access denied', 404);
        }

        $favorite->is_active = 0;
        $favorite->updated_by = auth()->user()->name ?? 'System';
        $favorite->save();


        return $this->jsonSuccessResponse($data, 'Favorite deleted successfully');
    }

    public function getProductBarcode($product_barcode_id)
    {
        $data = [];

        $barcode = \App\Models\TblPurcProductBarcode::where('product_barcode_id', $product_barcode_id)
            ->first();

        if (!$barcode) {
            return $this->jsonErrorResponse($data, 'Barcode not found', 404);
        }

        $data['barcode'] = $barcode->product_barcode_barcode;
        $data['product_barcode_id'] = $barcode->product_barcode_id;

        return $this->jsonSuccessResponse($data, 'Barcode retrieved successfully');
    }
}

