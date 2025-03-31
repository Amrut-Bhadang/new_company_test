<html>
<head>
    <style>
        * { margin: 0; padding: 0; }
        body {
            font: 14px/1.4  dejavusanscondensed;
            
        }
        #page-wrap { width: 800px; margin: 0 auto; border-style: groove; }
        table { border-collapse: collapse; }
        table td, table th { border: 1px solid black; padding: 5px; }
        #customer { overflow: hidden; }
        #logo { text-align: right; float: right; position: relative; margin-top: 25px; border: 1px solid #fff; max-width: 540px; overflow: hidden; }
        #meta { margin-top: 1px; width: 100%; float: right; }
        #meta td { text-align: right;  }
        #meta td.meta-head { text-align: left; background: #eee; }
        #meta td textarea { width: 100%; height: 20px; text-align: right; }
        #items { clear: both; width: 100%; margin: 30px 0 0 0; border: 1px solid black; }
        #items th { background: #eee; }
        #items textarea { width: 80px; height: 100%; }
        #items tr.item-row td {  vertical-align: top; }
        #items td.description { width: 100%; }
        #items td.item-name { width: 175px; }
        #items td.description textarea, #items td.item-name textarea { width: 100%; }
        #buttons { border-right: 0; text-align: right; }
        #items td.total-line { float: center; }
        #items td.total-value { border-left: 0; padding: 10px; }
        #items td.total-value textarea { height: 20px; background: none; }
        #items td.balance { background: #eee; }
        #items td.blank { border: 0; }
        #terms { text-align: left; margin: 20px 0 0 0; }
        #terms h5 { text-transform: uppercase; font: 13px ; letter-spacing: 10px; border-bottom: 1px solid black; padding: 0 0 8px 0; margin: 0 0 8px 0; }
        #terms textarea { width: 100%; text-align: center;}
    </style>
</head>
<body style="font-family:dejavusanscondensed">
<div id="page-wrap">
    <table width="100%">
        <tr>
            <td style="border: 0;  text-align: left" width="62%">
                <span style="font-size: 18px; color: #2f4f4f"><strong>TBN-{{$bookingDetails->id}}</strong></span>
            </td>
            <td style="border: 0;  text-align: right" width="62%"><div id="logo" style="font-size:18px">
                    <img id="image" src="{{asset('web/images/web_logo.svg')}}" alt="logo" /> <br> <br>
                    Tahadiyaat sport faciliities managment sole proprietorship L.L.C.<br>
                    Abu Dhabi - United Arab Emirates
                </div></td>
        </tr>
    </table>
<hr>
   

    <div id="customer">
        <table id="meta">
            <td rowspan="5" style="border: 1px solid white; border-right: 1px solid black; text-align: left" width="62%">
                    {{$bookingDetails->user_details->name ?? ''}}<br>
                           
            </td>
           
             <tr>
                <td class="meta-head">Invoice Date Date</td>
                <td>{{$bookingDetails->created_at ?? ''}}</td>
            </tr>           
            <tr>
                <td class="meta-head">Payment Received Status</td>
                <td>{{$bookingDetails->payment_received_status ?? ''}}</td>
            </tr>
            <tr>
                <td class="meta-head">Paid Amount</td>
                <td><span class="price">{{$bookingDetails->paid_amount ?? ''}}</span></td>
            </tr>
        </table>
    </div>
    <table id="items">
        <tr>
            <th>Court Name</th>
                <th style="width: 100px;">Court address</th>
                <th style="width: 100%;">Facility name</th>
                <th style="width: 100%;">mobile</th>
                <th style="width: 100%;">Booking Date</th>
                <th style="width: 100%;">Booking Start Time</th>
                <th style="width: 100%;">Booking End Time</th>
                
        </tr>
            <tr class="item-row">
                <td class="description">{{$bookingDetails->court_name ?? ''}}</td>
                <td align="description">{{$bookingDetails->address ?? ''}}</td>
                <td align="description">{{$bookingDetails->facility_name ?? ''}}</td>
                <td align="right">{{$bookingDetails->mobile ?? ''}}</td>
                <td align="right">{{$bookingDetails->booking_date ?? ''}}</td>
                <td align="right">{{$bookingDetails->booking_start_time ?? ''}}</td>
                <td align="right">{{$bookingDetails->booking_end_time ?? ''}}</td>
               
            </tr>
            <tr>
                <td colspan="5" class="blank"></td>
                <td  class="total-line">Total Unpaid Amount </td>
               <td  colspan="2" class="total-value balance">{{$bookingDetails->total_amount ?? ''}}</td>
            </tr>
           <!--  <tr>
                <td colspan="6"class="blank"> Admin Commission Perce</td>
                <td  class="total-line"></td>
                <td  colspan="2" class="total-value balance">{{$bookingDetails->admin_commission_percentage ?? ''}}</td>
            </tr>
            <tr>
                <td colspan="6"class="blank"> </td>
                <td  class="total-line"></td>
               <td  colspan="2" class="total-value balance">{{$bookingDetails->admin_commission_amount ?? ''}}</td>
            </tr> -->
            <tr>
                <td colspan="5"class="blank"> </td>
                <td class="total-line">Invoice Total</td>
               <td  colspan="2" class="total-value balance">{{$bookingDetails->paid_amount ?? ''}}</td>
            </tr>
    </table>
    <!--    related transactions -->
        <br>
        @if($bookingDetails->booking_type!='normal')
        <h4>Related Challenges: </h4>
        <table id="related_transactions" style="width: 100%">
            <tr>
                <th align="left" width="20%">Date</th>
                <th align="left">Account</th>
                <th width="50%" align="left">Description</th>
                <th align="right">Amount</th>
            </tr>
            <tr class="item-row">
            <td align="left"></td>
            <td align="left"></td>
            <td align="left"></td>
            <td align="right"><span class="price"></span></td>
        </tr>';
        </table>
        @endif
        <div id="terms">
            <h5>Terms & conditions</h5>
            @if($termsresult!='')
                {!!$termsresult!!}
            @endif
        </div>
        <div style='float:center'>
            <button class="btn btn-primary"onclick="window.print()">Print</button>
        </div>
</div>
</body>
</html>