<?php
namespace App\Exports;
use App\Models\Category;
use App\Models\Restaurant;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CategoryExport implements FromQuery,WithHeadings,WithMapping
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
            'Type',
            'Created-At',
        ];
    }
    public function query()
    {
        $login_user_data = auth()->user();
        $userId = $login_user_data->id;

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
            return Category::query()->select('id', 'name','description','type','created_at')->where('added_by', $login_user_data->id);

        } else {
            return Category::query()->select('id', 'name','description','type','created_at');
        }
        
    }
    public function map($bulk): array
    {
        return [
            $bulk->id,
            $bulk->name,
            $bulk->description,
            $bulk->type,
            date('d M Y', strtotime($bulk->created_at)),
        ];
    }

}
