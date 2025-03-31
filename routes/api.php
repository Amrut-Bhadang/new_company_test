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

Route::group([
    'middleware' => ['throttle:250,1', 'api', 'language'],
    'prefix' => 'auth'

], function ($router) {
    Route::post('verify-otp', 'AuthController@otpVerify');
    Route::post('send-otp', 'AuthController@sendOtp');
    Route::post('set-password', 'AuthController@setPassword');
    Route::post('update-profile', 'AuthController@updateProfile');
    Route::post('update-profile-mobile', 'AuthController@updateProfileMobile');
    Route::post('logout', 'AuthController@logout');
    Route::post('login', 'AuthController@login');
    Route::post('signUp', 'AuthController@signUp');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('user-profile', 'AuthController@userProfile');
    Route::post('forgot-password', 'AuthController@forgot_password');
    Route::post('reset-password', 'AuthController@resetPassword');
    Route::post('change-password', 'AuthController@changePassword');
    // Route::post('static-content', 'AuthController@getStaticData');
    Route::post('saveDetails', 'AuthController@saveDetails');

    Route::get('getTaxList', 'AuthController@tax_list');
    Route::post('setting-data', 'AuthController@setting_data');
    Route::post('tax-detail', 'AuthController@tax_detail');
    Route::post('delete-account', 'AuthController@delete_account');
    Route::get('wallet-list', 'ApiController@wallet_list');
    Route::post('add-money', 'ApiController@add_money');
    Route::post('update_wallet', 'ApiController@update_transaction');

    // social login
    Route::post('social-login', 'AuthController@socialLogin');


    //Home Api
    Route::post('home', 'Api\HomeController@index');
    Route::post('search-homeData', 'Api\ApiController@searchHomeData');

    //court Api
    Route::post('court_list', 'Api\ApiController@getAllCourt');
    Route::post('get-all-court-list', 'Api\ApiController@getAllCourtList');
    Route::post('court-detail', 'Api\ApiController@courtDetail');
    Route::post('user-court-favourate', 'Api\ApiController@userCourtFavourate');
    Route::post('get-court-review', 'Api\ApiController@courtReview');
    Route::post('post-court-review', 'Api\ApiController@postCourtReview');

    Route::post('book-court', 'Api\ApiController@bookCourt');


    Route::post('search-facility-and-court', 'Api\ApiController@searchFacilityAndCourt');

    Route::post('get-player-list', 'Api\ApiController@getAllPlayers');

    //facility Api
    Route::post('facility_list', 'Api\ApiController@getAllFacility');
    Route::post('get-all-facility-list', 'Api\ApiController@getAllFacilityList');
    Route::post('facility-detail', 'Api\ApiController@facilityDetail');

    //court booking Api
    Route::post('court-booking-list', 'Api\ApiController@getAllCourtBooking');
    Route::post('court-booking-detail', 'Api\ApiController@CourtBookingDetail');
    Route::post('court-booking-check-available', 'Api\ApiController@CourtBookingCheckAvaliable');
    Route::post('court-booking-cancel', 'Api\ApiController@courtBookingCancel');
    Route::post('court-challenge-booking-cancel', 'Api\ApiController@courtBookingCancel');
    Route::post('join-challenge', 'Api\ApiController@joinChallenge');
    Route::post('all-challenge-list', 'Api\ApiController@getAllJoinChallenge');
    Route::post('invite-player', 'Api\ApiController@invitePlayer');
    Route::post('cancellation_charge', 'Api\ApiController@getCancellationCharge');

    // payment-curl
    // Route::get('/payment-curl', [PaymentController::class,'paymentWithCurl'])->name('payment-curl');
    Route::post('payment-curl', 'Api\ApiController@paymentWithCurl');
    Route::post('refund-payment-curl', 'Api\ApiController@refundPaymentWithCurl');
    

    // notification API
    Route::post('notification-list', 'Api\ApiController@notificationList');
    Route::post('notification-count', 'Api\ApiController@getNotificationCount');
    Route::post('read-notification', 'Api\ApiController@readNotification');
    Route::post('notification-remove', 'Api\ApiController@removeNotification');
    /*Order Data*/
    Route::post('create-order', 'ApiController@saveOrder');

    /*User Address*/
    Route::get('getAllAddress', 'ApiController@getAllAddress');
    Route::get('getDefaultAddress', 'ApiController@getDefaultAddress');
    Route::post('addAddress', 'ApiController@addAddress');
    Route::post('deleteAddress', 'ApiController@deleteAddress');
    Route::post('defaultAddress', 'ApiController@defaultAddress');

    //car 
    Route::get('getAllCar', 'ApiController@getAllCar');
    Route::get('getDefaultCar', 'ApiController@getDefaultCar');
    Route::post('addCar', 'ApiController@addCar');
    Route::post('deleteCar', 'ApiController@deleteCar');
    Route::post('defaultCar', 'ApiController@defaultCar');

    Route::get('order_callback', 'AuthController@order_callback');

    // Faq Section
    Route::post('faq-request', 'ApiController@faq_request');
    Route::post('faq-list', 'ApiController@getFaq');

    /*Route::post('apply-discount', 'ApiController@applyDiscount');
    Route::post('remove-discount', 'ApiController@removeDiscount');
    Route::post('checkTableAvailable', 'ApiController@checkTableAvailable');

    Route::post('getHomeData', 'ApiController@getHomeData');
    Route::post('getAllRestro', 'ApiController@getAllRestro');
    Route::post('getAllBrand', 'ApiController@getAllBrand');
    Route::post('getBrandDetail', 'ApiController@getBrandDetail');
    Route::post('favoriteDish', 'ApiController@favoriteDish');
    Route::post('favoriteListing', 'ApiController@favoriteListing');
    Route::post('restroDetail', 'ApiController@restroDetail');
    Route::get('getMainCategory', 'ApiController@getMainCategory');

    Route::post('otpVerifyChangeMobile','AuthController@otpVerifyChangeMobile');
    Route::post('getAllData', 'ApiController@getAllData');
    Route::post('getAllCategory', 'ApiController@getAllCategory');
    Route::post('getSubCategory', 'ApiController@getSubCategory');
    Route::get('getAllCelebrity', 'ApiController@getAllCelebrity');
    Route::post('getAllDish', 'ApiController@getAllDish');
    Route::post('getDishToppings', 'ApiController@getDishToppings');
    Route::get('getCelebrityDetails', 'ApiController@getCelebrityDetails');
    Route::post('getProductDetails', 'ApiController@getProductDetails');
    Route::post('getProductTags', 'ApiController@getProductTags');
    Route::get('getAllGift', 'ApiController@getAllGift');
    // Route::get('getAllGiftCategory', 'ApiController@getAllGiftCategory');


    Route::post('getDiscountCode', 'ApiController@getDiscountCode');
    Route::get('getRandomCode', 'ApiController@getRandomCode');
    Route::get('getOffer', 'ApiController@getOffer');
    Route::post('checkProductAvailbility', 'ApiController@checkProductAvailbility');

    Route::post('getAllCelebrityDish', 'ApiController@getAllCelebrityDish');
    Route::post('getAllCelebrityCategory', 'ApiController@getAllCelebrityCategory');

    Route::post('addtocart', 'CartController@add_to_cart');
    Route::post('getCart', 'CartController@cart_list');
    Route::get('cartDestroy', 'CartController@cart_destroy');
    Route::post('update-shopcart', 'CartController@update_shop_cart');
    Route::post('add-contact-detail', 'CartController@addContactDetails');
    Route::post('checkout', 'CartController@checkout');
    Route::post('order-list', 'CartController@order_list');
    Route::post('order-detail', 'CartController@order_detail');
    Route::post('rate-restro', 'CartController@rate_restro');
    Route::post('rate-product', 'CartController@rate_product');
    Route::post('cancel-order', 'CartController@cancel_order');
    Route::post('reorder', 'CartController@reorder');
    Route::post('order-rating-list', 'CartController@orderRatingList');
    Route::post('product-rating-list', 'CartController@productRatingList');
    Route::post('customer_arrive', 'CartController@customer_arrive');
    Route::post('edit_order', 'CartController@edit_order');
    Route::post('cancel-reasions', 'CartController@cancelReasion');
    Route::post('checkout_payonfinish', 'CartController@checkout_payonfinish');
    //pay by other
    Route::post('dishPayByOther', 'CartController@payByOther');
    Route::post('dishPayByOtherPaymentStatus', 'CartController@payByOtherPaymentStatus');

    Route::get('notification-list', 'ApiController@notificationList');
    Route::post('notification-remove', 'ApiController@removeNotification');
    Route::post('notification-all-remove', 'ApiController@removeAllNotification');
    Route::post('read-notification', 'ApiController@readNotification');
    Route::get('read-all-notification', 'ApiController@readAllNotification');

    Route::post('category-restro-list', 'ApiController@getCategoryRestroList');
    Route::post('searchUsers', 'ApiController@searchUsers');
    Route::post('splitBillAction', 'CartController@actionPaymentRequest');
    Route::post('getSplitBillUserData', 'CartController@getSplitBillUserData');
    Route::post('splitBillPaymentStatus', 'CartController@getSplitBillPaymentStatus');*/

    Route::post('register-user', 'CommonApiController@registerUser');
    Route::get('getModes', 'CommonApiController@getModes');
    Route::post('getMainCategory', 'CommonApiController@getMainCategory');
    Route::get('getLanguage', 'CommonApiController@getLanguage');
    Route::post('static-content', 'Api\ContentController@getContant');
});

Route::group([
    'middleware' => ['throttle:500,1', 'api', 'localization', 'customeAuthorization'],
    'prefix' => 'auth'

], function ($router) {
    // Common With Auth Section

    /*User Section*/
    Route::post('user-details', 'CommonWithAuthApiController@userProfile');
    // Route::post('saveDetails','CommonWithAuthApiController@saveDetails');

    // /*Faq Section*/
    // Route::post('faq-request', 'CommonWithAuthApiController@faq_request');
    // Route::post('faq-list', 'CommonWithAuthApiController@getFaq');

    // /*User Car Section*/
    // Route::get('getAllCar', 'CommonWithAuthApiController@getAllCar');
    // Route::get('getDefaultCar', 'CommonWithAuthApiController@getDefaultCar');
    // Route::post('addCar', 'CommonWithAuthApiController@addCar');
    // Route::post('deleteCar', 'CommonWithAuthApiController@deleteCar');
    // Route::post('defaultCar', 'CommonWithAuthApiController@defaultCar');
});
