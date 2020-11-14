<?php

use Illuminate\Http\Request;



/**
 * * API FOR KRUUU APPLICATION
 * * for user_type = 'talents'
 *
 */

Route::group(['prefix' => 'talents', 'middleware' => 'auth:api'], function () {

    Route::post('auth/token', 'Api\AuthController@tokencek');
    Route::post('auth/checktoken', 'Api\AuthController@ViewUserTokenExpired');
    Route::get('banner', 'Api\BannerController@get')->name('user.banner');

    Route::post('/logout', 'Api\AuthController@logout')->name('user.logout');

    // USER
    Route::post('/lastlogin', 'Api\User\UserController@ViewUserLastLogin');
    Route::get('/status', 'Api\User\UserController@viewUserStatus');
    Route::get('/exists', 'Api\User\UserController@viewUserExist');
    Route::get('/profile', 'Api\User\UserController@details');
    Route::put('/profile', 'Api\User\UserController@update');

    //HOME

    Route::get('/home/ads', 'Api\User\AdsController@allAdsList');

    Route::get('/home/ads/cart/list', 'Api\User\AdsController@allCartList');

    // TAMBAH CART
    Route::post('/home/ads/cart/create/step1', 'Api\User\AdsController@createCartStep1');
    Route::post('/home/ads/cart/create/step2', 'Api\User\AdsController@createCartStep2');
    Route::post('/home/ads/cart/create/step3', 'Api\User\AdsController@finish');

    Route::get('/home/ads/cart/count', 'Api\User\AdsController@countCart');
    Route::post('/home/ads/cart/payment', 'Api\User\AdsController@paymentAdsv1');

    Route::get('/home/ads/history', 'Api\User\AdsController@historyAdsList');
    Route::get('/home/ads/history/detail', 'Api\User\AdsController@historyAdsDetail');


    Route::get('/home/ads/detail', 'Api\User\AdsController@detailAdsList');
    Route::get('/home/ads/onbid', 'Api\User\AdsController@allOnBidList');
    Route::get('/home/ads/onbid/detail', 'Api\User\AdsController@detailOnBidList');

});

Route::group(['prefix' => 'partner', 'middleware' => 'auth:partner-api'], function () {
    Route::post('/logout', 'Api\AuthController@logout')->name('partner.logout');

    Route::get('banner', 'Api\BannerController@get')->name('partner.banner');
    Route::get('home/ads', 'Api\Partner\HomeController@getAdsList');

    Route::get('/status', 'Api\Partner\PartnerController@viewPartnerStatus');
    Route::get('/exists', 'Api\Partner\PartnerController@viewPartnerExist');
    Route::get('/profile', 'Api\Partner\PartnerController@detail');
    Route::put('/profile', 'Api\Partner\PartnerController@update');

    Route::get('/home/ads/onbid', 'Api\Partner\AdsController@allAdsListOnBid');
    Route::get('/home/ads/bid', 'Api\Partner\AdsController@allAdsListBid');
    Route::post('/home/ads', 'Api\Partner\AdsController@getAds');//!deprecated

    Route::post('/home/ads/step1', 'Api\Partner\AdsController@addAdsStep1');
    Route::post('/home/ads/step2', 'Api\Partner\AdsController@addAdsStep2');
    Route::post('/home/ads/finish', 'Api\Partner\AdsController@getAds');

    Route::get('/home/car', 'Api\Partner\CarPartnerController@allCarPartner');
    Route::post('/home/car/add', 'Api\Partner\CarPartnerController@createCarPartner');
    Route::put('/home/car', 'Api\Partner\CarPartnerController@editCarPartner');
    Route::get('/home/car/detail', 'Api\Partner\CarPartnerController@partnerCarDetail');

});

Route::post('partner/register/step1', 'Api\Partner\RegisterController@step1');
Route::post('partner/register/step2', 'Api\Partner\RegisterController@step2');
Route::post('partner/register/step3', 'Api\Partner\RegisterController@step3');
Route::post('partner/register/finish', 'Api\Partner\RegisterController@finish');

Route::post('auth/login', 'Api\AuthController@login');

// AUTH REGISTER USER
Route::post('user/register/step1', 'Api\User\RegisterController@registerAsUserStepOne');
Route::post('user/register/step2', 'Api\User\RegisterController@registerAsUserStepTwo');
Route::post('user/register/finish', 'Api\User\RegisterController@registerAsUserFinish');


Route::get('master/provinsi', 'Api\MasterController@provinsi');
Route::get('master/kota', 'Api\MasterController@kota');
Route::get('master/district', 'Api\MasterController@district');
Route::get('master/kelurahan', 'Api\MasterController@kelurahan');

Route::get('master/product', 'Api\MasterController@getMasterProduct');
Route::get('master/product/car', 'Api\MasterController@getMasterProductCar');
Route::get('master/product/loc', 'Api\MasterController@getMasterProductCarLoc');
