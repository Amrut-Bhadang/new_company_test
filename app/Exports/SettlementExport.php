<?php
namespace App\Exports;
use App\Models\Orders;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
class SettlementExport implements FromQuery,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */  
    // use Exportable;

    public function headings(): array
    {
        return [
            'Order_Id',
            'Type',
            'Order status',
            'Amount (QAR)',
            'Coupons',
            'Admin Received (QAR)',
            'Admin Commissions (QAR)',
            'Date',
        ];
    }

    public function query()
    {
        return DB::table('orders')->select('orders.id as order_id','modes.name as order_type','orders.order_status','orders.amount','orders.discount_amount','orders.admin_amount','orders.admin_commission','orders.created_at')
                    ->leftjoin('modes','modes.id','=','orders.order_type')
                    ->groupBy('orders.id')
                    ->orderBy('orders.id', 'ASC');
        
    }

    public function map($bulk): array
    {
        return [
            $bulk->order_id,
            $bulk->order_type,
            $bulk->order_status,
            $bulk->amount,
            $bulk->discount_amount,
            $bulk->admin_amount,
            $bulk->admin_commission,
            Date::dateTimeToExcel($bulk->created_at),
        ];
    }

}
