<?php
namespace App\Exports;

use App\Models\Facility;
use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use DB;
Use \Carbon\Carbon;

class FacilityExport implements FromQuery,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */  
    // use Exportable;

    public function headings(): array
    {
        $id = __("backend.Id");
        $Facility_Owner = __("backend.Facility_Owner");
        $Facility_Name = __("backend.Facility_Name");
        $Address = __("backend.Address");
        $Amenities = __("backend.Amenities");
        $Status = __("backend.Status");
        $Created_Date = __("backend.Created_Date");
        return [
            $id,
            $Facility_Owner,
            $Facility_Name,
            $Address,
            $Amenities,
            $Status,
            $Created_Date,
        ];
    }
    public function query()
    {
         return Facility::join('users','users.id','facilities.facility_owner_id')
         ->select('facilities.*','users.name as facility_owner_name');
    }
    public function map($bulk): array
    {
        return [
            $bulk->id,
            $bulk->facility_owner_name,
            $bulk->name,
            $bulk->address,
            $bulk->facility_amenities,
            $bulk->status == 0 ? 'Deactive':'Active',
            date('d M Y', strtotime($bulk->created_at)),
        ];
    }

}
