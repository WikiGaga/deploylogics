<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', 'Api\Auth\LoginController@register');
Route::post('auth/login', 'Api\Auth\LoginController@login');
Route::get('public-config', 'Api\Auth\LoginController@publicConfig');
Route::get('whatsapp-api/{link}' , 'Api\WhatsApp\WhatsAppApiController@response');

// WhatsApp Webhook Services
Route::get('/whatsapp-webhook', 'Api\WhatsApp\WhatsAppApiController@validateResponse');
Route::post('/whatsapp-webhook', 'Api\WhatsApp\WhatsAppApiController@handleWebhookResponse');
Route::get('/whatsapp-send-bill/{text?}/{link?}/{to?}', 'Api\WhatsApp\WhatsAppApiController@sendWhatsAppDocument');
Route::post('/whatsapp-send-offer/{link}', 'Api\WhatsApp\WhatsAppApiController@sendWhatsAppOfferFile');


Route::group(['middleware'=>['auth:api']], function () {

    Route::get('test', 'Api\ApiHomeController@index');

    Route::get('dashboard', 'Api\ApiHomeController@dashboard');

    Route::prefix('auth')->group(function () {
        Route::post('verify-branch', 'Api\Auth\LoginController@verifyBranch');
        Route::get('private-config', 'Api\Auth\LoginController@privateConfig');
        Route::get('me',  'Api\Auth\LoginController@me');
        Route::post('refresh', 'Api\Auth\LoginController@refresh');
        Route::get('logout', 'Api\Auth\LoginController@logout');
    });

    Route::prefix('listing')->group(function () {
        Route::get('grn','Api\Common\ListingController@GRNList');
        Route::get('demand','Api\Common\ListingController@PurchaseDemandList');
        Route::get('stock/{type}/{current_page?}','Api\Common\ListingController@StockList');
        Route::get('stock-request/{current_page?}','Api\Common\ListingController@StockRequestList');
        Route::get('stock-taking/{current_page?}','Api\Common\ListingController@StockTakingList');
        Route::get('opening-stock/{current_page?}','Api\Common\ListingController@OpeningStockList');
        Route::get('stock-adjustment/{current_page?}','Api\Common\ListingController@StockAdjustmentList');
        Route::get('test/{current_page?}','Api\Common\ListingController@TestList');

        Route::post('inline-help/{helpType}/{str?}','Api\Common\ListingController@inlineHelpOpen');
    });

    Route::prefix('help')->group(function () {
        Route::get('barcode','Api\Common\FormHelpController@barcodeHelp');
        Route::get('po','Api\Common\FormHelpController@poHelp');
        Route::get('supplier','Api\Common\FormHelpController@supplierHelp');
        Route::get('get-stock-locations','Api\Common\FormHelpController@LocationsByStoreHelp');
    });

    Route::prefix('product')->group(function () {
        Route::post('barcode','Api\Product\ProductBarcodeController@getBarcodeDtl');
        Route::post('get-barcode-detail-by-uom','Api\Product\ProductBarcodeController@getBarcodeDetailByUOM');
    });

    Route::prefix('grn')->group(function () {
        Route::get('form','Api\Purchase\GRNController@create');
        Route::post('store/{id?}','Api\Purchase\GRNController@store');
        Route::post('delete/{id}','Api\Purchase\GRNController@destroy');
    });

    Route::prefix('stock-taking')->group(function () {
        Route::get('form','Api\Inventory\StockTakingController@create');
        Route::post('store/{id?}','Api\Inventory\StockTakingController@store');
        Route::post('delete/{id}','Api\Inventory\StockTakingController@destroy');
    });

    Route::prefix('demand')->group(function () {
        Route::get('form/{id?}','Api\Purchase\PurchaseDemandController@create');
        Route::get('get/category-products/{id}','Api\Purchase\PurchaseDemandController@getCategoryProducts');
        Route::post('store/{id?}','Api\Purchase\PurchaseDemandController@store');
        Route::post('delete/{id}','Api\Purchase\PurchaseDemandController@destroy');
    });

    // for opening stock,stock transfer,internal stock transfer
    /*do not use = Route::prefix('stock/{type}')->group(function () {
        Route::get('form/{id?}','Api\Inventory\StockController@create');
        Route::post('store/{id?}','Api\Inventory\StockController@store');
        Route::post('delete/{id}','Api\Inventory\StockController@destroy');
    });*/

    Route::prefix('stock-request')->group(function () {
        Route::get('form/{id?}','Api\Inventory\StockRequestController@create');
        Route::post('store/{id?}','Api\Inventory\StockRequestController@store');
        Route::post('delete/{id}','Api\Inventory\StockRequestController@destroy');
    });

    Route::prefix('inventory')->group(function () {
        Route::prefix('opening-stock')->group(function () {
            Route::get('form/{id?}','Api\Inventory\OpeningStockController@create');
            Route::post('store/{id?}','Api\Inventory\OpeningStockController@store');
        });
        Route::prefix('stock-adjustment')->group(function () {
            Route::get('form/{id?}','Api\Inventory\StockAdjustmentController@create');
            Route::post('store/{id?}','Api\Inventory\StockAdjustmentController@store');
        });
    });
});
