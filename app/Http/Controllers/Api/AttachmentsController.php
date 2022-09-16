<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Models\Attachment;
use App\Models\Setting;
use App\Policies\AttachmentPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttachmentsController extends Controller
{
    use CrudTrait;

    protected string|Model $model = Attachment::class;

    protected string $policy = AttachmentPolicy::class;

    protected string $collection = AttachmentCollection::class;

    protected string $resource = AttachmentResource::class;

    protected string $filter = '';

    public function saveFolders(Request $request): JsonResponse
        {
            if ($request->get('model') && $request->get('model') == 'file-library') {
                $setting = Setting::query()->where('key', 'file-library')->first();

                if ($setting) {
                    $setting->json_value = json_encode($request->get('folders'));
                    $setting->save();
                    return response()->json([
                        'success' => true,
                        'message' => 'Folders Saved Successfully'
                    ]);
                }
            }

            if ($request->get('model') && $request->get('id')) {
                $model = '\\App\Models\\'.$request->get('model');
                $object = $model::find($request->get('id'));

                if ($object) {
                    $object->attachment_folders = $request->get('folders');
                    $object->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Folders Saved Successfully'
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Unable to find object to update'
            ]);
        }
}
