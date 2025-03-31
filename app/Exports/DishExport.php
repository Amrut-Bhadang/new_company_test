<?php
namespace App\Exports;
use App\Models\Products;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DishExport implements FromQuery,WithHeadings
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
            'Recipe Description',
            'Long Description',
            'Total Amount',
            'Created-At',
        ];
    }
    public function query()
    {
        return Products::query()->select('id','name','recipe_description','long_description','total_amount','created_at');
        
    }
    public function map($bulk): array
    {
        return [
            $bulk->id,
            $bulk->name,
            $bulk->recipe_description,
            $bulk->long_description,
            $bulk->total_amount,
            Date::dateTimeToExcel($bulk->created_at),
        ];
    }

}
