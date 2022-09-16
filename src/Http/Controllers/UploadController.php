<?php

namespace Prodemmi\Lava\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Prodemmi\Lava\Utils\LavaHelper;

class UploadController extends Controller
{

    public $disk, $url;

    public function __construct()
    {

        $disk = config('lava.file_manager.disk');

        $this->url = $disk['url'];
        $this->disk = collect(config('filesystems.disks'))->filter(function ($dsk) use ($disk) {
            return $dsk === $disk;
        })->keys()->first();

    }

    public function upload(Request $request)
    {
        $path = $request->path ?: '';

        $ckeditor = $request->file('upload');

        if ($ckeditor) {

            $files = [$ckeditor];
        } else {

            $files = $request->file();
        }
        $resource = $request->input('resource');

        $resource = $resource ? resolve($resource) : NULL;
        $column   = $request->input('column');

        $create_thumb = $request->input('create_thumb', FALSE);

        if ($resource && $column) {

            $field = $resource->findField($column);
        }

        $response = [];

        $media = [];
        foreach ($files as $key => $file) {

            $filename  = LavaHelper::str_sanitize(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();

            if (isset($field) && isset($field->storeFileName) && $field->storeFileName) {

                $store_name = call_user_func($field->storeFileName, $filename, $extension);

                if (isset($field->disk) && $field->disk)
                    $disk = $field->disk;
            } else {
                $store_name = $filename . '_' . time() . ".$extension";
                $disk = $this->disk;
            }

            $response[$key]['filename']  = $filename;
            $response[$key]['extension'] = $extension;
            $response[$key]['size']      = $file->getSize();
            $response[$key]['path']      = $path ?? '/';
            $response[$key]['mime_type'] = $file->getClientMimeType();
            $response[$key]['disk']      = $disk;

            $uploadedFile = $file->storeAs($path, $store_name, [
                'disk'  => $disk
            ]);

            $response[$key]['uploaded'] = TRUE;
            $response[$key]['url']      = rtrim($this->url, '/') . '/' . $uploadedFile;

            if (isset($field) && isset($field->onUpload) && $field->onUpload) {

                $newResponse    = call_user_func($field->onUpload, $response[$key]);
                $response[$key] = $newResponse ?? $response[$key];
            }

            $media[] = $response[$key];
        }

        return response()->json($ckeditor ? Arr::first(array_values($response)) : $media);
    }
}
