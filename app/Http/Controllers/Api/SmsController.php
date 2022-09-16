<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Sms\SmsCollection;
use App\Models\Sms;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;

class SmsController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $phones = explode(',', $request->get('to_phone'));
        $patientIds = explode(',', $request->get('to_id'));

        $user = User::where('id', auth()->user()->id)->first();

        foreach ($patientIds as $key => $value) {

            if ($request->get('model')) {
                $model = '\\App\Models\\'.$request->get('model');
                $object = $model::find(trim($value));
            }

            $sms = Sms::query()->create([
                'provider_id' => null,
                'to' => $phones[$key],
                'patient_id' => $object->id,
                'from' => '',
                'message' => Sms::parseMessage($request->get('message'), $object),
                'direction' => 'outbound',
                'created_by' => auth()->user()->id,
            ]);

            $object->sms()->save($sms);
            $user->sms()->save($sms);
        }

        return response()->json([
            'success' => true,
            'message' => 'SMS Sent Successfully'
        ]);
    }

    public function getUserSMS(Request $request): JsonResponse
    {
        $sms = SMS::where('created_by', auth()->user()->id)->orderby('created_at', 'desc');

        $search = $request->search;
        $sms->where(function ($query) use ($search) {
            $query->where('to', 'LIKE', '%'.$search.'%')
                ->orWhere('message', 'LIKE', '%'.$search.'%');
        });

        return response()->json(new SmsCollection($sms->limit(50)->get()));
    }
}
