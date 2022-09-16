<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Models\Attachment;
use Auth;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadsController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        try {
            if ($request->file('files')) {
                $data = [];

                foreach ($request->file('files') as $file) {
                    $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'-'.Str::random(6)).'.'.$file->getClientOriginalExtension();
                    Storage::disk('s3')->putFileAs($request->get('folder'), $file, $filename, 'public');

                    $attachment = new Attachment();
                    $attachment->path = $request->get('folder').'/'.$filename;
                    $attachment->group = $request->get('group') && $request->get('group') !== 'null' ? $request->get('group') : null;
                    $attachment->mime = $file->getMimeType();
                    $attachment->created_by = auth()->user()->id;
                    $attachment->size = $file->getSize();
                    $attachment->folder = $request->get('selectedFolder') && $request->get('selectedFolder') !== 'null'  ? $request->get('selectedFolder') : null;

                    if ($request->get('model') && $request->get('model') !== '' && $request->get('model') !== null && $request->get('model') !== 'null' && $request->get('id')) {
                        $model = '\\App\Models\\'.$request->get('model');

                        if ($object = $model::find($request->get('id'))) {
                            $attachment->attachable()->associate($object);
                        }
                    }

                    $attachment->save();

                    $data[] = new AttachmentResource($attachment);
                }

                return response()->json(['success' => true, 'message' => 'Successfully Uploaded File(s)', 'data' => $data]);
            }

            if ($request->file('file')) {
                $data = [];

                $filename = Str::slug(pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME).'-'.Str::random(6)).'.'.$request->file('file')->getClientOriginalExtension();
                Storage::disk('s3')->putFileAs($request->get('folder'), $request->file('file'), $filename, 'public');

                $attachment = new Attachment();
                $attachment->path = $request->get('folder').'/'.$filename;
                $attachment->group = $request->get('group') && $request->get('group') !== 'null' ? $request->get('group') : null;
                $attachment->mime = $request->file('file')->getMimeType();
                $attachment->created_by = Auth::user()->id;
                $attachment->size = $request->file('file')->getSize();
                $attachment->folder = $request->get('selectedFolder') && $request->get('selectedFolder') !== 'null'  ? $request->get('selectedFolder') : null;

                if ($request->get('model') && $request->get('model') !== '' && $request->get('model') !== null && $request->get('model') !== 'null' && $request->get('id')) {
                    $model = '\\App\Models\\'.$request->get('model');

                    if ($object = $model::find($request->get('id'))) {
                        $attachment->attachable()->associate($object);
                    }
                }

                $attachment->save();

                $data[] = new AttachmentResource($attachment);

                if ($request->get('return')) {
                    if ($request->get('return') === 'thumbnail') {
                        $url = env('S3_URL').'/'.$request->get('folder').'/thumbs/'.$filename;

                        $found = false;

                        while (!$found) {
                            sleep(1);
                            $found = Storage::disk('s3')->exists($request->get('folder').'/thumbs/'.$filename);
                        }
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Successfully Uploaded File',
                    'data' => $data,
                ]);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        return response()->json(['success' => false, 'message' => 'Nothing Uploaded']);
    }

    public function vapor(Request $request): JsonResponse
    {
        $data = [];

        foreach ($request->get('files') as $file){
            $filename = Str::slug(Str::of($file['filename'])->beforeLast('.')).'-'.Str::random(5).'.'.Str::of($file['filename'])->afterLast('.');
            Storage::disk('s3')->move($file['key'], $file['folder'].'/'.$filename);
            Storage::disk('s3')->setVisibility($file['folder'].'/'.$filename, 'public');

            $attachment = new Attachment();
            $attachment->path = $file['folder'].'/'.$filename;
            $attachment->group = isset($file['group']) && $file['group'] !== 'null' ? $file['group'] : null;
            $attachment->mime = $file['type'];
            $attachment->created_by = Auth::user()->id;
            $attachment->size = $file['size'];
            $attachment->folder = isset($file['selectedFolder']) && $file['selectedFolder'] !== 'null'  ? $file['selectedFolder'] : null;

            if (isset($file['model']) && $file['model'] !== '' && $file['model'] !== null && $file['model'] !== 'null' && isset($file['id'])) {
                $model = '\\App\Models\\'.$file['model'];

                if ($object = $model::find($file['id'])) {
                    $attachment->attachable()->associate($object);
                }
            }

            $attachment->save();

            $data[] = new AttachmentResource($attachment);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully Uploaded File(s)',
            'data' => $data,
        ]);
    }
}
