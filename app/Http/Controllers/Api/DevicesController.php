<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\DeviceFilter;
use App\Http\Resources\Device\DeviceCollection;
use App\Http\Resources\Device\DeviceResource;
use App\Models\Device;
use Illuminate\Database\Eloquent\Model;
use App\Policies\DevicePolicy;

class DevicesController extends Controller
{
    use CrudTrait;

    protected string|Model $model = Device::class;

    protected string $policy = DevicePolicy::class;

    protected string $collection = DeviceCollection::class;

    protected string $resource = DeviceResource::class;

    protected string $filter = DeviceFilter::class;

    public function updateFrequency() {
        $this->model::where('id', $this->request->get('device_id'))
                    ->update([
                        'frequencies' => $this->request->get('frequencies')
                    ]);

        return response()->json([
            'success' => true,
            'message' => 'Device frequency was successfully updated'
        ]);
    }
}
