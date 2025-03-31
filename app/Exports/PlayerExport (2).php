<?php
namespace App\Exports;
use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use DB;
Use \Carbon\Carbon;
use Illuminate\Support\Facades\App;

class PlayerExport implements FromQuery,WithHeadings,WithMapping
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
        // return DB::table('users')->select('Name', 'name', 'email', 'mobile', 'created_at')->where('type', 0)->orderBy('Name', 'ASC');
        // return User::query()->select('id', 'name', 'email', 'mobile', 'created_at')->where('type', 0)->orderBy('id', 'ASC');
        /*you can use condition in query to get required result
         return Bulk::query()->whereRaw('id > 5');*/
         return User::where('type', 3);
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
