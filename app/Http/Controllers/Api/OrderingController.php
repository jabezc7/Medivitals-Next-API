<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Type;

class OrderingController extends Controller
{
    const TYPE_CLASS = [
        'types' => Type::class,
    ];

    public function update(Request $request): JsonResponse
    {
        try{
            collect($request->get('orderingData'))
                ->each(function ($item) use ($request) {
                    $type = (self::TYPE_CLASS[$request->get('orderingType')])::find($item['id']);
                    $type->ordering = $item['ordering'];
                    $type->save();
                });
        } catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Error updating order'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully Updated Ordering'
        ]);
    }
}
