<?php

namespace Prodemmi\Lava\Http\Controllers;

use DirectoryIterator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Prodemmi\Lava\Media;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class MediaController extends Controller
{

    public $path;

    public function __construct()
    {
        $this->middleware('api');

        $this->path = config('lava.file_manager.base_path');
    }

    protected function get_path($path = ''){

        return rtrim($this->path, '/') . '/' . trim($path, '/');
    }

    public function upload(Request $request)
    {

        try {

            $path = $request->input('path') ? $this->get_path(trim(str_replace($this->get_path(), '', $request->input('path')), '/')) : null;

            $ckeditor = $request->file('upload');
            if ($ckeditor) {

                $files = [$ckeditor];
            } else {

                $files = $request->file();
            }
            $resource = $request->input('resource');

            if (filled($files)) {

                $resource = $resource ? resolve($resource) : NULL;
                $column   = $request->input('column');

                $create_thumb = $request->input('create_thumb', FALSE);
                $disk         = $request->input('disk', 'public');
                dd($request->all());

                if ($resource && $column) {

                    $field = $resource->findField($column);
                }

                $response = [];

                $media = [];
                foreach ($files as $key => $file) {

                    $filename  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();


                    if (isset($field->storeFileName) && $field->storeFileName)
                        $store_name = call_user_func($field->storeFileName, $filename, $extension);
                    else
                        $store_name = $filename . '_' . time() . ".$extension";

                    $response[$key]['filename']  = $filename;
                    $response[$key]['extension'] = $extension;
                    $response[$key]['size']      = $file->getSize();
                    $response[$key]['path']      = $store_name;
                    $response[$key]['mime_type'] = $file->getClientMimeType();
                    $response[$key]['disk']      = $disk;

                    dd($path);
                    $uploadedFile = $file->storeAs($disk, $store_name);

                    $response[$key]['uploaded'] = TRUE;
                    $response[$key]['url']      = asset($uploadedFile);

                    if (isset($field->afterUpload) && $field->afterUpload) {

                        $newResponse    = call_user_func($field->afterUpload, $response[$key]);
                        $response[$key] = $newResponse ?? $response[$key];
                    }

                    $media[] = Media::create($response[$key]);
                }

                return response()->json($ckeditor ? Arr::first(array_values($response)) : $media);
            }
        } catch (\Exception $e) {
        }
    }

    public function getMedia(Request $request)
    {

        $base_path = $this->get_path();

        $path = '/' . trim($request->input('path') ?? $base_path, '/');

        $last = explode('/', trim($base_path, '/'));
        $last = Arr::last($last);

        $breadcrumb = trim($base_path, '/') == trim($path, '/') ? ' ' : ('/' . str_replace($base_path, '', $path));
        
        $files = [];

        foreach (array_slice(scandir($path), 2) as $file) {
            $files [] = $this->getFileInfo("$path/$file");
        }

        $previus = explode('/', trim($path, '/'));
        $previus = Arr::last($previus) !== $last;

        return response()->json(['list' => $files, 'path' => $path, 'previus' => $previus, 'breadcrumb' => $breadcrumb]);
    }

    public function searchMedia(Request $request)
    {

        $path = rtrim($request->input('path', $this->get_path()));
        $search = $request->input('search');

        $files = [];

        // foreach (glob("$path/{,*/,*/*/,*/*/*/}*$search*", GLOB_BRACE) as $file) {
        //     $files [] = $this->getFileInfo($file);
        // }
        foreach (glob("$path/{,*/,*/*/,*/*/*/}*", GLOB_BRACE) as $file) {
            $basename = strtolower(basename($file));
            if(preg_match('/'.strtolower($search).'/i', $basename))
                $files [] = $this->getFileInfo($file);
        }

        return response()->json(['list' => $files, 'path' => $path, 'previus' => false, 'breadcrumb' => null]);

    }

    protected function getFileInfo($path){

            $f = [
                'filename'  => pathinfo($path, PATHINFO_FILENAME),
                'full_path' => $path,
                'directory' => is_dir($path),
                'link'      => is_link($path),
                'modified'  => date ("F d Y H:i:s", filemtime($path))
            ];

            $f['path'] = trim(str_replace($this->get_path(), '', $f['full_path']), '/');

            if (!$f['directory']) {
                $f['ext']  = pathinfo($path, PATHINFO_EXTENSION);
                $f['size'] = $this->human_filesize(filesize($path));
                $f['url']  = url(str_replace($this->get_path(), '', $f['path']));
                $f['mime_type']  = mime_content_type($f['full_path']);

                $f['video']  = str_contains($f['mime_type'], "video");
                $f['image']  = str_contains($f['mime_type'], "image");
                $f['audio']  = str_contains($f['mime_type'], "audio");
                $f['text']  = str_contains($f['mime_type'], "text");

                $f['editable'] = $this->is_text($f);

            } else {

                $f['size'] = $this->human_filesize($this->folderSize($f['full_path']));
            }

        return $f;
    }

    protected function folderSize($dir)
    {
        $size = 0;

        foreach (glob("$dir/*", GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->folderSize($each);
        }

        return $size;
    }

    protected function human_filesize($size, $precision = 2)
    {
        $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $step = 1024;
        $i = 0;
        while (($size / $step) > 0.9) {
            $size = $size / $step;
            $i++;
        }
        return round($size, $precision) . ' ' . $units[$i];
    }

    public function newFile(Request $request)
    {

        $path = $request->input('path');
        $filename = $request->input('filename', 'New File');

        file_put_contents("$path/$filename", null);

        return $this->getMedia($request);
    }

    public function newFolder(Request $request)
    {

        $path = $request->input('path');
        $name = $request->input('name', 'New Folder');

        mkdir("$path/$name");

        return $this->getMedia($request);
    }


    public function deleteMedia(Request $request)
    {

        $path = $request->input('path');
        $is_dire = $request->input('is_dire', false);

        return response()->json((new Filesystem)->delete($path));

    }

    protected function is_text($file)
    {

        if($file['text']) 
            return TRUE;

        $exts = ['', 'txt', 'css', 'less','sass', 'scss', 'ccss', 'cs', 'json', 'php', 'c', 'h', 'cpp', 'hh', 'cmake', 'csv', 'dart', 'go', 'gradle', 'lock', 'jsx', 'java', 'ts', 'kt', 'lua', 'md' , 'markdown', 'matlab', 'nginxconf', 'vhost', 'm', 'aw','inc', 'perl', 'pl', 'bzl', 'wsgi', 'rmd', 'rs', 'styl' , 'tsx', 'vhdl', 'vb', 'vue' ,'fcgi' , 'ctp', 'rb', 'py', 'hss', 'js', 'coffee', 'xml', 'html', 'htm', 'svg', 'cgi'];

        return in_array(strtolower($file['ext']), $exts);

    }

    protected function glob_recursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);

        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files, $this->glob_recursive($dir.'/'.basename($pattern), $flags));
        }

        return $files;
    }
}
