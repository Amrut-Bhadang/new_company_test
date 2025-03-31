@extends('layouts.web.master')
@section('title',$title)
@section('content')
<main class="court-dtl-page">
		<section class="thank_you_page space-cls">
			<div class="container">
				<div class="thank_you_page_in">
					<div class="thank_you_page_img"><img src="{{asset('web/images/thank_you.png')}}"></div>
					<div class="thank_you_page_con">
						<h1>{{__('backend.Thank_You')}}</h1>
						<p>{{$message ??''}}</p>
						<a href="{{route('web.home')}}" class="btn btn-primary">{{__('backend.Back_to_Home')}}</a>
						<a href="{{route('web.print_out',['id'=>$bookingDetailsId])}}')}}" class="btn btn-primary">{{__('backend.Printout')}}</a>

						 	<!-- For Sharing print to socia media  -->
                                <!-- <div class="share-icon">
                                    <a href="javascript:;" class="dropdown-toggle btn btn-primary" id="navbarDropdownShare" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                         {{__('backend.Printout')}}
                                   	</a>
                                    <div class="dropdown-menu share-dropdown" aria-labelledby="navbarDropdownShare">
                                        <ul>
                                    	<li class="facebook">      
                                            <a href="{{route('web.print_out',['id'=>$bookingDetailsId])}}')">{{__('backend.Download')}}
                                            </a>
                                        </li>
                                        <hr/>
                                        <li class="facebook">      
                                            <a href="javascript:;" onclick="window.open('https://facebook.com/sharer.php?u={{route('web.print_out',['id'=>$bookingDetailsId])}}&quote={!! $bookingDetailsId !!}')">
                                                <img src="{{ URL::asset('web/images/facebook.png')}}"> {{__('backend.Facebook')}}
                                            </a>
                                        </li>
                                        <hr/>
                                        <li class="twiter">
                                            <a href="javascript:;" onclick="window.open('https://twitter.com/share?url={{route('web.print_out',['id'=>$bookingDetailsId])}}&text={!! $bookingDetailsId !!}&via=Iseehat&hashtags=buyonIseehat')">
                                                <img src="{{ URL::asset('web/images/twitter.png')}}"> {{__('backend.Twitter')}}
                                            </a>
                                        </li>
                                        <hr/>
                                        <li class="whatup">
                                            <a href="javascript:;" onclick="window.open('https://api.whatsapp.com/send?text={{route('web.print_out',['id'=>$bookingDetailsId])}}')">
                                                <img src="{{ URL::asset('web/images/whatup.png')}}"> 
                                                {{__('backend.whatsapp')}}
                                            </a>
                                        </li>
                                        </ul>
                                    </div>
                                </div>
                        	</div> -->
					</div>
				</div>
			</div>
		</section>
	</main>
@endsection