<?php

use Illuminate\Http\Request;

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


Route::middleware('auth:api')->get('/getuser', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:api']], function (){

    Route::get('/userInfo',function (Request $request) {
        return $request->user();
    });

    Route::post('/authentication', 'API\UserController@authentication');

    Route::get('/user-profile', 'API\UserController@getUser');
    Route::resource('user', 'API\UserController');
    Route::get('/filter/user', 'API\UserController@filter');


    Route::resource('group', 'API\GroupController');
    Route::get('/filter/group', 'API\GroupController@filter');

    Route::resource('company', 'API\CompanyController');
    Route::post('/get-all-company','API\CompanyController@getAllCompany');
    Route::get('/filter/company', 'API\CompanyController@filter');

    Route::resource('companytype', 'API\CompanyTypeController');
    Route::get('/filter/companytype', 'API\CompanyTypeController@filter');

    Route::resource('role', 'API\RoleController');
    Route::get('/filter/role', 'API\RoleController@filter');

    Route::resource('log', 'API\LogController');
    Route::get('/filter/log', 'API\LogController@filter');

    Route::resource('module', 'API\ModuleController');
    Route::get('/filter/module', 'API\ModuleController@filter');

    //======================================= Store Related Route =======================================================

    Route::resource('/category', 'API\CategoryController');
    Route::get('/filter/category', 'API\CategoryController@filter');

    Route::resource('/type', 'API\TypeController');
    Route::get('/filter/type', 'API\TypeController@filter');

    Route::resource('/productfeature', 'API\ProductFeatureController');
    Route::get('/filter/productfeature', 'API\ProductFeatureController@filter');

    Route::resource('store', 'API\StoreController');
    Route::get('/filter/store', 'API\StoreController@filter');
    Route::get('/store/{uid}/promotions', 'API\StoreController@getPromotions');
    Route::get('/store/{uid}/warranties', 'API\StoreController@getWarranties');
    Route::get('/store/{uid}/shippings', 'API\StoreController@getShippings');
    Route::get('/store/{uid}/inventories', 'API\StoreController@getInventories');
    Route::get('/store/{uid}/vouchers', 'API\StoreController@getVouchers');

    Route::resource('storereview', 'API\StoreReviewController');
    Route::get('/filter/storereview', 'API\StoreReviewController@filter');

    Route::resource('productpromotion', 'API\ProductPromotionController');
    Route::get('/filter/productpromotion', 'API\ProductPromotionController@filter');

    Route::resource('productreview', 'API\ProductReviewController');
    Route::get('/filter/productreview', 'API\ProductReviewController@filter');
    // Route::post('/productreview/{uid}/edit', 'API\ProductReviewController@update');

    Route::resource('warranty', 'API\WarrantyController');
    Route::get('/filter/warranty', 'API\WarrantyController@filter');

    Route::resource('shipping', 'API\ShippingController');
    Route::get('/filter/shipping', 'API\ShippingController@filter');

    Route::resource('inventory', 'API\InventoryController');
    Route::get('/filter/inventory', 'API\InventoryController@filter');
    Route::post('/thumbnailupload/inventory', 'API\InventoryController@uploadThumbnail');

    Route::resource('inventoryimage', 'API\InventoryImageController');
    Route::get('/filter/inventory', 'API\InventoryImageController@filter');

    Route::resource('ticket', 'API\TicketController');
    Route::get('/filter/ticket', 'API\TicketController@filter');

    Route::resource('sale', 'API\SaleController');
    Route::get('/filter/sale', 'API\SaleController@filter');
    Route::get('/usersales', 'API\SaleController@userSales');

    Route::resource('voucher', 'API\VoucherController');
    Route::get('/filter/voucher', 'API\VoucherController@filter');

    Route::resource('vouchercode', 'API\VoucherCodeController');
    Route::get('/filter/vouchercode', 'API\VoucherCodeController@filter');


    // ========================== Blogger Related Route =========================================================

    Route::resource('blogger', 'API\BloggerController');
    Route::get('/filter/blogger', 'API\BloggerController@filter');
    Route::get('/blogger/{uid}/articles', 'API\BloggerController@getArticles');

    Route::resource('article', 'API\ArticleController');
    Route::get('/filter/article', 'API\ArticleController@filter');

    Route::resource('articleimage', 'API\ArticleImageController');
    Route::get('/filter/articleimage', 'API\ArticleImageController@filter');
    
    // ========================== Channel Related Route =========================================================

    Route::resource('comment', 'API\CommentController');
    Route::get('/filter/comment', 'API\CommentController@filter');

    Route::resource('channel', 'API\ChannelController');
    Route::get('/filter/channel', 'API\ChannelController@filter');
    Route::get('/channel/{uid}/videos', 'API\ChannelController@getVideos');

    Route::resource('video', 'API\VideoController');
    Route::get('/filter/video', 'API\VideoController@filter');
    Route::get('/uservideos', 'API\VideoController@userVideos');
    
});


Route::get('/category', 'API\CategoryController@index');
Route::get('/type', 'API\TypeController@index');
Route::get('/productfeature', 'API\ProductFeatureController@index');
Route::get('/productfeature/{uid}', 'API\ProductFeatureController@show');

Route::post('/register', 'API\UserController@register');

Route::get('/productfeature/{uid}/products', 'API\ProductFeatureController@getFeaturedProducts');

Route::post('/inventorypayment', 'API\PaymentController@inventoryPayment');
Route::post('/videopayment', 'API\PaymentController@videoPayment');
Route::post('/testpayment', 'API\PaymentController@testPayment');
Route::get('/filter/payment', 'API\PaymentController@filter');

Route::get('/inventory/{uid}/onsale', 'API\InventoryController@getOnSaleInventory');
Route::get('/inventories/onsale/filter', 'API\InventoryController@filterOnSaleInventories');


Route::get('/public/videos', 'API\VideoController@getPublicVideos');
Route::get('/public/videos/filter', 'API\VideoController@filterPublicVideos');
Route::get('/public/video/{uid}', 'API\VideoController@getPublicVideo');
Route::get('/public/video/{uid}/comments', 'API\VideoController@getVideoComments');


Route::get('/public/articles', 'API\ArticleController@getPublicArticles');
Route::get('/public/articles/filter', 'API\ArticleController@filterPublicArticles');
Route::get('/public/article/{uid}', 'API\ArticleController@getPublicArticle');
Route::get('/public/article/{uid}/comments', 'API\ArticleController@getArticleComments');


// TODO: Test upload image
Route::post('/thumbnail', 'API\InventoryController@uploadInventoryThumbnail');
