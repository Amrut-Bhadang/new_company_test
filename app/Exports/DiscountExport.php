<?php
namespace App\Exports;
use App\Models\Discount;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DiscountExport implements FromQuery,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */  
    // use Exportable;

    public function headings(): array
    {
        return [
            'Id',
            'Category type',
            'Discount Code',
            'Percentage',
            'Valid Upto',
            'No.of Use Per User',
            'Description',
            'Created-At',
        ];
    }
    public function query()
    {
        return Discount::query()->select('id','category_type','discount_code','percentage','valid_upto','no_of_use_per_user','description','created_at');
        
    }
    public function map($bulk): array
    {
        return [
            $bulk->id,
            $bulk->category_type,
            $bulk->discount_code,
            $bulk->percentage,
            $bulk->valid_upto,
            $bulk->no_of_use_per_user,
            $bulk->description,
            Date::dateTimeToExcel($bulk->created_at),
        ];
    }

}
