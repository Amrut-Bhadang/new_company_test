<?php
namespace App\Exports;
use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use DB;
Use \Carbon\Carbon;

class FacilityOwnerExport implements FromQuery,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */  
    // use Exportable;

    public function headings(): array
    {
        $id = __("backend.Id");
        $Name = __("backend.Name");
        $Email = __("backend.Email");
        $Mobile = __("backend.Mobile_Number");
        $Gender = __("backend.Gender");
        $Status = __("backend.Status");
        $Created = __("backend.created_at");
        return [
            $id,
            $Name,
            $Email,
            $Mobile,
            $Gender,
            $Status,
            $Created,
        ];
    }
    public function query()
    {
         return User::where('type', 1);
    }
    public function map($bulk): array
    {
        return [
            $bulk->id,
            $bulk->name,
            $bulk->email,
            $bulk->country_code .'-'. $bulk->mobile,
            $bulk->gender,
            $bulk->status == 0 ? 'Deactive':'Active',
            date('d M Y', strtotime($bulk->created_at)),
        ];
    }

}
