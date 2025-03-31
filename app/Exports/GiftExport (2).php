<?php
namespace App\Exports;
use App\Models\Gift;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GiftExport implements FromQuery,WithHeadings
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
            'Quantity',
            'Amount',
            'Points',
            'Created-At',
        ];
    }
    public function query()
    {
        return Gift::query()->select('id','name','description','quantity','amount','points','created_at');
        
    }
    public function map($bulk): array
    {
        return [
            $bulk->id,
            $bulk->name,
            $bulk->description,
            $bulk->quantity,
            $bulk->amount,
            $bulk->points,
            Date::dateTimeToExcel($bulk->created_at),
        ];
    }

}
