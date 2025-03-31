<?php
namespace App\Exports;

use App\Models\CourtBooking;
use App\Models\Orders;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use DB;
Use \Carbon\Carbon;

class BulkOrderExport implements FromQuery,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */  
    // use Exportable;

    public function headings(): array
    {
        return [
            'Id',
            'User Name',
            'Court Name',
            'Facility Name',
            'Booking Date',
            'Total Amount',
            'Payment Type',
            'Status',
            'Created-At',
        ];
    }
    public function query()
    {
        $orders = CourtBooking::join('courts','courts.id','court_booking.court_id')
            ->join('court_booking_slots','court_booking_slots.court_booking_id','court_booking.id')
            ->join('users','users.id','court_booking.user_id')
            ->join('facilities','facilities.id','courts.facility_id')
            ->groupBy('court_booking_slots.court_booking_id')
            ->select('court_booking.*','courts.court_name','courts.image as court_image','courts.latitude','courts.longitude','courts.address'
            ,'court_booking_slots.booking_start_time','court_booking_slots.booking_end_time','users.name as user_name',
            'facilities.name as facility_name');
            // dd($orders,'d');
            return $orders;
    }
    public function map($bulk): array
    {
        // dd($bulk);
        return [
            $bulk->id,
            $bulk->user_name ? $bulk->user_name:'No Name',
            $bulk->court_name,
            $bulk->facility_name,
            date('d-m-Y', strtotime($bulk->booking_date)),
            $bulk->total_amount,
            $bulk->payment_type,
            $bulk->order_status,
            date('d-m-Y', strtotime($bulk->created_at)),
        ];
    }

}
