<?php
namespace App\Exports;
use App\Models\Restaurant;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RestaurantExport implements FromQuery,WithHeadings
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
            'Email',
            'Country Code',
            'Mobile',
            'Cost For Two Price',
            'Address',
            'Created-At',
        ];
    }
    public function query()
    {
        return Restaurant::query()->select('id', 'name', 'email', 'country_code', 'phone_number', 'cost_for_two_price', 'address',  'created_at');
        
    }
    public function map($bulk): array
    {
        return [
            $bulk->id,
            $bulk->name,
            $bulk->email,
            $bulk->country_code,
            $bulk->phone_number,
            $bulk->cost_for_two_price,
            $bulk->address,
            Date::dateTimeToExcel($bulk->created_at),
        ];
    }

}
