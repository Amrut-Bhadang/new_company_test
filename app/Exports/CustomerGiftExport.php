<?php
namespace App\Exports;
use App\User;
use App\Models\GiftOrder;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use DB;
Use \Carbon\Carbon;

class CustomerGiftExport implements FromQuery,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */  
    // use Exportable;

    public  function __construct($slug) {
        $this->id = $slug;
    }


    public function headings(): array
    {
        return [
            'Order ID',
            'Address',
            'Points',
            'Status',
            'Created-At',
        ];
    }
    public function query()
    {
       //  return DB::table('users')->select('id', 'name', 'email', 'mobile', 'created_at')->where('type', 0)->orderBy('id', 'ASC');
        //return User::query()->select('id', 'name', 'email', 'mobile', 'created_at')->where('type', 0)->orderBy('id', 'ASC');
        /*you can use condition in query to get required result
         return Bulk::query()->whereRaw('id > 5');*/
//         return Orders::select('id','order_status','created_at','amount','address')->where('user_id',$this->id);
         return GiftOrder::select('id','order_status','created_at','points','address')->where('user_id',$this->id);

        /*if (!empty($request->from_price) && !empty($request->to_price)) {
            $transaction->whereBetween('orders.amount', array($request->from_price, $request->to_price));
        }*/
        // $data['total'] = $transaction->select(DB::raw('SUM(amount)' as 'total_amount'))->first();
        //return $transaction->get();

    }
    public function map($bulk): array
    {
        return [
            $bulk->id,
            $bulk->address,
            $bulk->points,
            $bulk->order_status,
            date('d M Y', strtotime($bulk->created_at)),
        ];
    }

}
