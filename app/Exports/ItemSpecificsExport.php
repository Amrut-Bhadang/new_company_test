<?php
namespace App\Exports;
use App\Models\Topping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemSpecificsExport implements FromQuery,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */  
    // use Exportable;

    public function headings(): array
    {
        return [
            'Id',
            'Name',
            'Price',
            'Created-At',
        ];
    }
    public function query()
    {
        return Topping::query()->select('id','topping_name','price','created_at');
        
    }
    public function map($bulk): array
    {
        return [
            $bulk->id,
            $bulk->topping_name,
            $bulk->price,
            Date::dateTimeToExcel($bulk->created_at),
        ];
    }

}
