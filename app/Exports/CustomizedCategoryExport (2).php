<?php
namespace App\Exports;
use App\Models\ToppingCategory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomizedCategoryExport implements FromQuery,WithHeadings
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
            'Topping Choose',
            'Created-At',
        ];
    }
    public function query()
    {
        return ToppingCategory::query()->select('id','name','topping_choose','created_at');
        
    }
    public function map($bulk): array
    {
        return [
            $bulk->id,
            $bulk->name,
            $bulk->topping_choose,
            Date::dateTimeToExcel($bulk->created_at),
        ];
    }

}
