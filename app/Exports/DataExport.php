<?php

namespace App\Exports;

use App\Models\Data;
use App\Models\Type;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DataExport implements FromQuery, WithHeadings
{
    use Exportable;

    private $filters;
    public function  __construct($filters) {
        $this->filters = $filters;
    }

    public function query(): Relation|EloquentBuilder|Builder
    {
        $filters = json_decode($this->filters);

        $search = $filters->search->value;
        $builder = Data::query()->where('patient_id', $search);

        if (isset($filters->type)) {
            if (is_string($filters->type) && $filters->type != '') {
                $type = Type::getTypeNameByID($filters->type);

                $newType = strtolower((str_replace('-', '_', $type)));
                $builder->where('type', $newType);
            }
        }

        if (isset($filters->date)) {
            $startDate = $filters->date->startDate;
            $endDate = $filters->date->endDate;

            $builder->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $builder;
    }

    public function headings(): array
    {
        return Schema::getColumnListing('data');
    }
}
