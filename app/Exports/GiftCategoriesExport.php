<?php
namespace App\Exports;
use App\Models\GiftCategory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GiftCategoriesExport implements FromQuery,WithHeadings
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
            'Created-At',
        ];
    }
    public function query()
    {
        return GiftCategory::query()->select('id', 'name', 'created_at');
        
    }
    public function map($bulk): array
    {
        return [
            $bulk->id,
            $bulk->name,
            Date::dateTimeToExcel($bulk->created_at),
        ];
    }

}
