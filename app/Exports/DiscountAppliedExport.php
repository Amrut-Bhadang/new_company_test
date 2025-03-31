<?php
namespace App\Exports;
use App\Models\Discount;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use DB;
Use \Carbon\Carbon;

class DiscountAppliedExport implements FromQuery,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */  
    // use Exportable;

    public function __construct(int $id)
    {
        $this->discount_id = $id;
    }

    public function headings(): array
    {
        return [
            'Order Id',
            'Username',
            'Email',
            'Discount(%)',
            'Discount Code',
            'Order-At',
        ];
    }
    public function query()
    {
        // return User::query()->select('id', 'name', 'email', 'mobile', 'created_at')->where('type', 0)->orderBy('id', 'ASC');
        return Discount::select('users.name','users.email','orders.amount','orders.id','orders.random_order_id','orders.discount_percent','orders.order_status','orders.created_at','discount.discount_code')->join('orders','orders.discount_code','=','discount.discount_code')->join('users','users.id','=','orders.user_id')->where('discount.id',$this->discount_id)->where('orders.order_status','!=','Cancel')->orderBy('orders.id', 'DESC');
    }
    public function map($bulk): array
    {
        return [
            $bulk->random_order_id ? $bulk->random_order_id : $bulk->id,
            $bulk->name,
            $bulk->email,
            $bulk->discount_percent,
            $bulk->discount_code,
            date('d M Y', strtotime($bulk->created_at)),
        ];
    }

}
