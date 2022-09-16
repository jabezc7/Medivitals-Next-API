<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\PatientFilter;
use App\Http\Resources\Patient\PatientCollection;
use App\Http\Resources\Patient\PatientResource;
use App\Http\Resources\Patient\PatientVitalsResource;
use App\Http\Resources\User\UserSimpleCollection;
use App\Models\Patient;
use App\Models\DevicePatient;
use App\Policies\PatientPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientsController extends Controller
{
    use CrudTrait;

    protected string|Model $model = Patient::class;

    protected string $policy = PatientPolicy::class;

    protected string $collection = PatientCollection::class;

    protected string $resource = PatientResource::class;

    protected string $filter = PatientFilter::class;

    public function addDevice(): JsonResponse
    {
        $device = new DevicePatient();
        $device->device_id = $this->request->device_id;
        $device->patient_id = $this->request->patient_id;
        $device->save();

        return response()->json([
            'success' => true,
            'message' => 'Device was succesfully added to the patient'
        ]);
    }

    public function unlinkDevice(): JsonResponse
    {
        DevicePatient::where('device_id', $this->request->device_id)
                  ->where('patient_id', $this->request->patient_id)
                  ->first()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Device was succesfully deleted'
        ]);
    }

    public function getVitalStats(): JsonResponse
    {
        $query = Patient::find($this->request->patient_id);
        $query->range = $this->request->range;
        $query->sections = $this->request->sections;

        $data = new PatientVitalsResource($query);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function get(): JsonResponse
    {
        $users = Patient::select(['id', 'first', 'last'])->get();
        return response()->json(new UserSimpleCollection($users));
    }

    public function storeView($id): JsonResponse
    {
        $exists = DB::table('patient_views')->where('patient_id', $id)->where('user_id', auth()->user()->id)->first();

        if ($exists){
            DB::table('patient_views')->where('patient_id', $id)
                ->where('user_id', auth()->user()->id)
                ->update(['last_viewed_at' => now()->format('Y-m-d H:i:s')]);

            return response()->json();
        }

        DB::table('patient_views')->insert([
            'patient_id' => $id,
            'user_id' => auth()->user()->id,
            'last_viewed_at' => now()->format('Y-m-d H:i:s')
        ]);

        return response()->json();
    }
}
