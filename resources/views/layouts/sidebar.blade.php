<?php

use App\Models\Restaurant;

$login_user_data = auth()->user();
$restroId = '';

if ($login_user_data->type == 4) {
    $restaurant_detail = Restaurant::select('name', 'id', 'user_id')->where(['user_id' => $login_user_data->id])->first();

    if ($restaurant_detail) {
        $restroId = $restaurant_detail->id;
    }
}
?>
<aside class="left-sidebar" style="background-color:white">
    <div class="d-flex no-block nav-text-box align-items-center logo_sec">
        <span><img src="{{ URL::asset('assets/images/logo-light-text-small.svg')}}" alt="elegant admin template"></span>
        <a class="nav-lock waves-effect waves-dark ml-auto hidden-md-down" href="javascript:void(0)"><i class="mdi mdi-toggle-switch"></i></a>
        <a class="nav-toggler waves-effect waves-dark ml-auto hidden-sm-up" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
    </div>
    <!-- Sidebar scroll-->
    <div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li>
                    <a class="waves-effect waves-dark {{ (Request::is('*/dashboard') ? 'active':'') }}" href="{{ url('admin/') }}" aria-expanded="false">
                        <span class="head-icon">
                            <!-- <img src="{{ URL::asset('assets/images/dashboard.svg')}}"> -->
                            <i class="fa fa-tachometer-alt"></i>
                        </span>
                        <span class="hide-menu">{{ __('backend.dashboard') }}
                            <!-- <span class="badge badge-pill badge-cyan">4</span> -->
                        </span>
                    </a>
                </li>
                @can('Player-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Request::is('*/players/*') ? 'active':'') }}" href="{{ route('admin.players') }}">
                        <span class="head-icon">
                            <!-- <img src="{{ URL::asset('assets/images/booking.svg')}}"> -->
                            <i class="fas fa-users"></i>
                        </span>
                        {{ __('backend.Player_Manager') }}
                    </a>
                </li>
                @endcan
                @can('Facility_owner-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Request::is('*/facility_owner/*') ? 'active':'') }}" href="{{ route('admin.facility_owner') }}">
                        <span class="head-icon">
                            <!-- <img src="{{ URL::asset('assets/images/booking.svg')}}"> -->
                            <i class="fas fa-user"></i>
                        </span>
                        {{ __('backend.Facility_Owner_Manager') }}
                    </a>
                </li>
                @endcan
                @can('User_bank_detail-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Request::is('*/user_bank_detail/*') ? 'active':'') }}" href="{{ route('admin.user_bank_detail') }}">
                        <span class="head-icon">
                        <i class="fas fa-info-circle"></i>
                        </span>
                        {{ __('backend.User_bank_detail_Manager') }}
                    </a>
                </li>
                @endcan

                @can('Amenity-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Request::is('*/amenities/*') ? 'active':'') }}" href="{{ route('admin.amenities') }}">
                        <span class="head-icon">
                            <!-- <img src="{{ URL::asset('assets/images/booking.svg')}}"> -->
                            <i class="fab fa-servicestack"></i>
                        </span>
                        {{ __('backend.Amenity_Manager') }}
                    </a>
                </li>
                @endcan
                @can('Facility-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Request::is('*/facilities/*') ? 'active':'') }}" href="{{ route('admin.facilities') }}">
                        <span class="head-icon">
                            <!-- <img src="{{ URL::asset('assets/images/booking.svg')}}"> -->
                            <i class="fa fa-building" aria-hidden="true"></i>
                        </span>
                        {{ __('backend.Facility_Manager') }}
                    </a>
                </li>
                @endcan
                @can('Commission-section')
                <!-- <li>
                    <a class="waves-effect waves-dark {{ (Request::is('*/commission/*') ? 'active':'') }}" href="{{ route('admin.commission') }}">
                        <span class="head-icon">
                            <i class="fas fa-percent"></i>
                        </span>
                        {{ __('backend.Commission_Manager') }}
                    </a>
                </li> -->
                @endcan
                @can('CourtCategory-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Request::is('*/court-category/*') ? 'active':'') }}" href="{{ route('admin.court_category') }}">
                        <span class="head-icon">
                        <i class="fas fa-baseball-ball"></i>
                            <!-- <i class="fas fa-layer-group"></i> -->
                        </span>
                        {{ __('backend.CourtCategory_Manager') }}
                    </a>
                </li>
                @endcan
                @can('Court-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Request::is('*/courts/*') ? 'active':'') }}" href="{{ route('admin.courts') }}">
                        <span class="head-icon">
                            <!-- <img src="{{ URL::asset('assets/images/court.svg')}}"> -->
                            <i class="fas fa-ring"></i>
                        </span>
                        {{ __('backend.Court_Manager') }}
                    </a>
                </li>
                @endcan

                @can('Order-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Route::current()->uri=='orders'?'active':'') }}" href="{{ route('admin.orders') }}">
                        <span class="head-icon">
                            <!-- <img src="{{ URL::asset('assets/images/booking.svg')}}"> -->
                            <i class="fab fa-first-order"></i>
                        </span>
                        {{ __('backend.Booking_Manager') }}
                    </a>
                </li>
                @endcan
                @can('Cash-settlement-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Route::current()->uri=='cash-settlement'?'active':'') }}" href="{{ route('admin.cash-settlement') }}">
                        <span class="head-icon">
                            <!-- <img src="{{ URL::asset('assets/images/booking.svg')}}"> -->
                            <i class="fas fa-money-bill-wave"></i>
                        </span>
                        {{ __('backend.Cash_Settlement_Manager') }}
                    </a>
                </li>
                @endcan

                @can('Banner-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Request::is('*/banner/*') ? 'active':'') }}" href="{{ route('admin.banner') }}">
                        <i class="fas fa-image"></i>
                        {{ __('backend.Banner_Manager') }}
                    </a>
                </li>
                @endcan
                <!-- @can('Testimonial-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Request::is('*/testimonial/*') ? 'active':'') }}" href="{{ route('admin.testimonial') }}">
                        <!-- <i class="fas fa-image"></i> -->
                        <!-- <i class="fas fa-comment-alt"></i> -->
                        <!-- {{ __('backend.Testimonial_Manager') }} -->
                    <!-- </a> -->
                <!-- </li> -->
                <!-- @endcan --> 
                @can('ContactUs-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Request::is('*/contact_us/*') ? 'active':'') }}" href="{{ route('admin.contact_us') }}">
                         <i class="fas fa-id-badge"></i>
                        {{ __('backend.ContactUs_Manager') }}
                    </a>
                </li>
                @endcan



                <!-- @if($restroId)
                <li>
                    <a class="waves-effect waves-dark {{ (Request::is('tables/*') ? 'active':'') }}" href="{{ url('restaurant/tables').'/'.$restroId }}">
                        <i class="fa fa-chair"></i>
                        {{ __('Restaurant Table') }}
                    </a>
                </li>
                @endif -->

                <!-- <?php if ($restroId) { ?>

                    @can('Operator-section')
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('operator/*') ? 'active':'') }}" href="{{ route('admin.operator') }}">
                                <i class="fa fa-users"></i>
                                {{ __('Operators') }}
                            </a>
                        </li>
                    @endcan

                    @can('Holiday-section')
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('holiday/*') ? 'active':'') }}" href="{{ route('admin.holiday') }}">
                                <i class="fas fa-glass-cheers"></i>
                                {{ __('Holiday Manager') }}
                            </a>
                        </li>
                    @endcan
                <?php } ?> -->

                <!--  @can('Restaurant-section')
                    <li>
                       <a class="has-arrow waves-effect waves-dark {{ (Request::is('brands/*') ? 'active':'') }} {{ (Request::is('operator/*') ? 'active':'') }} {{ (Request::is('restaurant/*') ? 'active':'') }}" id="collapseTest" href="javascript:void(0)" aria-expanded="false">
                            <i class="fa fa-building"></i><span class="hide-menu">{{ __('Store Manager') }}
                       </a>
                       <ul aria-expanded="false" class="collapse {{ (Request::is('brands/*') ? 'in':'') }} {{ (Request::is('restaurant/*') ? 'in':'') }}">

                            @can('Operator-section')
                            <li>
                                <a class="waves-effect waves-dark {{ (Request::is('operator/*') ? 'active':'') }}" href="{{ route('admin.operator') }}">
                                    {{ __('Operators') }}
                                </a>
                            </li>
                            @endcan

                            @can('Brand-section')
                            <li>
                                <a class="waves-effect waves-dark {{ (Request::is('brand_category/*') ? 'active':'') }}" href="{{ url('brand_category') }}">
                                    {{ __('Vendor Category') }}
                                </a>
                            </li>
                            <li>
                                <a class="waves-effect waves-dark {{ (Request::is('brands/*') ? 'active':'') }}" href="{{ url('brands') }}">
                                    {{ __('Vendor Manager') }}
                                </a>
                            </li>
                            @endcan

                            @can('Restaurant-section')
                            <li>
                                <a class="waves-effect waves-dark {{ (Request::is('restaurant/*') ? 'active':'') }}" href="{{ route('admin.restaurant') }}">
                                    {{ __('Store Manager') }}
                                </a>
                            </li>
                            @endcan

                            @can('Holiday-section')
                            <li>
                                <a class="waves-effect waves-dark {{ (Request::is('holiday/*') ? 'active':'') }}" href="{{ route('admin.holiday') }}">
                                    {{ __('Holiday Manager') }}
                                </a>
                            </li>
                            @endcan

                        </ul>
                    </li>
                @endcan -->

                <!-- @can('Category-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Route::current()->uri=='category'?'active':'') }}" href="{{ route('admin.category') }}">
                        <i class="fas fa-list-alt"></i>
                        {{ __('Category Manager') }}
                    </a>
                </li>
                @endcan

                @can('Category-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Route::current()->uri=='subcategory'?'active':'') }}" href="{{ route('admin.subcategory') }}">
                        <i class="fas fa-list-alt"></i>
                        {{ __('SubCategory Manager') }}
                    </a>
                </li>
                @endcan -->

                <!-- @can('Attribute-section')
                <li>
                    <a class="has-arrow waves-effect waves-dark {{ (Request::is('attribute/*') ? 'active':'') }}" id="collapseTest" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-fire"></i><span class="hide-menu">{{ __('Attribute Manager') }}
                    </a>
                    <ul aria-expanded="false" class="collapse {{ (Request::is('attribute/*') ? 'in':'') }}" >
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('attribute/*') ? 'active':'') }}" href="{{ route('admin.attribute') }}">
                                {{ __('Attributes') }}
                            </a>
                        </li>
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('attribute_value/*') ? 'active':'') }}" href="{{ route('admin.attribute_value') }}">
                                {{ __('Attribute Values') }}
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan -->

                @can('Product-section')
                <!--  <li>
                    <a class="waves-effect waves-dark {{ (Request::is('product/*') ? 'active':'') }}" href="{{ route('admin.product') }}">
                    <i class="fab fa-product-hunt"></i>
                        {{ __('Product Manager') }}
                    </a>
                </li>
 -->
                <!-- <li>
                    <a class="has-arrow waves-effect waves-dark {{ (Request::is('product/*') ? 'active':'') }} {{ (Request::is('topping/*') ? 'active':'') }} {{ (Request::is('topping_category/*') ? 'active':'') }}" id="collapseTest" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-fire"></i><span class="hide-menu">{{ __('Items Manager') }}
                    </a>
                    <ul aria-expanded="false" class="collapse {{ (Request::is('product/*') ? 'in':'') }} {{ (Request::is('topping/*') ? 'in':'') }} {{ (Request::is('topping_category/*') ? 'in':'') }}" >
                        @can('Toppings_category-section')
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('topping_category/*') ? 'active':'') }}" href="{{ route('admin.topping_category') }}">
                                <i class="fa fa-tint"></i>
                                {{ __('Customized Category') }}
                            </a>
                        </li>
                        @endcan

                        @can('Product-section')
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('product/*') ? 'active':'') }}" href="{{ route('admin.product') }}">
                            <i class="fab fa-product-hunt"></i>
                                {{ __('Product Manager') }}
                            </a>
                        </li>
                        @endcan

                        @can('Toppings-section')
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('topping/*') ? 'active':'') }}" href="{{ route('admin.topping') }}">
                                <i class="fas fa-bread-slice"></i>
                                {{ __('Item Specifics') }}
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li> -->
                @endcan

                <!-- <li>
                    <a class="waves-effect waves-dark {{ (Route::current()->uri=='orders'?'active':'') }}" href="{{ route('admin.orders') }}">
                        <i class="ti-shopping-cart"></i><span class="hide-menu">{{ __('Orders') }}
                    </a>
                </li> -->

                @can('Order-section')
                <!-- <li>
                   <a class="has-arrow waves-effect waves-dark {{ (Request::is('orders/*') ? 'active':'') }}" id="collapseTest" href="javascript:void(0)" aria-expanded="false">
                        <i class="ti-shopping-cart"></i><span class="hide-menu">{{ __('Orders') }}
                   </a>

                    @if ($login_user_data->type == 5)
                        <ul aria-expanded="false" class="collapse in">
                    @else
                        <ul aria-expanded="false" class="collapse {{ (Request::is('orders/*') ? 'in':'') }}">
                    @endif
                        @can('Order-section')
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('orders/Pending') ? 'active':'') }} {{ (Request::is('orders/view/*') ? 'active':'') }} {{ (Request::is('orders/Prepare') ? 'active':'') }} {{ (Request::is('orders/Accepted') ? 'active':'') }}" href="{{ url('orders/Accepted') }}">
                                {{ __('Current Orders') }}
                            </a>
                        </li>
                        @endcan
                        @can('Order-section')
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('orders/Complete') ? 'active':'') }} {{ (Request::is('orders/history/view/*') ? 'active':'') }} {{ (Request::is('orders/Cancel') ? 'active':'') }}" href="{{ url('orders/Complete') }}">
                                {{ __('Orders History') }}
                            </a>
                        </li>
                        @endcan

                        @can('Subadmin-section')
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('cancel_reason/*') ? 'active':'') }}" href="{{ route('admin.cancel_reason') }}">
                                {{ __('Cancel Reason Manager') }}
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li> -->
                @endcan

                <!-- @can('Gift-section')
                <li>
                    <a class="has-arrow waves-effect waves-dark {{ (Request::is('gift_category/*') ? 'active':'') }} {{ (Request::is('gift_banner/*') ? 'active':'') }} {{ (Request::is('gift/*') ? 'active':'') }} {{ (Request::is('gift_orders/*') ? 'active':'') }} {{ (Request::is('gift_attribute/*') ? 'active':'') }} {{ (Request::is('gift_attribute_value/*') ? 'active':'') }}" id="collapseTest" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-gift"></i><span class="hide-menu">{{ __('Gift Manager') }}
                    </a>
                    <ul aria-expanded="false" class="collapse {{ (Request::is('gift_banner/*') ? 'in':'') }} {{ (Request::is('gift/*') ? 'in':'') }} {{ (Request::is('gift_brand/*') ? 'in':'') }} {{ (Request::is('gift_category/*') ? 'in':'') }} {{ (Request::is('gift_orders/*') ? 'in':'') }} {{ (Request::is('gift_attribute/*') ? 'in':'') }} {{ (Request::is('gift_attribute_value/*') ? 'in':'') }}" >

                        @can('Gift-Brand-section')
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('gift_brand/*') ? 'active':'') }}" href="{{ route('admin.gift_brand') }}">
                                <i class="fa fa"></i>
                                {{ __('Gift Brand') }}
                            </a>
                        </li>
                        @endcan

                        @can('Gift-Banner-section')
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('gift_banner/*') ? 'active':'') }}" href="{{ route('admin.gift_banner') }}">
                                <i class="fa fa"></i>
                                {{ __('Gift Banner') }}
                            </a>
                        </li>
                        @endcan

                        @can('Gift-Banner-section')
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('gift_category/*') ? 'active':'') }}" href="{{ route('admin.gift_category') }}">
                                <i class="fa fa"></i>
                                {{ __('Gift Category') }}
                            </a>
                        </li>
                        @endcan

                        @can('GiftCategory-section')
                        <li>
                            <a class="waves-effect waves-dark {{ (Route::current()->uri=='gift-category'?'active':'') }}" href="{{ route('admin.gift-category') }}">
                                <i class="fas fa"></i>
                                {{ __('Gift Sub-Category') }}
                            </a>
                        </li>
                        @endcan

                        @can('Attribute-section')
                            <li id="headingGift" class="{{ (Request::is('gift_attribute/*') ? 'active':'') }} {{ (Request::is('gift_attribute_value/*') ? 'active':'') }}">
                                <a class="waves-effect waves-dark {{ (Request::is('gift_attribute/*') ? 'active':'collapsed') }}" data-toggle="collapse" data-target="#collapseGift" aria-expanded="false" aria-controls="collapseGift">
                               Gift Attribute Manager <i class="fas fa-angle-down"></i>
                                </a>
                                <ul  id="collapseGift" class="{{ (Request::is('gift_attribute/*') ? 'show':'') }} {{ (Request::is('gift_attribute_value/*') ? 'show':'') }} collapse" aria-labelledby="headingGift">
                                    <li>
                                        <a class="waves-effect waves-dark {{ (Request::is('gift_attribute/*') ? 'active':'') }}" href="{{ route('admin.gift_attribute') }}">
                                           Attributes
                                        </a>
                                    </li>                                    
                                    <li>
                                        <a class="waves-effect waves-dark {{ (Request::is('gift_attribute_value/*') ? 'active':'') }}" href="{{ route('admin.gift_attribute_value') }}">
                                           Attribute Values
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endcan


                        @can('Gift-section')
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('gift/*') ? 'active':'') }}" href="{{ route('admin.gift') }}">
                                <i class="fas fa"></i>
                                {{ __('Gifts') }}
                            </a>
                        </li>
                        @endcan

                        @can('Inventory-section')
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('inventory/*') ? 'active':'') }}" href="{{ route('admin.inventory') }}">
                                <i class="fas fa"></i>
                                {{ __('Inventory') }}
                            </a>
                        </li>
                        @endcan

                        <li id="headingEEEE" class="{{ (Request::is('gift_orders/*') ? 'active':'') }}">
                            <a class="waves-effect waves-dark {{ (Request::is('gift_orders/*') ? 'active':'collapsed') }}" data-toggle="collapse" data-target="#collapseEEEE" aria-expanded="false" aria-controls="collapseEEEE">
                           Gift Order <i class="fas fa-angle-down"></i>
                            </a>
                            <ul  id="collapseEEEE" class="{{ (Request::is('gift_orders/*') ? 'show':'') }} collapse" aria-labelledby="headingEEEE">
                                      @can('Gift-section')
                            <li>
                                <a class="waves-effect waves-dark {{ (Request::is('gift_orders/Accepted') ? 'active':'') }}  {{ (Request::is('gift_orders/Pending') ? 'active':'') }}" href="{{ url('gift_orders/Pending') }}">
                                    <i class="fas fa"></i>
                                   Current Order
                                </a>
                            </li>
                            @endcan


                            @can('Gift-section')
                            <li>
                                <a class="waves-effect waves-dark {{ (Request::is('gift_orders/Cancel') ? 'active':'') }}  {{ (Request::is('gift_orders/Complete') ? 'active':'') }}" href="{{ url('gift_orders/Complete') }}">
                                    <i class="fas fa"></i>
                                   Order History
                                </a>
                            </li>
                            @endcan
                            </ul>
                        </li>

                    </ul>
                </li>
                @endcan -->

                <!-- @can('Discount-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Request::is('discount/*') ? 'active':'') }}" href="{{ route('admin.discount') }}">
                        <i class="fa fa-tag"></i>
                        {{ __('Discount Manager') }}
                    </a>
                </li>
                @endcan

                @can('Banner-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Route::current()->uri=='banner'?'active':'') }}" href="{{ url('banner') }}">
                    <i class="fas fa-image"></i>
                        {{ __('Banner Manager') }}
                    </a>
                </li>
                @endcan -->

                <!-- @can('Cash-register-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Route::current()->uri=='cash-register'?'active':'') }}" href="{{ route('admin.cash-register') }}">
                    <i class="fas fa-cash-register"></i>
                        {{ __('Cash Register Manager') }}
                    </a>
                </li>
                @endcan  -->

                <!-- @can('Settlement-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Route::current()->uri=='settlement '?'active':'') }}" href="{{ route('admin.settlement') }}">
                    <i class="fas fa-cash-register"></i>
                    <i class="fas fa-calculator"></i>
                        {{ __('Settlement Manager') }}
                    </a>
                </li>
                @endcan -->



                <!-- @can('Earning-section')
                <li>
                    <a href="{{route('earning')}}" class="waves-effect waves-dark {{ (Request::is('earning/*') ? 'active':'') }} {{ Route::current()->uri=='earning'?'active':'' }}">
                    <i class="fas fa-university"></i>
                        {{ __('Earning Manager') }}
                    </a>
                </li>
                @endcan -->

                @can('Tax-section')
                <!-- <li>
                    <a class="waves-effect waves-dark {{ (Route::current()->uri=='tax'?'active':'') }}" href="{{ route('admin.tax') }}">
                        <i class="fa fa-percent"></i>
                        {{ __('Tax Manager') }}
                    </a>
                </li> -->
                @endcan

                @can('Faq-section')
                <!-- <li>
                    <a class="has-arrow waves-effect waves-dark {{ (Request::is('faq/*') ? 'active':'') }}" id="collapseTest" href="javascript:void(0)" aria-expanded="false">
                        <i class="fa fa-question-circle"></i><span class="hide-menu">{{ __('FAQ Manager') }}
                    </a>
                    <ul aria-expanded="false" class="collapse {{ (Request::is('faq/*') ? 'in':'') }}" >
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('faq/*') ? 'active':'') }}" href="{{ route('admin.faq') }}">
                                <i class="fa fa-question-circle"></i>
                                {{ __('FAQ`s') }}
                            </a>
                        </li>
                        <li>
                            <a class="waves-effect waves-dark {{ (Request::is('faq_request/*') ? 'active':'') }}" href="{{ route('admin.faq_request') }}">
                                <i class="fa fa-tint"></i>
                                {{ __('FAQ Request') }}
                            </a>
                        </li>
                    </ul>
                </li> -->
                @endcan

                <!-- @can('Info-section')
                <li>
                    <a href="{{route('info')}}" class="waves-effect waves-dark {{ Route::current()->uri=='info'?'active':'' }}">
                        <i class="fas fa-info"></i>
                        {{ __('Info Manager') }}
                    </a>
                </li>
                @endcan -->



                @can('Notification-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Route::current()->uri=='notifications'?'active':'') }}" href="{{ route('admin.notifications') }}">
                        <i class="fas fa-bell"></i>
                        {{ __('backend.Notifications_Manager') }}
                    </a>
                </li>
                @endcan
                @can('Content-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Route::current()->uri=='content'?'active':'') }}" href="{{ route('admin.content') }}">
                        <i class="fas fa-fw fa-edit"></i>
                        {{ __('backend.Content_Manager') }}
                    </a>
                </li>
                @endcan
                @can('Email-Template-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Route::current()->uri=='emails '?'active':'') }}" href="{{ route('admin.emails') }}">
                        <i class="fas fa-envelope"></i>
                        {{ __('backend.Email_Manager') }}
                    </a>
                </li>
                @endcan
                @can('AdminSetting-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Route::current()->uri=='price-settings'?'active':'') }}" href="{{ route('admin.price-settings') }}">
                        <i class="fas fa-cog"></i>
                        {{ __('backend.Admin_Settings') }}
                    </a>
                </li>
                @endcan
                @can('Permission-section')
                <li>
                    <a class="waves-effect waves-dark {{ (Request::is('*/permissions/*') ? 'active':'') }}" href="{{ route('admin.permissions') }}">
                        <i class="fas fa-lock"></i>
                        {{ __('backend.Permissions') }}
                    </a>
                </li>
                @endcan

            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
<script>
    $(document).ready(function() {

        $('.has-arrow').on('click', function(e) {
            $(".collapse").css({
                'display': ''
            });
            $(".collapse").removeClass("in");
        });
    });
</script>