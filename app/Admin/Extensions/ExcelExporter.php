<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ExcelExporter extends AbstractExporter
{
    protected $fields;

    public function __construct($fields = [])
    {
        parent::__construct();
        $this->fields = $fields;
    }

    public function export()
    {
        $filename = $this->getTable();


        Excel::create($filename, function (LaravelExcelWriter $excel) {

            $excel->sheet('sheet1', function (LaravelExcelWorksheet $sheet) {

                $rows = collect($this->getData());
                $rows->transform(function ($item) {
                    if (!empty($this->fields)) return array_only($item, array_keys($this->fields));
                    else return $item;
                });
                $sheet->rows($rows);
                if (!empty($this->fields)) $sheet->prependRow(array_values($this->fields));
                else $sheet->prependRow(array_keys($rows->first()));

            });

        })->export('xls');
    }
}