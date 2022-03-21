<?php

namespace Prodemmi\Lava\Http\Controllers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Prodemmi\Lava\Media;

class MediaController extends Controller
{

    public $path, $disk, $url, $storage;

    public function __construct()
    {
        $this->middleware('api');

        $disk = config('lava.file_manager.disk');

        $this->path = $disk['root'];
        $this->url = $disk['url'];
        $this->disk = collect(config('filesystems.disks'))->filter(function ($dsk) use ($disk) {
            return $dsk === $disk;
        })->keys()->first();

        $this->storage   = Storage::disk($this->disk);
    }

    protected function get_path($path = '')
    {

        return rtrim($this->path, '/') . '/' . trim($path, '/');
    }

    protected function get_url($path = '')
    {

        return rtrim($this->url, '/') . '/' . trim($path, '/');
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

            $filename  = $this->sanitize(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
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

    protected function sanitize($filename, $force_lowercase = true, $anal = false) {
        $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
                       "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
                       "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = trim(str_replace($strip, "", strip_tags($filename)));
        $clean = preg_replace('/\s+/', "-", $clean);
        $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;
        return ($force_lowercase) ?
            (function_exists('mb_strtolower')) ?
                mb_strtolower($clean, 'UTF-8') :
                strtolower($clean) :
            $clean;
    }

    public function getContent(Request $request){

        $relativePath = $request->input('path');        

        return response()->json(['content' => $this->storage->get($relativePath)]);

    }

    public function editContent(Request $request){

        $relativePath = $request->input('file_path');        
        $content      = $request->input('content', '');      

        $this->storage->put($relativePath, $content);

        return $this->getMedia($request);     

    }

    public function getMedia(Request $request)
    {

        $relativePath = $request->path ?: '';

        $files = [];

        $content = array_merge($this->storage->files($relativePath), $this->storage->directories($relativePath));

        foreach ($content as $file) {
            $files[] = $this->getFileInfo($file);
        }

        return response()->json(['list' => $files, 'path' => $relativePath]);
    }

    public function searchMedia(Request $request)
    {

        $relativePath = $request->input('path');
        $search = strtolower($request->input('search'));

        $files = [];

        $content = array_merge($this->storage->allFiles($relativePath), $this->storage->allDirectories($relativePath));

        foreach ($content as $file) {

            $basename = strtolower(basename($file));
            if(preg_match("/^\/.+\/[a-z]*$/i",$request->input('search')) && preg_match($request->input('search'), $file))
                $files[] = $this->getFileInfo($file);
            elseif (strpos($basename, $search) !== false)
                $files[] = $this->getFileInfo($file);
            
        }

        return response()->json(['list' => $files, 'path' => $relativePath]);
    }

    protected function getFileInfo($relativePath)
    {

        $detail = $this->storage->getMetadata($relativePath);
        $filename = basename($relativePath);

        $detail['modified'] = date('Y-m-d H:i:s', $detail['timestamp']);
        $detail['full_path'] = $this->storage->path($relativePath);
        $detail['filename'] = pathinfo($filename, PATHINFO_FILENAME);
        $detail['mime_type'] = $this->storage->getMimeType($relativePath);
        $detail['url'] = $this->storage->url($relativePath);
        $detail['disk'] = $this->disk;

        if ($detail['type'] === 'dir') {
            $detail['size'] = $this->folderSize($relativePath);
        } else {
            $detail['size'] =  $this->storage->size($relativePath);
            $detail['ext'] = pathinfo($filename, PATHINFO_EXTENSION);
            $detail['editable'] = $this->is_editable($detail['ext']);
            $detail['archive']  = in_array($detail['ext'], ['zip', 'rar']);
        }

        return $detail;
    }

    protected function folderSize($dir)
    {
        $size = 0;

        foreach ($this->storage->allFiles($dir) as $each) {
            $size += $this->storage->exists($each) ? $this->storage->size($each) : $this->folderSize($each);
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

        $relativePath = $request->path ?: '/';
        $filename = $request->input('filename');

        $path = ltrim("$relativePath/$filename", '/');

        $filename = $this->calculateManyFilename($path, $filename);

        $this->storage->put($path, null);

        return $this->getMedia($request);
    }

    public function compressMedia(Request $request)
    {

        $path = $request->input('path');
        $files = $request->input('clipboard');
        $name = $this->calculateManyFilename($path, $request->input('filename'));

        $filename = pathinfo($name, PATHINFO_FILENAME);
        $path = "$path/$filename.zip";

        $zip = new \ZipArchive();
        if ($zip->open($path, \ZipArchive::CREATE) === TRUE) {

            foreach ($files as $file) {

                $this->compressRecursively($zip, $file);
            }

            $zip->close();
        }

        return $this->getMedia($request);
    }

    public function extractMedia(Request $request)
    {

        $relativePath = $request->input('path');
        $files = $request->input('files');

        foreach ($files as $file) {

            $unzip = new \ZipArchive;
            $path = ltrim("$relativePath/" . $file['filename'], '/');
            
            if($unzip->open($this->storage->path($file['path']))){

                $unzip->extractTo($this->storage->path($path));
                $unzip->close();

            }

            $extractedFiles = $this->storage->files($path);

        }

        return $this->getMedia($request);

    }

    protected function compressRecursively(&$zip, $file)
    {
        $file_path = $file['full_path'];

        if (is_dir($file_path)) {

            $dirname = trim(str_replace($this->path, '', $file_path), '/');
            $zip->addEmptyDir(dirname($dirname));

            $newFiles = scandir($file_path);
            $newFiles = array_splice($newFiles, -2);
            foreach ($newFiles as $subfile) {

                $p = trim(str_replace($this->path, '', "$file_path/$subfile"), '/');

                $zip->addFile("$file_path/$subfile", $p);
            }
        } else {
            $zip->addFile($file_path);
        }
    }

    public function newFolder(Request $request)
    {

        $relativePath = str_replace($this->path, '', $request->input('path'));
        $name = $request->input('name');

        $name = $this->calculateManyFilename("$relativePath/$name", $name);

        $this->storage->makeDirectory("$relativePath/$name");

        return $this->getMedia($request);
    }

    protected function calculateManyFilename($path, $name)
    {

        $path = $this->storage->path(trim($path, '/'));

        $new_folders_count = collect(scandir(dirname($path)))->filter(function ($path) use ($name) {
            return str_starts_with(strtolower($path), strtolower($name));
        })->count();

        if ($new_folders_count > 0) {
            $new_folders_count++;
            $name .= " ($new_folders_count)";
        }

        return $name;
    }

    public function deleteMedia(Request $request)
    {

        $files = $request->input('files');

        if(filled($files)){
            foreach ($files as $file) {

                if ($file['type'] === 'dir') {
                    Storage::disk($file['disk'])->deleteDirectory($file['path']);
                } else {
                    Storage::disk($file['disk'])->delete($file['path']);
                }
    
            }
        }

        return $this->getMedia($request);
    }

    public function renameMedia(Request $request)
    {

        $file = $request->input('file');
        $newName = $this->sanitize($request->input('new_name'));

        if ($file['type'] === 'file' && empty(pathinfo($newName, PATHINFO_EXTENSION)) && $file['ext']) {

            $newName = "$newName." . $file['ext'];
        }

        if ($file['type'] === 'dir') {
            $target = dirname($file['path']) . '/' . $newName;

            Storage::disk($file['disk'])->move($file['path'], $target);
        } else {

            $target = dirname($file['path']) . '/' . $newName;

            Storage::disk($file['disk'])->move($file['path'], $target);
        }

        return $this->getMedia($request);
    }

    public function pasteMedia(Request $request)
    {

        $operation = $request->input('operation');
        $files = $request->input('clipboard', []);
        $path = rtrim($request->input('path'), '/');

        foreach ($files as $file) {

            $source = $file['path'];
            $target = "$path/" . basename($file['path']);

            if ($operation === 'copy') {

                $this->storage->copy($source, $target);
            } elseif ($operation === 'cut') {

                $this->storage->move($source, $target);
            }
        }

        return $this->getMedia($request);
    }

    public function getStatics(Request $request)
    {

        $path = $this->path;

        $used_extensions = implode(',', ['tif', 'tiff', 'bmp', 'svg', 'jpg', 'jpeg', 'png', 'webp', 'psd', 'mp4', 'mpg', 'mp2', 'mpeg', 'mov', 'avi', 'mkv', 'webm', 'flv', 'swf', 'wpd', 'mp3', 'aa', 'aiff', 'alac', 'mpc', 'mmf', 'wav', 'flacdoc', 'docx', 'html', 'xml', 'odt', 'pdf', 'xls', 'xlsx', 'xlsm', 'ods', 'ops', 'pps', 'ppt', 'pptx', 'txt', 'zip', 'rar', '7z', 'tar.gz', 'csv', 'log']);

        $data = [
            'Images' => glob("$path/{,*/,*/*/,*/*/*/}*.{tif,tiff,bmp,svg,jpg,jpeg,png,webp,psd}", GLOB_BRACE),
            'Videos' => glob("$path/{,*/,*/*/,*/*/*/}*.{mp4,mpg,mp2,mpeg,mov,avi,mkv,webm,flv,swf,wpd}", GLOB_BRACE),
            'Musics' => glob("$path/{,*/,*/*/,*/*/*/}*.{mp3,aa,aiff,alac,mpc,mmf,wav,flac}", GLOB_BRACE),
            'Documents'   => glob("$path/{,*/,*/*/,*/*/*/}*.{doc,docx,html,xml,odt,pdf,xls,xlsx,xlsm,ods,ops,pps,ppt,pptx,txt,zip,rar,7z,tar.gz,csv}", GLOB_BRACE)
        ];

        $data['Others'] = array_diff(glob("$path/{,*/,*/*/,*/*/*/}*", GLOB_BRACE), array_merge($data['Images'], $data['Videos'], $data['Musics'], $data['Documents']));

        $colors = ['#00a2c0', '#00cd54', '#ffca4c', '#a741e9', '#bf815e'];

        $response = [];

        $all_size = 0;

        foreach ($data as $label => $files) {

            $size = array_sum(array_map(function ($file) {
                return filesize($file);
            }, $files));
            
            switch ($label) {
                case 'Images':
                    $icon = 'ri-image-line';
                    break;
                case 'Videos':
                    $icon = 'ri-mv-line';
                    break;
                case 'Musics':
                    $icon = 'ri-file-music-line';
                    break;
                case 'Documents':
                    $icon = 'ri-file-text-line';
                    break;
                default:
                $icon = 'ri-file-line"';
                    break;
            }

            $response[] = [
                'label' => $label,
                'color' => array_pop($colors),
                'icon'  => $icon,
                'count' => count($files),
                'size'  => $size
            ];

            $all_size += $size;
        }

        return response()->json(compact('response', 'all_size'));
    }

    protected function is_editable($ext)
    {

        $exts = ['', 'txt', 'css', 'less', 'sass', 'scss', 'ccss', 'cs', 'json', 'php', 'c', 'h', 'cpp', 'hh', 'cmake', 'csv', 'dart', 'go', 'gradle', 'lock', 'jsx', 'java', 'ts', 'kt', 'lua', 'md', 'markdown', 'matlab', 'nginxconf', 'vhost', 'm', 'aw', 'inc', 'perl', 'pl', 'bzl', 'wsgi', 'rmd', 'rs', 'styl', 'tsx', 'vhdl', 'vb', 'vue', 'fcgi', 'ctp', 'rb', 'py', 'hss', 'js', 'coffee', 'xml', 'html', 'htm', 'svg', 'cgi'];

        return in_array(strtolower($ext), $exts);
    }

    protected function glob_recursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);

        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->glob_recursive($dir . '/' . basename($pattern), $flags));
        }

        return $files;
    }
}
