<?php
namespace App\Exports;
use App\Models\Media;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BannerExport implements FromQuery,WithHeadings
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
            'Brand Type',
            'Type',
            'Created-At',
        ];
    }
    public function query()
    {
        return Media::query()->select('id','name','brand_type','type','created_at');
        
    }
    public function map($bulk): array
    {
        return [
            $bulk->id,
            $bulk->name,
            $bulk->brand_type,
            $bulk->type,
            Date::dateTimeToExcel($bulk->created_at),
        ];
    }

}
