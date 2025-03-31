<footer class="footer_sec">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-6">
                <div class="footer_about">
                    <!-- <img src="{{asset('web/images/logo.png')}}" alt="" > -->
                    <h3 class="footer_title">{{__('backend.About_Tahadiyat')}}</h3>
                    <p>{{__('backend.web_footer_about_text')}}</p>
                </div>
                <div class="footer_follow">
                    <ul>
                        <li>
                            <a href="https://www.facebook.com/profile.php?id=100083010384409" target="_blank">
                                <img src="{{asset('web/images/facebook.png')}}">
                            </a>
                        </li>
                        <li>
                            <a href="https://twitter.com/tahadiyaat" target="_blank">
                                <img src="{{asset('web/images/twitter.png')}}">
                            </a>
                        </li>
                        <li>
                            <a href="#" target="_blank">
                                <img src="{{asset('web/images/insta.png')}}">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-6 category_space">
                <div class="footer_link">
                    <h3 class="footer_title">{{__('backend.About_Tahadiyat')}}</h3>
                    <ul>
                        <li>
                            <a href="{{route('web.about_us')}}">{{__('backend.About_US')}}</a>
                        </li>
                        <li>
                            <a href="{{route('web.terms_and_conditions')}}">{{__('backend.terms_and_conditions')}}</a>
                        </li>
                        <li>
                            <a href="{{route('web.how_it_works')}}">{{__('backend.How_it_works')}}</a>
                        </li>
                        <li>
                            <a href="{{route('web.private_policy')}}">{{__('backend.Private_Policy')}}</a>
                        </li>
                        <li>
                            <a href="{{route('web.payment_confirmation')}}">{{__('backend.Payment_Confirmation')}}</a>
                        </li>
                        <li>
                            <a href="{{route('web.refund_policy')}}">{{__('backend.Refund_Policy')}}</a>
                        </li>
                        <li>
                            <a href="{{route('web.cancellation_policy')}}">{{__('backend.Cancellation_Policy')}}</a>
                        </li>
                        <li>
                            <a href="{{route('web.contact_us')}}">{{__('backend.Contact_us')}}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 influencer_sec">
                <div class="footer_link">
                    <h3 class="footer_title">{{__('backend.Quick_Links')}}</h3>
                    <ul>
                        <!-- <li>
                            <a href="#">Upcoming Matches</a>
                        </li> -->
                        <li>
                            <a href="{{route('web.court_list')}}">{{__('backend.Courts')}}</a>
                        </li>
                        <li>
                            <a href="{{route('web.facility')}}">{{__('backend.Facilities')}}</a>
                        </li>

                    </ul>
                </div>
            </div>
            <!-- <div class="col-lg-3 col-md-3 col-sm-6 service_sec">
                <div class="footer_link app-down">
                    <h3 class="footer_title">{{__('backend.Download_the_App_Now')}}</h3>
                    <ul>
                        <li>
                            <a href="#">
                                <img src="{{asset('web/images/googleplay.png')}}">
                            </a>
                        </li>
                        <li>
                            <a href="https://apps.apple.com/us/app/tahadiyaat/id6444200949" target="_blank">
                                <img src="{{asset('web/images/appstore.png')}}">
                            </a>
                        </li>

                    </ul>
                </div>
            </div> -->
        </div>
    </div>
    <div class="copyright_sec">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-sm-6 col-md-6">
                    <div class="copyright_inner">
                        <p><span><script>document.write(new Date().getFullYear())</script> © {{ __('backend.company_name') }} {{ __('backend.All_Rights_Reserved') }}</script></span></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 text-right">
                    <div class="payment">
                        <img src="{{asset('web/images/payment.png')}}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>