
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});
Route::get('/config-cache', function () {
    Artisan::call('config:cache');
    return "Config is cleared";
});
Auth::routes(['verify' => true, 'register' => false]);

Route::group([
    'middleware' => ['localization']

], function ($router) {
    Route::get('locale/{locale}', function ($locale) {
        Session::put('locale', $locale);
        return redirect()->back();
    });
});

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', function () {
        return redirect()->route('admin.login');
    });

    // login route
    Route::POST('logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::POST('login', 'Auth\LoginController@login')->name('login');


    Route::get('/dashboard', 'HomeController@index')->name('home')->middleware(['verified', 'auth']);
    Route::get('switchAccount/{id}', 'HomeController@switchAccount')->name('switchAccount');
    
    Route::get('/sendReminderNotification', 'CronController@sendReminderNotification');
    Route::get('/dish/payByOther/{id}', 'PayController@payByOther');
    Route::get('/gift/payByOther/{id}', 'PayController@payByOthergift');
    Route::post('/gift/purchase/{id}', 'PayController@purchaseGift');
    Route::post('/dish/purchase/{id}', 'PayController@purchaseDish');
    Route::get('/thank-you', 'PayController@thankyou');
    Route::get('/page/{slug}', 'PayController@page');

    // facility_owner manager route
    Route::resource('api/facility_owner', 'FacilityOwnerController');

    Route::get('facility_owner', 'FacilityOwnerController@frontend')->name('facility_owner');
    Route::get('facility_owner/create', 'FacilityOwnerController@create');
    Route::get('facility_owner/edit/{id}', 'FacilityOwnerController@edit_frontend');
    Route::get('facility_owner/changeStatus/{id}/{status}', 'FacilityOwnerController@changeStatus');
    Route::get('facility_owner/view/{id}', 'FacilityOwnerController@show');
    Route::get('facility_owner/export/{slug}', 'FacilityOwnerController@exportFacilityOwner');

    // user bank detail manager route
    Route::resource('api/user_bank_detail', 'UserBankDetailController');
    Route::get('user_bank_detail', 'UserBankDetailController@frontend')->name('user_bank_detail');
    Route::get('user_bank_detail/create', 'UserBankDetailController@create');
    Route::get('user_bank_detail/edit/{id}', 'UserBankDetailController@edit_frontend');
    Route::get('user_bank_detail/changeStatus/{id}/{status}', 'UserBankDetailController@changeStatus');
    Route::get('user_bank_detail/view/{id}', 'UserBankDetailController@show');
    Route::get('user_bank_detail/export/{slug}', 'UserBankDetailController@exportUserBankDetail');


    // players manager route
    Route::resource('api/players', 'PlayerController');
    Route::get('players', 'PlayerController@frontend')->name('players');
    Route::get('players/create', 'PlayerController@create');
    Route::get('players/edit/{id}', 'PlayerController@edit_frontend');
    Route::get('players/changeStatus/{id}/{status}', 'PlayerController@changeStatus');
    Route::get('players/view/{id}', 'PlayerController@show');
    Route::get('players/export/{slug}', 'PlayerController@exportPlayers');
    Route::get('players/is_facility_owner/{id}/{status}', 'PlayerController@is_facility_owner');
    Route::get('players/delete_users/{id}/{is_delete}', 'PlayerController@delete_users');



    // facility manager route
    Route::resource('api/facilities', 'FacilityController');
    Route::get('facilities', 'FacilityController@frontend')->name('facilities');
    Route::get('facilities/create', 'FacilityController@create');
    Route::get('facilities/edit/{id}', 'FacilityController@edit_frontend');
    Route::get('facilities/changeStatus/{id}/{status}', 'FacilityController@changeStatus');
    Route::get('facilities/view/{id}', 'FacilityController@show');
    Route::get('facilities/export/{slug}', 'FacilityController@exportFacility');
    Route::get('facilities/delete_facilities/{id}/{is_delete}', 'FacilityController@delete_facilities');


    // Amenity manager route
    Route::resource('api/amenities', 'AmenityController');

    Route::get('amenities', 'AmenityController@frontend')->name('amenities');
    Route::get('amenities/create', 'AmenityController@create');
    Route::get('amenities/edit/{id}', 'AmenityController@edit_frontend');
    Route::get('amenities/changeStatus/{id}/{status}', 'AmenityController@changeStatus');
    Route::get('amenities/view/{id}', 'AmenityController@show');

    // Banner manager route
    Route::resource('api/banner', 'BannerController');
    Route::get('banner', 'BannerController@frontend')->name('banner');
    Route::get('banner/create', 'BannerController@create');
    Route::get('banner/edit/{id}', 'BannerController@edit_frontend');
    Route::get('banner/changeStatus/{id}/{status}', 'BannerController@changeStatus');
    Route::get('banner/view/{id}', 'BannerController@show');
    Route::get('banner/show_type_data/{type}/{type_id?}', 'BannerController@show_type_data');

    // Testimonial manager route
    Route::resource('api/testimonial', 'TestimonialController');
    Route::get('testimonial', 'TestimonialController@frontend')->name('testimonial');
    Route::get('testimonial/create', 'TestimonialController@create');
    Route::get('testimonial/edit/{id}', 'TestimonialController@edit_frontend');
    Route::get('testimonial/changeStatus/{id}/{status}', 'TestimonialController@changeStatus');
    Route::get('testimonial/view/{id}', 'TestimonialController@show');
    Route::get('testimonial/show_type_data/{type}/{type_id?}', 'TestimonialController@show_type_data');

    // court-category manager route
    Route::resource('api/court-category', 'CourtCategoryController');
    Route::get('court-category', 'CourtCategoryController@frontend')->name('court_category');
    Route::get('court-category/create', 'CourtCategoryController@create');
    Route::get('court-category/edit/{id}', 'CourtCategoryController@edit_frontend');
    Route::get('court-category/changeStatus/{id}/{status}', 'CourtCategoryController@changeStatus');
    Route::get('court-category/view/{id}', 'CourtCategoryController@show');

    // Commission manager route
    Route::resource('api/commission', 'CommissionController');

    Route::get('commission', 'CommissionController@frontend')->name('commission');
    Route::get('commission/create', 'CommissionController@create');
    Route::get('commission/edit/{id}', 'CommissionController@edit_frontend');
    Route::get('commission/changeStatus/{id}/{status}', 'CommissionController@changeStatus');
    Route::get('commission/view/{id}', 'CommissionController@show');
    Route::get('commission/show_court_data/{facility_id}/{court_id?}', 'CommissionController@show_court_data');


    Route::resource('api/roles', 'RoleController');
    Route::get('roles', 'RoleController@frontend')->name('roles');
    Route::get('roles/edit/{id}', 'RoleController@edit_frontend');

    Route::resource('api/users', 'UsersController');
    Route::get('users', 'UsersController@frontend')->name('users');
    Route::get('users/edit/{id}', 'UsersController@edit_frontend');
    Route::get('users/changeStatus/{id}/{status}', 'UsersController@changeStatus');
    Route::get('users/view/{id}', 'UsersController@show');
    Route::get('users/view_orders/{id}', 'UsersController@view_orders')->name('view_orders');
    Route::get('users/view_address/{id}', 'UsersController@view_address')->name('view_address');
    Route::get('users/transaction/{id}', 'UsersController@showtransaction');
    Route::get('users/giftstransaction/{id}', 'UsersController@showgifttransaction');
    Route::get('users/exportUsers/{slug}', 'UsersController@exportUsers');
    Route::get('users/exportTransUsers/{slug}', 'UsersController@exportTransUsers');
    Route::get('users/exportGiftUsers/{slug}', 'UsersController@exportGiftUsers');
    Route::post('users/ImportUsers', 'UsersController@importUsers');
    Route::post('users/getWalletData', 'UsersController@getWalletData');
    Route::post('users/send_notification', 'UsersController@send_notification');
    

    Route::resource('api/courts', 'CourtsController');
    Route::get('courts', 'CourtsController@frontend')->name('courts');
    Route::get('courts/create', 'CourtsController@create');
    Route::get('courts/edit/{id}', 'CourtsController@edit_frontend');
    Route::get('courts/changeStatus/{id}/{status}', 'CourtsController@changeStatus');
    Route::get('courts/changeIsFeatured/{id}/{is_featured}', 'CourtsController@changeIsFeatured');
    Route::get('courts/delete_court/{id}/{is_delete}', 'CourtsController@delete_court');
    Route::get('courts/view/{id}', 'CourtsController@show');
    Route::get('courts/view_orders/{id}', 'CourtsController@view_orders')->name('view_orders');
    Route::get('courts/exportUsers/{slug}', 'CourtsController@exportUsers');
    Route::post('courts/ImportUsers', 'CourtsController@importUsers');
    Route::post('courts/send_notification', 'CourtsController@send_notification');
    Route::delete('api/courts/{id?}', 'CourtsController@destroy')->name('courts.delete');
    Route::get('courts/manage_timing/{id}', 'CourtsController@manage_timing');
    Route::get('courts/popular_timing/{id}', 'CourtsController@popularTiming');
    Route::get('courts/popular_timing_index/{id}', 'CourtsController@popularTimingIndex');
    Route::get('courts/popular_timing/create/{id}', 'CourtsController@popularTimingCreate');
    Route::post('courts/popular_timing/store', 'CourtsController@popularTimingStore');
    Route::get('courts/popular_timing/edit/{court_id}/{id}', 'CourtsController@popularTimingEdit');
    Route::post('courts/popular_timing/update/{court_id}/{id}', 'CourtsController@popularTimingUpdate');
    Route::get('courts/popular_timing/changeStatus/{id}/{status}', 'CourtsController@popularTimeChangeStatus');

    Route::post('courts/book_slot', 'CourtsController@book_slot');
    Route::get('courts/check_book_slot/{id}/{date}', 'CourtsController@check_book_slot');
    Route::get('courts/show_court_category_data/{facility_id}/{category_id?}', 'CourtsController@show_court_category_data');


    Route::resource('api/subadmin', 'SubadminController');
    Route::get('subadmin', 'SubadminController@frontend')->name('subadmin');
    Route::get('subadmin/edit/{id}', 'SubadminController@edit_frontend');
    Route::get('subadmin/changeStatus/{id}/{status}', 'SubadminController@changeStatus');
    Route::get('subadmin/view/{id}', 'SubadminController@show');
    Route::get('subadmin/view_orders/{id}', 'SubadminController@view_orders')->name('view_orders');
    Route::get('subadmin/view_address/{id}', 'SubadminController@view_address')->name('view_address');
    Route::get('subadmin/transaction/{id}', 'SubadminController@showtransaction');
    Route::get('subadmin/giftstransaction/{id}', 'SubadminController@showgifttransaction');
    Route::get('subadmin/exportUsers/{slug}', 'SubadminController@exportUsers');
    Route::post('subadmin/ImportUsers', 'SubadminController@importUsers');
    Route::post('subadmin/getWalletData', 'SubadminController@getWalletData');

    Route::resource('api/operator', 'OperatorController');
    Route::get('operator', 'OperatorController@frontend')->name('operator');
    Route::get('operator/edit/{id}', 'OperatorController@edit_frontend');
    Route::get('operator/changeStatus/{id}/{status}', 'OperatorController@changeStatus');
    Route::get('operator/view/{id}', 'OperatorController@show');
    Route::get('operator/view_orders/{id}', 'OperatorController@view_orders')->name('view_orders');
    Route::get('operator/view_address/{id}', 'OperatorController@view_address')->name('view_address');
    Route::get('operator/transaction/{id}', 'OperatorController@showtransaction');
    Route::get('operator/giftstransaction/{id}', 'OperatorController@showgifttransaction');
    Route::get('operator/exportUsers/{slug}', 'OperatorController@exportUsers');
    Route::post('operator/ImportUsers', 'OperatorController@importUsers');
    Route::post('operator/getWalletData', 'OperatorController@getWalletData');
    Route::post('operator/send_notification', 'OperatorController@send_notification');

    Route::resource('api/chef', 'ChefController');
    Route::get('chef', 'ChefController@frontend')->name('chef');
    Route::get('chef/edit/{id}', 'ChefController@edit_frontend');
    Route::get('chef/changeStatus/{id}/{status}', 'ChefController@changeStatus');
    Route::get('chef/view/{id}', 'ChefController@show');
    Route::get('chef/product/{id}', 'ChefController@productShow');

    Route::get('api/chef-staff-list/{id?}', 'ChefController@chef_staff_listing');
    Route::get('chef-staff-list/{id?}', 'ChefController@chef_staff')->name('chef_staff');

    Route::resource('api/celebrity', 'CelebrityController');
    Route::get('celebrity', 'CelebrityController@frontend')->name('celebrity');
    Route::get('celebrity/edit/{id}', 'CelebrityController@edit_frontend');
    Route::get('celebrity/changeStatus/{id}/{status}', 'CelebrityController@changeStatus');
    Route::get('celebrity/view/{id}', 'CelebrityController@show');
    Route::get('celebrity/product/{id}', 'CelebrityController@productShow');

    Route::resource('api/content', 'ContentController');
    Route::get('content', 'ContentController@frontend')->name('content');
    Route::get('content/edit/{id}', 'ContentController@edit_frontend');
    Route::get('content/changeStatus/{id}/{status}', 'ContentController@changeStatus');
    Route::get('content/view/{id}', 'ContentController@show');

    Route::resource('api/contact_us', 'ContactUsController');
    Route::get('contact_us', 'ContactUsController@frontend')->name('contact_us');
    Route::get('contact_us/edit/{id}', 'ContactUsController@edit_frontend');
    Route::get('contact_us/changeStatus/{id}/{status}', 'ContactUsController@changeStatus');
    Route::get('contact_us/view/{id}', 'ContactUsController@show');

    Route::resource('api/category', 'CategoryController');
    Route::get('category', 'CategoryController@frontend')->name('category');
    Route::get('category/edit/{id}', 'CategoryController@edit_frontend');
    Route::get('category/changeStatus/{id}/{status}', 'CategoryController@changeStatus');
    Route::get('category/view/{id}', 'CategoryController@show');
    Route::get('category/exportUsers/{slug}', 'CategoryController@exportUsers');
    Route::post('category/ImportUsers', 'CategoryController@importUsers');
    Route::get('category/import', 'CategoryController@import');
    Route::post('category/importData', 'CategoryController@importData');

    Route::resource('api/subcategory', 'SubCategoryController');
    Route::get('subcategory', 'SubCategoryController@frontend')->name('subcategory');
    Route::get('subcategory/edit/{id}', 'SubCategoryController@edit_frontend');
    Route::get('subcategory/changeStatus/{id}/{status}', 'SubCategoryController@changeStatus');
    Route::get('subcategory/view/{id}', 'SubCategoryController@show');
    Route::get('subcategory/exportUsers/{slug}', 'SubCategoryController@exportUsers');
    Route::get('subcategory/show_service_category/{id}/{category_id?}', 'SubCategoryController@show_service_category');
    Route::get('subcategory/show_sub_category/{id}/{sub_category_id?}', 'SubCategoryController@show_sub_category');

    Route::resource('api/faq', 'FaqController');
    Route::get('faq', 'FaqController@frontend')->name('faq');
    Route::get('faq/edit/{id}', 'FaqController@edit_frontend');
    Route::get('faq/changeStatus/{id}/{status}', 'FaqController@changeStatus');
    Route::get('faq/view/{id}', 'FaqController@show');

    Route::resource('api/faq_request', 'FaqRequestController');
    Route::get('faq_request', 'FaqRequestController@frontend')->name('faq_request');
    Route::get('faq_request/edit/{id}', 'FaqRequestController@edit_frontend');
    Route::get('faq_request/changeStatus/{id}/{status}', 'FaqRequestController@changeStatus');
    Route::get('faq_request/view/{id}', 'FaqRequestController@show');


    Route::resource('api/main_category', 'MainCategoryController');
    Route::get('main_category', 'MainCategoryController@frontend')->name('main_category');
    Route::get('main_category/edit/{id}', 'MainCategoryController@edit_frontend');
    Route::get('main_category/changeStatus/{id}/{status}', 'MainCategoryController@changeStatus');
    Route::get('main_category/view/{id}', 'MainCategoryController@show');
    Route::get('main_category/exportUsers/{slug}', 'MainCategoryController@exportUsers');
    Route::post('main_category/ImportUsers', 'MainCategoryController@importUsers');

    Route::resource('api/info', 'InfoController');
    Route::get('info', 'InfoController@frontend')->name('info');
    Route::get('info/edit/{id}', 'InfoController@edit_frontend');
    Route::get('info/changeStatus/{id}/{status}', 'InfoController@changeStatus');
    Route::get('info/view/{id}', 'InfoController@show');
    Route::get('info/exportUsers/{slug}', 'InfoController@exportUsers');
    Route::post('info/ImportUsers', 'InfoController@importUsers');

    Route::resource('api/gift_category', 'GiftCategoriesController');
    Route::get('gift_category', 'GiftCategoriesController@frontend')->name('gift_category');
    Route::get('gift_category/edit/{id}', 'GiftCategoriesController@edit_frontend');
    Route::get('gift_category/changeStatus/{id}/{status}', 'GiftCategoriesController@changeStatus');
    Route::get('gift_category/view/{id}', 'GiftCategoriesController@show');
    Route::get('gift_category/exportUsers/{slug}', 'GiftCategoriesController@exportUsers');
    Route::post('gift_category/ImportUsers', 'GiftCategoriesController@importUsers');

    Route::resource('api/emails', 'EmailTemplateController');
    Route::get('emails', 'EmailTemplateController@frontend')->name('emails');
    Route::get('emails/edit/{id}', 'EmailTemplateController@edit_frontend');
    Route::get('emails/changeStatus/{id}/{status}', 'EmailTemplateController@changeStatus');
    Route::get('emails/view/{id}', 'EmailTemplateController@show');

    Route::resource('api/topping_category', 'ToppingCategoryController');
    Route::get('topping_category', 'ToppingCategoryController@frontend')->name('topping_category');
    Route::get('topping_category/edit/{id}', 'ToppingCategoryController@edit_frontend');
    Route::get('topping_category/changeStatus/{id}/{status}', 'ToppingCategoryController@changeStatus');
    Route::get('topping_category/view/{id}', 'ToppingCategoryController@show');
    Route::get('topping_category/exportUsers/{slug}', 'ToppingCategoryController@exportUsers');
    Route::post('topping_category/ImportUsers', 'ToppingCategoryController@importUsers');

    Route::resource('api/topping', 'ToppingController');
    Route::get('topping', 'ToppingController@frontend')->name('topping');
    Route::get('topping/create', 'ToppingController@create');
    Route::get('topping/edit/{id}', 'ToppingController@edit_frontend');
    Route::get('topping/changeStatus/{id}/{status}', 'ToppingController@changeStatus');
    Route::get('topping/view/{id}', 'ToppingController@show');
    Route::get('topping/show_category/{id}/{category_id?}', 'ToppingController@show_category');
    Route::get('topping/show_dishes/{id}/{dish_id?}', 'ToppingController@show_dishes');
    Route::get('topping/show_attributes/{id}/{category_id}/{dish_id?}', 'ToppingController@show_attributes');
    Route::get('topping/show_single_attributes/{id}/{category_id}/{option_type}/{dish_id?}', 'ToppingController@show_single_attributes');
    Route::get('topping/show_attribute_values/{id}/{category_id}/{count}/{customizeType?}', 'ToppingController@show_attribute_values');
    Route::get('topping/exportUsers/{slug}', 'ToppingController@exportUsers');
    Route::post('topping/ImportUsers', 'ToppingController@importUsers');
    Route::get('topping/show_productsByMainCatIds/{id?}', 'ToppingController@show_productsByMainCatIds');
    Route::get('topping/show_category_byIds/{id?}', 'ToppingController@show_category_byIds');
    Route::get('topping/import/', 'ToppingController@import');
    Route::get('topping/show_category_popup/{id}/{category_id?}', 'ToppingController@show_category_popup');
    Route::get('topping/exportSampleFileForSpecifics/{main_category_id}/{category_id}', 'ToppingController@exportSampleFileForSpecifics');

    Route::resource('api/gift_topping', 'GiftToppingController');
    Route::get('gift_topping', 'GiftToppingController@frontend')->name('gift_topping');
    Route::get('gift_topping/create', 'GiftToppingController@create');
    Route::get('gift_topping/edit/{id}', 'GiftToppingController@edit_frontend');
    Route::get('gift_topping/changeStatus/{id}/{status}', 'GiftToppingController@changeStatus');
    Route::get('gift_topping/view/{id}', 'GiftToppingController@show');
    Route::get('gift_topping/show_category/{id}/{category_id?}', 'GiftToppingController@show_category');
    Route::get('gift_topping/show_dishes/{id}/{gift_id?}', 'GiftToppingController@show_dishes');
    Route::get('gift_topping/show_attributes/{id}/{category_id}/{gift_id?}', 'GiftToppingController@show_attributes');
    Route::get('gift_topping/show_single_attributes/{id}/{category_id}/{option_type}/{gift_id?}', 'GiftToppingController@show_single_attributes');
    Route::get('gift_topping/show_attribute_values/{id}/{category_id}/{count}/{customizeType?}', 'GiftToppingController@show_attribute_values');
    Route::get('gift_topping/exportUsers/{slug}', 'GiftToppingController@exportUsers');
    Route::post('gift_topping/ImportUsers', 'GiftToppingController@importUsers');
    Route::get('gift_topping/show_productsByMainCatIds/{id?}', 'GiftToppingController@show_productsByMainCatIds');
    Route::get('gift_topping/show_category_byIds/{id?}', 'GiftToppingController@show_category_byIds');
    Route::get('gift_topping/import/', 'GiftToppingController@import');
    Route::get('gift_topping/show_category_popup/{id}/{category_id?}', 'GiftToppingController@show_category_popup');
    Route::get('gift_topping/exportSampleFileForSpecifics/{main_category_id}/{category_id}', 'GiftToppingController@exportSampleFileForSpecifics');

    Route::resource('api/holiday', 'HolidayController');
    Route::get('holiday', 'HolidayController@frontend')->name('holiday');
    Route::get('holiday/edit/{id}', 'HolidayController@edit_frontend');
    Route::get('holiday/changeStatus/{id}/{status}', 'HolidayController@changeStatus');
    Route::get('holiday/view/{id}', 'HolidayController@show');
    Route::get('holiday/exportUsers/{slug}', 'HolidayController@exportUsers');
    Route::post('holiday/ImportUsers', 'HolidayController@importUsers');


    // Route::resource('api/banner', 'BannerController');
    // Route::get('banner', 'BannerController@frontend')->name('banner');
    // Route::get('banner/edit/{id}', 'BannerController@edit_frontend');
    // Route::get('banner/show_category/{type}/{main_category_id}/{id?}', 'BannerController@show_category');
    // Route::get('banner/exportUsers/{slug}', 'BannerController@exportUsers');
    // Route::delete('api/banner/{id?}', 'BannerController@destroy')->name('banner.delete');
    // Route::post('banner/ImportUsers', 'BannerController@importUsers');

    Route::resource('api/gift_banner', 'GiftBannerController');
    Route::get('gift_banner', 'GiftBannerController@frontend')->name('gift_banner');
    Route::get('gift_banner/edit/{id}', 'GiftBannerController@edit_frontend');
    /*Route::get('gift_banner/view/{id}', 'GiftBannerController@show');*/
    Route::get('gift_banner/exportUsers/{slug}', 'GiftBannerController@exportUsers');
    Route::post('gift_banner/ImportUsers', 'GiftBannerController@importUsers');

    Route::resource('api/discount', 'DiscountController');
    Route::get('discount', 'DiscountController@frontend')->name('discount');
    Route::get('discount/edit/{id}', 'DiscountController@edit_frontend');
    Route::get('discount/changeStatus/{id}/{status}', 'DiscountController@changeStatus');
    Route::get('discount/show_category/{type}/{id?}', 'DiscountController@show_category');
    Route::get('discount/view/{id}', 'DiscountController@show');
    Route::get('discount/user_details/{id}', 'DiscountController@show_user')->name('user_details');
    Route::get('discount/exportUsers/{slug}', 'DiscountController@exportUsers');
    Route::post('discount/ImportUsers', 'DiscountController@importUsers');
    Route::get('discount/exportAppliedUsers/{id}', 'DiscountController@exportAppliedUsers');


    Route::resource('api/order_report', 'OrderReportController');
    Route::get('order_report', 'OrderReportController@frontend')->name('order_report');
    Route::get('order_report/edit/{id}', 'OrderReportController@edit_frontend');
    Route::get('order_report/changeStatus/{id}/{status}', 'OrderReportController@changeStatus');
    Route::get('order_report/show_category/{type}/{id?}', 'OrderReportController@show_category');
    Route::get('order_report/view/{id}', 'OrderReportController@show');


    Route::resource('api/gift-category', 'GiftCategoryController');
    Route::get('gift-category', 'GiftCategoryController@frontend')->name('gift-category');
    Route::get('gift-category/edit/{id}', 'GiftCategoryController@edit_frontend');
    Route::get('gift-category/changeStatus/{id}/{status}', 'GiftCategoryController@changeStatus');
    Route::get('gift-category/view/{id}', 'GiftCategoryController@show');
    Route::get('gift-category/exportUsers/{slug}', 'GiftCategoryController@exportUsers');
    Route::post('gift-category/ImportUsers', 'GiftCategoryController@importUsers');

    Route::resource('api/inventory', 'InventoryController');
    Route::get('inventory', 'InventoryController@frontend')->name('inventory');
    Route::get('inventory/edit/{id}', 'InventoryController@edit_frontend');
    Route::get('inventory/get_gifts/{id}', 'InventoryController@get_gifts');
    Route::get('inventory/changeStatus/{id}/{status}', 'InventoryController@changeStatus');
    Route::get('inventory/view/{id}', 'InventoryController@show');
    Route::get('inventory/exportUsers/{slug}', 'InventoryController@exportUsers');
    Route::post('inventory/ImportUsers', 'InventoryController@importUsers');

    Route::resource('api/gift', 'GiftController');
    Route::get('gift', 'GiftController@frontend')->name('gift');
    Route::get('gift/add', 'GiftController@giftAdd')->name('gift.add');
    Route::get('gift/edit/{id?}', 'GiftController@edit_frontend')->name('gift.edit');
    Route::get('gift/changeStatus/{id}/{status}', 'GiftController@changeStatus');

    Route::get('gift/changeStatus/{id}/{status}', 'GiftController@changeStatus');
    Route::get('/gift/imageView/{u_id}', 'GiftController@imageView');
    Route::delete('/gift/giftImagesDelete/{del_id}', 'GiftController@giftImagesDelete');
    Route::get('gift/view/{id}', 'GiftController@show');
    Route::get('gift/barcode/{id}', 'GiftController@show');
    Route::get('gift/show_subcategory/{id}/{sub_category_id?}', 'GiftController@show_subcategory');
    Route::put('gift/add-more-images/{id}', 'GiftController@addMoreImages');
    Route::put('gift/add_sku/{id}', 'GiftController@addSKU');
    Route::get('gift/exportUsers/{slug}', 'GiftController@exportUsers');
    Route::post('gift/importData', 'GiftController@importData');


    Route::resource('api/product', 'ProductController');
    Route::get('product', 'ProductController@frontend')->name('product');
    Route::get('product/create', 'ProductController@create');
    Route::get('product/edit/{id}', 'ProductController@edit_frontend');
    Route::get('product/rating/{id}', 'ProductController@ratingList');
    Route::get('product/changeStatus/{id}/{status}', 'ProductController@changeStatus');
    Route::get('/product/imageView/{u_id}', 'ProductController@imageView');
    Route::delete('/product/productImagesDelete/{del_id}', 'ProductController@productImagesDelete');
    Route::get('product/view/{id}', 'ProductController@show');
    Route::get('product/import/', 'ProductController@import');
    Route::get('product/exportSampleFileForImport/{mainCatId}/{brand_id}', 'ProductController@exportSampleFileForImport');
    Route::get('product/import_new/{id}', 'ProductController@import_new');
    Route::put('product/add-more-images/{id}', 'ProductController@addMoreImages');
    Route::get('product/exportUsers/{slug}', 'ProductController@exportUsers');
    Route::post('product/importData', 'ProductController@importData');
    Route::get('product/show_attribute_value/{id?}', 'ProductController@show_attribute_value');
    Route::get('product/show_restro_category/{restro_id}/{id?}', 'ProductController@show_restro_category');
    Route::get('product/show_restro/{id}/{brand_id}/{restaurant_id?}', 'ProductController@show_restro');
    Route::get('product/show_restro_byMainCatIds/{id?}', 'ProductController@show_restro_byMainCatIds');
    Route::get('product/show_brands/{id}/{brand_id?}', 'ProductController@show_brands');
    Route::get('product/showTags', 'ProductController@showTags');
    Route::get('product/showTagsAr', 'ProductController@showTagsAr');

    Route::match(['get', 'post'], 'search', 'ProductController@fetch_data')->name('product.filter');



    Route::get('api/orders/{status?}', 'OrdersController@index');
    Route::get('orders', 'OrdersController@frontend')->name('orders');
    Route::get('orders/changeOrderStatus/{id}/{status}', 'OrdersController@changeOrderStatus');
    Route::get('orders/changePaymentStatus/{id}/{status}', 'OrdersController@changePaymentStatus');
    Route::get('orders/changeJoinerPaymentStatus/{id}/{status}', 'OrdersController@changeJoinerPaymentStatus');
    Route::post('orders/cancelOrderStatus', 'OrdersController@cancelOrderStatus');
    Route::get('orders/view/{id}', 'OrdersController@show');
    Route::get('orders/pdf/{id}', 'OrdersController@pdf');
    Route::get('orders/history/view/{id}', 'OrdersController@show');
    Route::get('orders/show_restro_byMainCatIds/{id?}', 'OrdersController@show_restro_byMainCatIds');
    Route::get('orders/exportOrders', 'OrdersController@exportOrders');
    Route::get('orders/cash_confirm/{id}', 'OrdersController@cash_confirm');
    Route::get('orders/show_court_data/{facility_id}/{court_id?}', 'OrdersController@show_court_data');
    Route::get('orders/delete_orders/{id}/{is_delete}', 'OrdersController@delete_orders');

    Route::get('api/cash-settlement/{status?}', 'CashSettlementController@index');
    Route::get('cash-settlement', 'CashSettlementController@frontend')->name('cash-settlement');
    Route::get('cash-settlement/changeOrderStatus/{id}/{status}', 'CashSettlementController@changeOrderStatus');
    Route::post('cash-settlement/cancelOrderStatus', 'CashSettlementController@cancelOrderStatus');
    Route::get('cash-settlement/view/{id}', 'CashSettlementController@show');
    Route::get('cash-settlement/pdf/{id}', 'CashSettlementController@pdf');
    Route::get('cash-settlement/history/view/{id}', 'CashSettlementController@show');
    Route::get('cash-settlement/show_restro_byMainCatIds/{id?}', 'CashSettlementController@show_restro_byMainCatIds');
    Route::get('cash-settlement/exportOrders', 'CashSettlementController@exportOrders');
    Route::get('cash-settlement/cash_confirm/{id}', 'CashSettlementController@cash_confirm');
    Route::get('cash-settlement/show_court_data/{facility_id}/{court_id?}', 'CashSettlementController@show_court_data');
    Route::post('cash-settlement/getAmountData', 'CashSettlementController@getAmountData');



    Route::get('api/gift_orders/{status}', 'GiftOrdersController@index');
    Route::get('gift_orders/{status}', 'GiftOrdersController@frontend')->name('gift_orders');
    Route::get('gift_orders/changeOrderStatus/{id}/{status}', 'GiftOrdersController@changeOrderStatus');
    Route::get('gift_orders/view/{id}', 'GiftOrdersController@show');
    Route::post('gift_orders/cancelOrderStatus', 'GiftOrdersController@cancelOrderStatus');

    Route::get('api/earning', 'EarningController@index');
    Route::get('earning', 'EarningController@frontend')->name('earning');
    Route::get('earning/view/{id}', 'EarningController@show');

    Route::get('api/settlement', 'SettlementController@index');
    Route::get('settlement', 'SettlementController@frontend')->name('settlement');
    Route::get('settlement/view/{id}', 'SettlementController@show');
    Route::get('settlement/cash-received', 'SettlementController@cashReceived');
    Route::any('settlement/saveCashRecevied', 'SettlementController@saveCashRecevied');
    Route::get('settlement/exportUsers/{slug}', 'SettlementController@exportUsers');


    Route::resource('api/notifications', 'NotificationController');
    Route::get('notifications', 'NotificationController@frontend')->name('notifications');
    Route::get('notifications/getNotificationData', 'NotificationController@getNotificationData')->name('getNotificationData');
    Route::get('notifications/readNotification/{id}', 'NotificationController@readNotification')->name('readNotification');
    Route::get('notifications/clearAllNotification', 'NotificationController@clearAllNotification')->name('clearAllNotification');
    Route::get('notifications/show_type_data/{type}/{type_id?}', 'NotificationController@show_type_data');


    Route::resource('api/brands', 'BrandController');
    Route::get('brands', 'BrandController@frontend')->name('brands');
    Route::get('brands/edit/{id}', 'BrandController@edit_frontend');
    Route::get('brands/changeStatus/{id}/{status}', 'BrandController@changeStatus');
    Route::get('brands/view/{id}', 'BrandController@show');
    Route::get('brands/restaurant/{id}', 'BrandController@restaurant_list');
    Route::get('brands/exportUsers/{slug}', 'BrandController@exportUsers');
    Route::post('brands/ImportUsers', 'BrandController@importUsers');

    Route::resource('api/brand_category', 'BrandCategoryController');
    Route::get('brand_category', 'BrandCategoryController@frontend')->name('brand_category');
    Route::get('brand_category/edit/{id}', 'BrandCategoryController@edit_frontend');
    Route::get('brand_category/changeStatus/{id}/{status}', 'BrandCategoryController@changeStatus');
    Route::get('brand_category/view/{id}', 'BrandCategoryController@show');
    Route::get('brand_category/exportUsers/{slug}', 'BrandCategoryController@exportUsers');
    Route::post('brand_category/ImportUsers', 'BrandCategoryController@importUsers');
    Route::get('brand_category/import', 'BrandCategoryController@import');
    Route::post('brand_category/importData', 'BrandCategoryController@importData');

    Route::resource('api/tax', 'TaxController');
    Route::get('tax', 'TaxController@frontend')->name('tax');
    Route::get('tax/edit/{id}', 'TaxController@edit_frontend');
    Route::get('tax/changeStatus/{id}/{status}', 'TaxController@changeStatus');
    Route::get('tax/view/{id}', 'TaxController@show');

    Route::resource('api/gift_brand', 'GiftBrandController');
    Route::get('gift_brand', 'GiftBrandController@frontend')->name('gift_brand');
    Route::get('gift_brand/edit/{id}', 'GiftBrandController@edit_frontend');
    Route::get('gift_brand/changeStatus/{id}/{status}', 'GiftBrandController@changeStatus');
    Route::get('gift_brand/view/{id}', 'GiftBrandController@show');
    Route::get('gift_brand/brand_gift/{id}', 'GiftBrandController@brand_gifts_list');
    Route::get('gift_brand/exportUsers/{slug}', 'GiftBrandController@exportUsers');
    Route::post('gift_brand/ImportUsers', 'GiftBrandController@importUsers');

    Route::resource('api/cancel_reason', 'CancelReasionController');
    Route::get('cancel_reason', 'CancelReasionController@frontend')->name('cancel_reason');
    Route::get('cancel_reason/edit/{id}', 'CancelReasionController@edit_frontend');
    Route::get('cancel_reason/changeStatus/{id}/{status}', 'CancelReasionController@changeStatus');
    Route::get('cancel_reason/view/{id}', 'CancelReasionController@show');

    Route::resource('api/restaurant', 'RestaurantController');
    Route::get('restaurant', 'RestaurantController@frontend')->name('restaurant');
    Route::get('restaurant/edit/{id}', 'RestaurantController@edit_frontend');
    Route::get('restaurant/create', 'RestaurantController@create');
    Route::get('restaurant/changeStatus/{id}/{status}', 'RestaurantController@changeStatus');
    Route::get('restaurant/view/{id}', 'RestaurantController@show');
    Route::get('restaurant/set_time/{id}', 'RestaurantController@set_time');
    Route::post('restaurant/time_update/{id}', 'RestaurantController@time_update');
    Route::put('restaurant/add-more-images/{id}', 'RestaurantController@addMoreImages');
    Route::delete('/restaurant/restaurantImagesDelete/{del_id}', 'RestaurantController@restaurantImagesDelete');
    Route::get('/restaurant/imageView/{u_id}', 'RestaurantController@imageView');
    Route::get('restaurant/menu/{id}', 'RestaurantController@showmenu');
    Route::get('restaurant/transaction/{id}', 'RestaurantController@showtransaction');
    Route::get('restaurant/deliver_order/{id}', 'RestaurantController@deliver_order');
    Route::get('restaurant/makeMainRestro/{id}/{status}', 'RestaurantController@makeMainRestro');
    Route::get('restaurant/exportUsers/{slug}', 'RestaurantController@exportUsers');
    Route::get('restaurant/exportTransUsers/{slug}', 'RestaurantController@exportTransUsers');
    Route::post('restaurant/ImportUsers', 'RestaurantController@importUsers');
    Route::get('restaurant/show_brands/{id}/{brand_id?}', 'RestaurantController@show_brands');
    Route::get('restaurant/show_brands_for_list/{id}', 'RestaurantController@show_brands_for_list');
    Route::get('restaurant/tables/{id}', 'RestaurantController@showtables');
    Route::post('restaurant/table_update/{id}', 'RestaurantController@table_update');
    Route::post('restaurant/importData', 'RestaurantController@importData');
    Route::post('restaurant/changeRestroOnOff', 'RestaurantController@changeRestroOnOff');


    Route::resource('api/cash-register', 'CashRegisterController');
    Route::get('cash-register', 'CashRegisterController@frontend')->name('cash-register');
    Route::get('cash-register/cash-received', 'CashRegisterController@cashReceived');
    Route::post('cash-register/saveCashRecevied', 'CashRegisterController@saveCashRecevied');
    Route::get('cash-register/exportUsers/{slug}', 'CashRegisterController@exportUsers');
    Route::post('cash-register/ImportUsers', 'CashRegisterController@importUsers');


    Route::get('price-settings', 'PriceSettingsController@frontend')->name('price-settings');
    Route::post('price-save/{id}', 'PriceSettingsController@update');

    Route::get('/roles_list', 'RoleController@roles_list');
    Route::get('/allroles', 'RoleController@allroles');

    Route::resource('api/attribute', 'AttributeController');
    Route::get('attribute', 'AttributeController@frontend')->name('attribute');
    Route::get('attribute/create', 'AttributeController@create');
    Route::get('attribute/edit/{id}', 'AttributeController@edit_frontend');
    Route::get('attribute/changeStatus/{id}/{status}', 'AttributeController@changeStatus');
    Route::get('attribute/view/{id}', 'AttributeController@show');
    Route::get('attribute/show_category/{id}/{category_id?}', 'AttributeController@show_category');
    Route::get('attribute/show_category_byIds/{id?}', 'AttributeController@show_category_byIds');
    Route::get('attribute/import', 'AttributeController@import');
    Route::post('attribute/importData', 'AttributeController@importData');

    Route::resource('api/attribute_value', 'AttributeValueController');
    Route::get('attribute_value', 'AttributeValueController@frontend')->name('attribute_value');
    Route::get('attribute_value/create', 'AttributeValueController@create');
    Route::get('attribute_value/edit/{id}', 'AttributeValueController@edit_frontend');
    Route::get('attribute_value/changeStatus/{id}/{status}', 'AttributeValueController@changeStatus');
    Route::get('attribute_value/view/{id}', 'AttributeValueController@show');
    Route::get('attribute_value/show_category/{id}/{category_id?}', 'AttributeValueController@show_category');
    Route::get('attribute_value/show_attributes/{id}/{category_id}', 'AttributeValueController@show_attributes');

    Route::resource('api/gift_attribute', 'GiftAttributeController');
    Route::get('gift_attribute', 'GiftAttributeController@frontend')->name('gift_attribute');
    Route::get('gift_attribute/create', 'GiftAttributeController@create');
    Route::get('gift_attribute/edit/{id}', 'GiftAttributeController@edit_frontend');
    Route::get('gift_attribute/changeStatus/{id}/{status}', 'GiftAttributeController@changeStatus');
    Route::get('gift_attribute/view/{id}', 'GiftAttributeController@show');
    Route::get('gift_attribute/show_category/{id}/{category_id?}', 'GiftAttributeController@show_category');
    Route::get('gift_attribute/show_category_byIds/{id?}', 'GiftAttributeController@show_category_byIds');
    Route::get('gift_attribute/import', 'GiftAttributeController@import');
    Route::post('gift_attribute/importData', 'GiftAttributeController@importData');

    Route::resource('api/gift_attribute_value', 'GiftAttributeValueController');
    Route::get('gift_attribute_value', 'GiftAttributeValueController@frontend')->name('gift_attribute_value');
    Route::get('gift_attribute_value/create', 'GiftAttributeValueController@create');
    Route::get('gift_attribute_value/edit/{id}', 'GiftAttributeValueController@edit_frontend');
    Route::get('gift_attribute_value/changeStatus/{id}/{status}', 'GiftAttributeValueController@changeStatus');
    Route::get('gift_attribute_value/view/{id}', 'GiftAttributeValueController@show');
    Route::get('gift_attribute_value/show_category/{id}/{category_id?}', 'GiftAttributeValueController@show_category');
    Route::get('gift_attribute_value/show_attributes/{id}/{category_id}', 'GiftAttributeValueController@show_attributes');

    Route::get('/permissions', 'PermissionController@index')->name('permissions');

    Route::get('/getRole', 'PermissionController@getRole');
    Route::get('/getPermissions/{id?}', 'PermissionController@getPermissions');
    Route::get('savePermission/{permission_id}/{role_id}', 'PermissionController@savePermission');
    Route::get('deletePermission/{permission_id}/{role_id}', 'PermissionController@deletePermission');
    Route::get('saveUserPermission/{permission_id}/{user_id}', 'PermissionController@saveUserPermission');
    Route::get('deleteUserPermission/{permission_id}/{user_id}', 'PermissionController@deleteUserPermission');

    Route::get('settings', 'SettingController@frontend')->name('settings');
    Route::post('/changePassword', 'SettingController@changePassword');
    Route::post('/sendVerificationLink', 'SettingController@sendVerificationLink');
    Route::post('/saveProfile', 'SettingController@saveProfile');
    Route::post('/updateProfile', 'SettingController@updateRestroProfile');
    Route::get('/reset_email/{userid}/{token}', 'SettingController@emailUpdate')->name('reset.email');
    Route::get('permissions/user_listing', 'PermissionController@perm_userData')->name('ajax.permUserdata');
    Route::get('permissions/user_permissions/{id}', 'PermissionController@user_permissions');
    Route::get('permissions/add_role_permission/{r_id}/{p_name}', 'PermissionController@saveRolePermission');
    Route::get('permissions/delete_role_permission/{r_id}/{p_name}', 'PermissionController@deleteRolePermission');
    Route::get('permissions/add_user_permission/{u_id}/{p_name}', 'PermissionController@saveUserPermission');
    Route::get('permissions/delete_user_permission/{u_id}/{p_name}', 'PermissionController@deleteUserPermission');
    // forgot password
    Route::get('facility-owner-change-password', [ForgotPasswordController::class, 'showResetPasswordFormFacilityOwner'])->name('change.password.get');
    Route::post('facility-owner-change-password', [ForgotPasswordController::class, 'submitResetPasswordFormFacilityOwner'])->name('change.password.post');
});

Route::get('/login', function () {
    return redirect()->route('web.home');
});
Route::get('/', 'Web\HomeController@index')->name('web.home');
Route::get('/court', 'Web\HomeController@courts')->name('web.court_list');
Route::post('/court-list-search', 'Web\HomeController@courts_search')->name('web.courts_search');
Route::post('/court-list-filter', 'Web\HomeController@courts_filter')->name('web.court_list_filter');
// Route::get('/court-list-sort/{sort}', 'Web\HomeController@courts_filter')->name('web.court_list_sort');
Route::get('court-list-pagination', 'Web\HomeController@courts_pagination')->name('courts_pagination');
Route::get('/about-us', 'Web\HomeController@about_us')->name('web.about_us');
Route::get('/terms_and_conditions', 'Web\HomeController@terms_and_conditions')->name('web.terms_and_conditions');
Route::get('/how_it_works', 'Web\HomeController@how_it_works')->name('web.how_it_works');
Route::get('/private_policy', 'Web\HomeController@private_policy')->name('web.private_policy');
Route::get('/payment_confirmation', 'Web\HomeController@payment_confirmation')->name('web.payment_confirmation');
Route::get('/refund_policy', 'Web\HomeController@refund_policy')->name('web.refund_policy');
Route::get('/cancellation_policy', 'Web\HomeController@cancellation_policy')->name('web.cancellation_policy');
Route::get('/contact-us', 'Web\HomeController@contact_us')->name('web.contact_us');
Route::post('/set_to_court_favourate', 'HomeController@set_to_court_favourate')->name('web.set_to_court_favourate');
Route::post('/set_to_facility_favourate', 'HomeController@set_to_facility_favourate')->name('web.set_to_facility_favourate');


Route::get('/facility', 'Web\HomeController@facility')->name('web.facility');
Route::post('/facility-list-filter', 'Web\HomeController@facility_filter')->name('web.facility_list_filter');
// Route::get('/facility-list-sort/{sort}', 'Web\HomeController@facility_filter')->name('web.facility_list_sort');
Route::get('facility-list-pagination', 'Web\HomeController@facility_pagination')->name('facility_pagination');
Route::get('/facility_detail/{id}', 'Web\HomeController@facility_detail')->name('web.facility_detail');
Route::post('/admin/contact-us', 'Web\HomeController@admin_contact')->name('web.admin.contact');

// login route
Route::post('/web_login', 'Web\AuthController@login')->name('web.login');
Route::post('/send-otp', 'Web\AuthController@send_otp')->name('web.send_otp');
Route::post('/resend-otp', 'Web\AuthController@resend_otp')->name('web.resend_otp');
Route::post('/verify-otp', 'Web\AuthController@verify_otp')->name('web.verify_otp');
Route::post('/set-password', 'Web\AuthController@set_password')->name('web.set_password');
Route::post('/reset-password', 'Web\AuthController@reset_password')->name('web.reset_password');
Route::group(['middleware' => 'auth.web'], function () {
    Route::get('/challenges', 'Web\HomeController@challenges')->name('web.challenges');
    Route::get('challenges-list-pagination', 'Web\HomeController@challenges_pagination')->name('challenges_pagination');
    Route::get('/challenges_detail/{id}', 'Web\HomeController@challenges_detail')->name('web.challenges_detail');
    Route::get('/create_challenge/{id}', 'Web\HomeController@create_challenge')->name('web.create_challenge');
    Route::get('/change_password', 'Web\HomeController@change_password')->name('web.change_password');
    Route::post('/change_password_submit', 'Web\HomeController@change_password_submit')->name('web.change_password_submit');
    Route::get('/completed_booking', 'Web\HomeController@completed_booking')->name('web.completed_booking');
    Route::get('completed_booking-pagination', 'Web\HomeController@completed_booking_pagination')->name('completed_booking_pagination');
    Route::get('/cancelled_booking', 'Web\HomeController@cancelled_booking')->name('web.cancelled_booking');
    Route::get('cancelled_booking-pagination', 'Web\HomeController@cancelled_booking_pagination')->name('cancelled_booking_pagination');
    Route::get('/favourate_court', 'Web\HomeController@favourate_court')->name('web.favourate_court');
    Route::get('/favourate_facility', 'Web\HomeController@favourate_facility')->name('web.favourate_facility');
    Route::get('/my_account', 'Web\HomeController@my_account')->name('web.my_account');
    Route::post('/book-court/checkout', 'Web\HomeController@bookCourtCheckout')->name('web.book_court_checkout');
    Route::post('/book-court', 'Web\HomeController@bookCourt')->name('web.book_court');
    Route::get('/check-booked-timeslot/{booking_date}/{id}', 'Web\HomeController@checkBookedTimeslot')->name('web.check.booked.timeslot');
    Route::get('/upcoming_booking', 'Web\HomeController@upcoming_booking')->name('web.upcoming_booking');
    Route::get('/court-booking-cancel/{id}', 'Web\HomeController@court_booking_cancel')->name('web.court_booking_cancel');
     Route::get('/court-challenge-booking-cancel/{id}', 'Web\HomeController@court_challenge_booking_cancel')->name('web.court_challenge_booking_cancel');
    Route::POST('/join-challenge/checkout', 'Web\HomeController@join_challenge_checkout')->name('web.join_challenge_checkout');
    Route::POST('/join-challenge', 'Web\HomeController@join_challenge')->name('web.join_challenge');
    Route::POST('/get-player-list', 'Web\HomeController@playerList')->name('web.get.player.list');
    Route::POST('/invite-player', 'Web\HomeController@invitePlayer')->name('web.invite.player');
    Route::POST('/create_review', 'Web\HomeController@create_review')->name('web.create_review');
    Route::get('web/logout', 'Web\AuthController@web_logout')->name('web.logout');
    Route::get('thank-you', 'Web\HomeController@thankYou')->name('web.thank.you');
    Route::get('/print_out', 'Web\HomeController@printOut')->name('web.print_out');
    Route::get('notifications/readNotification/{id}', 'Web\HomeController@readNotification')->name('web.readNotification');
    Route::get('notifications/clearAllNotification', 'Web\HomeController@clearAllNotification')->name('web.clearAllNotification');
    Route::get('/court_detail/{id}/{slug?}', 'Web\HomeController@court_detail')->name('web.court_detail');
    Route::get('/court_detail_data/{id}', 'Web\HomeController@court_detail_data')->name('web.court_detail_data');
    Route::get('notifications/getNotificationData/player', 'Web\HomeController@getNotificationDataPlayer')->name('getNotificationDataPlayer');

   
    
});

    Route::get('change_order_status_auto', 'Web\AuthController@changeOrderAuto');
    Route::get('/payment', [PaymentController::class,'payment'])->name('payment');
    Route::get('/payment-curl', [PaymentController::class,'paymentWithCurl'])->name('payment-curl');
    Route::get('/handle-payment/success',[PaymentController::class,'paymentSuccess'])->name('payment-success-web');
    Route::get('/handle-payment/cancel',[PaymentController::class,'paymentCancel'])->name('payment-cancel-web');
    Route::get('/handle-payment/declined',[PaymentController::class,'paymentDeclined'])->name('payment-declined-web');
    Route::get('/handle-payment/success_api',[PaymentController::class,'paymentSuccessApi'])->name('payment-success-api');
    Route::get('/store-session',[PaymentController::class,'storeSession']);
    Route::get('/handle-payment-api/success',[PaymentController::class,'paymentSuccess'])->name('payment-success');
    Route::get('/handle-payment-api/cancel',[PaymentController::class,'paymentCancel'])->name('payment-cancel');
    Route::get('/handle-payment-api/declined',[PaymentController::class,'paymentDeclined'])->name('payment-declined');


