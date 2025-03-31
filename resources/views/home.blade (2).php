@extends('layouts.master')

@section('content')
<?php
$login_user_data = auth()->user();
$userType = $login_user_data->type;
// dd($userType);
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script type="text/javascript">
  var no_of_customers = <?php echo $no_of_courts; ?>;
  var no_of_grocery = <?php echo 0 ?>;
  var no_of_restaurant = <?php echo $no_of_admin_commission; ?>;
  var yearly_sale_graph_data = <?php echo $yearly_sale_graph_data; ?>;
  var five_months_graph_data = <?php echo $LastFiveMonthsData; ?>;
  // var no_of_chef  = 0;
  var no_of_celebrity = 0;
</script>
<!-- Content Header (Page header) -->
<div class="row page-titles">
  <div class="col-md-5 align-self-center">
    <h4 class="text-themecolor">{{ __('backend.welcome').', '.$login_user_data->name }}</h4>
  </div>
  <div class="col-md-7 align-self-center text-right">
    <div class="d-flex justify-content-end align-items-center">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/admin') }}">{{ __('backend.home') }}</a></li>
        <li class="breadcrumb-item active"> {{ __('backend.dashboard') }} </li>
      </ol>
    </div>
  </div>
</div>
<!-- /.content-header -->
<!-- first row header -->
<!-- Column -->
<div class="row dashboard-page">

  <div class="col-md-4 col-sm-6">
    <div class="card bg-light">
      <div class="card-body">
        <a class="card_bd" href="{{ route('admin.courts') }}">
          <div class="round bg-info">
            <!-- <img src="{{ URL::asset('assets/images/booking.svg')}}"> -->
            <i class="fas fa-user" style="font-size: 20px;"></i>
          </div>
          <div class="card_bd_s">
            <h2 class="m-t-10 m-b-0">
              <!-- <sup class="font-16">$</sup> -->
              {{$facility_owner_no_of_courts}}
            </h2>
            <small>{{ __('backend.Courts') }}</small>
          </div>
        </a>
      </div>
    </div>
  </div>
  <div class="col-md-4 col-sm-6">
    <div class="card bg-light">
      <div class="card-body">
        <a class="card_bd" href="{{ route('admin.orders') }}">
          <div class="round bg-info">
            <!-- <img src="{{ URL::asset('assets/images/booking.svg')}}"> -->
            <i class="fab fa-first-order" style="font-size: 20px;"></i>
          </div>
          <div class="card_bd_s">
            <h2 class="m-t-10 m-b-0">
              <!-- <sup class="font-16">$</sup> -->
              {{$facility_owner_no_of_booking}}
            </h2>
            <small>{{ __('backend.Bookings') }}</small>
          </div>
        </a>
      </div>
    </div>
  </div>
  <div class="col-md-4 col-sm-6">
    <div class="card bg-light">
      <div class="card-body">
        <a class="card_bd" href="{{ route('admin.orders') }}">
          <div class="round bg-info">
            <img src="{{ URL::asset('assets/images/Commission.svg')}}">
          </div>
          <div class="card_bd_s">
            <h2 class="m-t-10 m-b-0">
              {{$facility_owner_no_of_admin_commission}}
            </h2>
            <small>{{ __('backend.Total_Revenue_&_Admin_Commission') }}</small>
          </div>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- recent tables -->
<div class="row pad">
  <!-- Order List -->
  <div class="graph-cls">
    <!-- <div class="graph-box" ><span style="background-color: rgba(255,0,0,1.0); height: 20px; width: 20px; display: inline-block;"></span><span>Total Courts</span></div>
        <div class="graph-box"><span style="background-color: rgba(0,0,255,1.0); height: 20px; width: 20px; display: inline-block;"></span> <span>Total Orders</span></div> -->
    <select name="graph_type" id="graph_type" class="form-control select2" onchange="changeGraph()" data-placeholder="{{ __('backend.Select') }}" data-dropdown-css-class="select2-primary">
      <option value="orders" <?php if (isset($_GET['graph'])) {
                                echo $_GET['graph'] == 'orders' ? 'selected' : '';
                              } ?>>{{ __('backend.Orders') }}</option>
      <option value="revenue" <?php if (isset($_GET['graph'])) {
                                echo $_GET['graph'] == 'revenue' ? 'selected' : '';
                              } ?>>{{ __('backend.Revenue') }}</option>
    </select>
    <!-- Replace "#FF850A" to change the color -->
  </div>
  <div class="col-md-12 col-sm-12">
    <canvas id="myChart" style="width:100%;height:400px;max-width:1800px"></canvas>
  </div>
  <div class="col-lg-6 col-xs-6 col-sm-6">
    <div class="portlet light">
      <div class="portlet-title">
        <div class="caption">
          <span class="caption-subject bold font-dark uppercase">{{ __('backend.Most_Popular_Timing') }}</span>
        </div>
        <div class="actions">
          <div class="btn-group">
            <a class="btn btn-info" href="{{route('admin.orders')}}"> {{ __('backend.View_All') }}
            </a>
          </div>
        </div>
      </div>
      <div class="portlet-body">
        <div class="tabbable-line">
          <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
              <thead>
                <tr>
                  <!-- <th class="orderTh"> Order Id </th> -->
                  <th class="orderTh"> {{ __('backend.Court_Name') }} </th>
                  <th class="orderTh"> {{ __('backend.Booking_Time') }} </th>
                  <th class="orderTh"> {{ __('backend.Order_Date') }} </th>
                  <!-- <th class="orderTh"> Status </th> -->
                  <!-- <th class="orderTh"> Action </th> -->
                </tr>
              </thead>
              <tbody>
                <tr>
                  @foreach($get_most_popular_booking_facility_owner as $key => $data)
                <tr>
                  <td>{{ $data->courtDetails->court_name ?? 'No Name'}}</td>
                  <td>{{ $data->booking_time ?? 'No Booking Time'}}</td>
                  <td>{{date('d-m-Y', strtotime($data->created_at))?? 'No Date'}}</td>
                </tr>
                @endforeach
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 col-xs-6 col-sm-6">
    <div class="portlet light">
      <div class="portlet-title">
        <div class="caption">
          <span class="caption-subject bold font-dark uppercase">{{ __('backend.Most_Court_Booking_Users') }}</span>
        </div>
        <div class="actions">
          <div class="btn-group">
            <a class="btn btn-info" href="{{route('admin.orders')}}"> {{ __('backend.View_All') }}
            </a>
          </div>
        </div>
      </div>
      <div class="portlet-body">
        <div class="tabbable-line">
          <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
              <thead>
                <tr>
                  <th class="orderTh"> {{ __('backend.User_Name') }} </th>
                  <th class="orderTh"> {{ __('backend.User_Email') }} </th>
                  <th class="orderTh"> {{ __('backend.User_Mobile_No') }} </th>
                  <th class="orderTh"> {{ __('backend.Court_Name') }} </th>
                  <th class="orderTh"> {{ __('backend.Total_Amount') }} </th>
                  <!-- <th class="orderTh"> Status </th> -->
                  <!-- <th class="orderTh"> Action </th> -->
                </tr>
              </thead>
              <tbody>
                @foreach($get_most_booking_users_facility_owner as $key => $user)
                <tr>
                  <td>{{ $user->name ? $user->name:'No Name'}}</td>
                  <td>{{ $user->email ?? 'No Email'}}</td>
                  <td>{{ $user->country_code ?? 'No Country Code'}}-{{ $user->mobile ?? 'No Mobile'}}</td>
                  <td>{{ $user->courtBookingDetail->courtDetails->court_name ?? 'No Court Name'}}</td>
                  <td>{{ $user->court_booking_total ?? 'No Amount'}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 col-xs-6 col-sm-6">
    <div class="portlet light">
      <div class="portlet-title">
        <div class="caption">
          <span class="caption-subject bold font-dark uppercase">{{ __('backend.Recent_Bookings') }}</span>
        </div>
        <div class="actions">
          <div class="btn-group">
            <a class="btn btn-info" href="{{route('admin.orders')}}"> {{ __('backend.View_All') }}
            </a>
          </div>
        </div>
      </div>
      <div class="portlet-body">
        <div class="tabbable-line">
          <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
              <thead>
                <tr>
                  <th class="orderTh"> {{ __('backend.Order_Id') }} </th>
                  <th class="orderTh"> {{ __('backend.User_Name') }} </th>
                  <th class="orderTh"> {{ __('backend.Court_Name') }} </th>
                  <th class="orderTh"> {{ __('backend.Amount') }} </th>
                  <th class="orderTh"> {{ __('backend.Order_Status') }} </th>
                  <th class="orderTh"> {{ __('backend.Order_Date') }} </th>
                </tr>
              </thead>
              <tbody>
                @foreach($get_recent_orders_facility_owner as $key => $order)
                <tr>
                  <td>{{ $order->id ?? 'No Order Id'}}</td>
                  <td>{{ $order->user_name ? $order->user_name:'No User Name'}}</td>
                  <td>{{ $order->courtDetails->court_name ?? 'No Court Name'}}</td>
                  <td>{{ $order->total_amount ?? 'No Amount'}}</td>
                  <td>
                    @if($order->order_status === 'Complete')
                    <span class="label label-sm label-success"> {{__('backend.Completed')}} </span>
                    @else
                    <span class="label label-sm label-warning"> {{__("backend.$order->order_status")}} </span>
                    @endif
                  </td>
                  <td>{{date('d-m-Y', strtotime($order->created_at))}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-xs-6 col-sm-6">
    <div class="portlet light">
      <div class="portlet-title">
        <div class="caption">
          <span class="caption-subject bold font-dark uppercase">{{ __('backend.Cash_Bookings') }}</span>
        </div>
        <div class="actions">
          <div class="btn-group">
            <a class="btn btn-info" href="{{route('admin.orders')}}"> {{ __('backend.View_All') }}
            </a>
          </div>
        </div>
      </div>
      <div class="portlet-body">
        <div class="tabbable-line">
          <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
              <thead>
                <tr>
                  <th class="orderTh"> {{ __('backend.Order_Id') }} </th>
                  <th class="orderTh"> {{ __('backend.User_Name') }} </th>
                  <th class="orderTh"> {{ __('backend.Court_Name') }} </th>
                  <th class="orderTh"> {{ __('backend.Amount') }} </th>
                  <th class="orderTh"> {{ __('backend.Payment_Received_Status') }} </th>
                  <th class="orderTh"> {{ __('backend.Order_Date') }} </th>
                </tr>
              </thead>
              <tbody>
                @foreach($get_cash_booking_facility_owner as $key => $order)
                <tr>
                  <td>{{ $order->id ?? 'No Order Id'}}</td>
                  <td>{{ $order->user_name ? $order->user_name:'No User Name'}}</td>
                  <td>{{ $order->courtDetails->court_name ?? 'No Court Name'}}</td>
                  <td>{{ $order->total_amount ?? 'No Amount'}}</td>
                  <td>
                  <div class="dropdown dropdown{{$order->id}}">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-id="{{$order->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ __('backend.Pending') }}
                    </button>
                    <div class="dropdown-menu dropdownmenu{{$order->id}}" aria-labelledby="dropdownMenuButton">
                    <a data-id="{{$order->id}}" title="Prepare Order" onclick="change_payment_status(this)" data-payment_received_status="Received" class="dropdown-item  status_btn" href="javascript:void(0);">{{ __('backend.Received') }}</a>
                    <a data-id="{{$order->id}}" title="Cancel Order" onclick="change_payment_status(this)" data-payment_received_status="NotReceived" class="dropdown-item" href="javascript:void(0);">{{ __('backend.NotReceived') }}</a>
                    </div>
                  </div>
                  </td>
                  <td>{{date('d-m-Y', strtotime($order->created_at))}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
</div>
<!-- end recent tables -->

<!-- popular tables -->
<!-- popular tabels ends  -->
<div class="card-group">

</div>

<script>
  var xValues = <?php echo $datesJson; ?>;
  var yValues = <?php echo $BookingDataArray; ?>;

  new Chart("myChart", {
    type: "line",
    data: {
      labels: xValues,
      datasets: [{
        fill: false,
        lineTension: 0,
        backgroundColor: "rgba(133,194,64,1.0)",
        borderColor: "rgba(133,194,64,0.4)",
        data: yValues
      }]
    },
    options: {
      legend: {
        display: false
      },
      scales: {
        yAxes: [{
          ticks: {
            min: 0,
            max: <?php echo $max; ?>
          }
        }],
      }
    }
  });

  function change_status($this) {

    id = $($this).attr("data-id");
    status = $($this).attr("data-order_status");

    if (status == 'Accepted') {
      var response = confirm("{{ __('backend.confirm_box_accepted_booking') }}");
    } else {
      var response = confirm("{{ __('backend.confirm_box_cancelled_booking') }}");
    }
    if (response) {

      $.ajax({
        type: 'post',
        data: {
          _method: 'get',
          _token: "{{ csrf_token() }}"
        },
        dataType: 'json',
        url: "{!! url('admin/orders/changeOrderStatus' )!!}" + "/" + id + '/' + status,
        success: function(res) {
          if (res.status === 1) {
            window.location.reload();
            toastr.success(res.message);
            ajax_datatable.draw();
          } else {
            toastr.error(res.message);
          }
        },
        error: function(jqXHR, textStatus, textStatus) {
          console.log(jqXHR);
          toastr.error(jqXHR.statusText)
        }
      });
    }
    return false;
  };
// change payment status
function change_payment_status($this) {
    id = $($this).attr("data-id");
    status = $($this).attr("data-payment_received_status");
    if (status == 'Received') {
      var response = confirm("{{ __('backend.confirm_box_received_cash') }}");
    } else {
      var response = confirm("{{ __('backend.confirm_box_not_received_cash') }}");
    }
   
    if (response) {
      $.ajax({
        type: 'post',
        data: {
          _method: 'get',
          _token: "{{ csrf_token() }}"
        },
        dataType: 'json',
        url: "{!! url('admin/orders/changePaymentStatus' )!!}" + "/" + id + '/' + status,
        success: function(res) {
          if (res.status === 1) {
            window.location.reload();
            toastr.success(res.message);
            ajax_datatable.draw();
          } else {
            toastr.error(res.message);
          }
        },
        error: function(jqXHR, textStatus, textStatus) {
          console.log(jqXHR);
          toastr.error(jqXHR.statusText)
        }
      });
    }
    return false;
    };
  function changeGraph() {
    var graph_type = $('#graph_type').val();
    window.location.href = "{{ url('admin/dashboard?graph=') }}" + graph_type;
  }
  $(document).on('click', '.dropdown-toggle', function() {
    var id = $(this).data('id');
    $(this).attr('aria-expanded', 'true');
    $(".dropdown" + id).addClass('show');
    $(".dropdownmenu" + id).addClass('show');
  });
</script>

@endsection