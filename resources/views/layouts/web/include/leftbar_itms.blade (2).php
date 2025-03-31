 <div class="filter_pop">
     <h3>{{__('backend.Filter')}}</h3>
     <a class="filter_icon" href="javascript:void(0);"><img src="{{asset('web/images/filter.png')}}"></a>
 </div>
 <div class="leftbar">
     <div class="filter-inner">
         <div class="remove_filter"><button type="button" class="filter_cross"><img src="{{asset('web/images/cross.png')}}"></button></div>
     </div>
     <ul class="leftbar_itms">
         <li><a href="{{route('web.my_account')}}" class="{{ (Request::is('my_account') ? 'active':'') }}">{{__('backend.My_Account')}}<img src="{{asset('web/images/menu_right.png')}}"></a></li>
         <li><a href="{{route('web.challenges')}}" class="{{ (Request::is('challenges') ? 'active':'') }}">{{__('backend.Challenges')}}<img src="{{asset('web/images/menu_right.png')}}"></a></li>
         <li><a href="{{route('web.upcoming_booking')}}" class="{{ (Request::is('upcoming_booking') ? 'active':'') }}">{{__('backend.Upcoming_Bookings')}} <img src="{{asset('web/images/menu_right.png')}}"></a></li>
         <li><a href="{{route('web.completed_booking')}}" class="{{ (Request::is('completed_booking') ? 'active':'') }}">{{__('backend.Completed_Bookings')}} <img src="{{asset('web/images/menu_right.png')}}"></a></li>
         <li><a href="{{route('web.cancelled_booking')}}" class="{{ (Request::is('cancelled_booking') ? 'active':'') }}">{{__('backend.Cancelled_Bookings')}} <img src="{{asset('web/images/menu_right.png')}}"></a></li>
         <li><a href="{{route('web.favourate_court')}}" class="{{ (Request::is('favourate_court') ? 'active':'') }}">{{__('backend.Favourate_court')}} <img src="{{asset('web/images/menu_right.png')}}"></a></li>
         <li><a href="{{route('web.favourate_facility')}}" class="{{ (Request::is('favourate_facility') ? 'active':'') }}">{{__('backend.Favourate_facility')}} <img src="{{asset('web/images/menu_right.png')}}"></a></li>
         <!-- <li><a href="{{route('web.change_password')}}" class="{{ (Request::is('change_password') ? 'active':'') }}">{{__('backend.Change_Password')}} <img src="{{asset('web/images/menu_right.png')}}"></a></li> -->
         <!-- <li><a href="{{route('web.logout')}}" class="logout_web {{ (Request::is('logout') ? 'active':'') }}">{{__('backend.Log_Out')}} <img src="{{asset('web/images/menu_right.png')}}"></a></li> -->
     </ul>
 </div>