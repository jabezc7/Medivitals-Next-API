<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Note\NoteCollection;
use App\Http\Resources\Notification\NotificationCollection;
use App\Http\Resources\Patient\PatientCollection;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PortalController extends Controller
{
    public function userDashboard(): JsonResponse
    {
        $patients = auth()->user()->patients;

        $return['patients'] = new PatientCollection(auth()->user()->patients()
            ->leftJoin('patient_views', 'patient_views.patient_id', '=', 'users.id')
            ->orderBy('patient_views.last_viewed_at', 'DESC')
            ->limit(11)
            ->get()
        );

        $return['notes'] = new NoteCollection(auth()
            ->user()
            ->notes()
            ->orderBy('created_at', 'DESC')
            ->limit(8)
            ->get()
        );

        $return['notifications'] = new NotificationCollection(Notification::query()
            ->whereIn('patient_id', $patients->pluck('id')->toArray())
            ->orderBy('created_at', 'DESC')
            ->limit(7)
            ->get()
        );

        return response()->json($return);
    }

    public function updateFrequencies(Request $request)
    {
        if (count($request->devices) > 0){
            foreach ($request->devices as $device){
                foreach ($request->data as $item){
                    if ($item['slug'] === 'heart-rate'){
                        // Heart rate
                        Http::asJson()->post(config('services.websocket.endpoint') . '/command', [
                            'data' => (string) 'IWBP86,' . $device['imei'] . ',080835,1,' . $item['value'] . '#',
                            'imei' => (string) $device['imei']
                        ]);
                    }

                    if ($item['slug'] === 'temperature'){
                        // Temperature
                        Http::asJson()->post(config('services.websocket.endpoint') . '/command', [
                            'data' => (string) 'IWBP87,' . $device['imei'] . ',080835,1,' . $item['value'] . '#',
                            'imei' => (string) $device['imei']
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully Updated Frequencies'
        ]);
    }
}
