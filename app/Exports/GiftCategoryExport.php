<?php
namespace App\Exports;
use App\Models\GiftSubCategory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GiftCategoryExport implements FromQuery,WithHeadings
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
            'Description',
            'Created-At',
        ];
    }
    public function query()
    {
        return GiftSubCategory::query()->select('id','name','description','created_at');
        
    }
    public function map($bulk): array
    {
        return [
            $bulk->id,
            $bulk->name,
            $bulk->description,
            Date::dateTimeToExcel($bulk->created_at),
        ];
    }

}
