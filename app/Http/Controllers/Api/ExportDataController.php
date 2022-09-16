<?php

namespace App\Http\Controllers\Api;

use App\Exports\DataExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Excel;

class ExportDataController extends Controller
{
    public function index(Request $request)
    {
        return (new DataExport($request->filters))->download('data.csv', Excel::CSV);
    }

    public function store()
    {
        return new JsonResource([
            'url' => URL::temporarySignedRoute('data.export.index', now()->addMinutes(5))
        ]);
    }
}
