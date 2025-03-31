<?php
namespace App\Exports;
use App\User;
/*use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;*/

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

use App\Models\Category;
use DB;
Use \Carbon\Carbon;


class ImportDataToBeExport implements FromCollection, WithHeadings, WithEvents, WithStrictNullComparison
{
    protected $results;

    public function __construct(int $main_category_id, int $brand_id)
    {
        $this->main_category_id = $main_category_id;
        $this->$brand_id = $brand_id;
    }

    public function collection()
    {
        // store the results for later use
       $this->results = $this->getActionItems();

        return $this->results;
    }

    private function getActionItems()
    {
        $select = 'name, description';

        $query = \DB::table('categories')->select(\DB::raw($select));
        // $query->whereNull('action_items.deleted_at');

        $ai = $query->orderBy('name')->get();
        return $ai;
    }

    public function headings(): array
    {
        $columns = [
            'Column 1',
            'Column 2',
            'Column 3',
            'Column 4',
            'Column 5',
            'Column 6',
            'Column 7'
        ];
        return $columns;
    }

    // ...

    public function registerEvents(): array
    {
        return [
            // handle by a closure.
            AfterSheet::class => function(AfterSheet $event) {

                // get layout counts (add 1 to rows for heading row)
                $row_count = $this->results->count() + 1;
                // $row_count = 1;
                // dd($this->results);
                $column_count = count($this->results->toArray());

                // set dropdown column
                $drop_column = 'A';

                // set dropdown options
                $options = [
                    'option 1',
                    'option 2',
                    'option 3',
                ];

                $configs = 'DUS800, DUG900+3xRRUS, DUW2100, 2xMU, SIU, DUS800+3xRRUS, DUG900+3xRRUS, DUW2100';

                // set dropdown list for first data row
               /* $sheet->SetCellValue("A1", "UK");
                $sheet->SetCellValue("A2", "USA");*/

                $validation = $event->sheet->getCell("{$drop_column}2")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST );
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Value is not in list.');
                $validation->setPromptTitle('Pick from list');
                $validation->setPrompt('Please pick a value from the drop-down list.');
                // $validation->setFormula1(sprintf('"%s"',implode(',',$options)));
                $validation->setFormula1('"' . $configs . '"');
                // dd($validation);

                // clone validation to remaining rows
                for ($i = 3; $i <= $row_count; $i++) {
                    $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                    // dd($event);
                }

                // set columns to autosize
                for ($i = 1; $i <= $column_count; $i++) {
                    $column = Coordinate::stringFromColumnIndex($i);
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
