<?php

namespace Prodemmi\Lava\Http\Controllers;

use App\Models\Media;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{

    public function __construct()
    {
        $this->middleware( 'api' );
    }

    public function upload(Request $request)
    {

        try {

            $ckeditor = $request->file( 'upload' );
            if ( $ckeditor ) {

                $files = [ $ckeditor ];

            }
            else {

                $files = $request->file();

            }
            $resource = $request->input( 'resource' );

            if ( filled( $files ) ) {

                $resource = $resource ? resolve($resource) : NULL;
                $column   = $request->input( 'column' );

                $create_thumb = $request->input( 'create_thumb', FALSE );
                $disk         = $request->input( 'disk', 'public' );

                if ( $resource && $column ) {

                    $field = $resource->findField( $column );

                }

                $response = [];

                $media = [];
                foreach ( $files as $key => $file ) {

                    $filename  = pathinfo( $file->getClientOriginalName(), PATHINFO_FILENAME );
                    $extension = $file->getClientOriginalExtension();


                    if ( isset( $field->storeFileName ) && $field->storeFileName )
                        $store_name = call_user_func( $field->storeFileName, $filename, $extension );
                    else
                        $store_name = $filename . '_' . time() . ".$extension";

                    $response[$key]['filename']  = $filename;
                    $response[$key]['extension'] = $extension;
                    $response[$key]['size']      = $file->getSize();
                    $response[$key]['path']      = $store_name;
                    $response[$key]['mime_type'] = $file->getClientMimeType();
                    $response[$key]['disk']      = $disk;

                    $uploadedFile = $file->storeAs( $disk, $store_name );

                    $response[$key]['uploaded'] = TRUE;
                    $response[$key]['url']      = asset( $uploadedFile );

                    if ( isset( $field->afterUpload ) && $field->afterUpload ) {

                        $newResponse    = call_user_func( $field->afterUpload, $response[$key] );
                        $response[$key] = $newResponse ?? $response[$key];

                    }

                    $media[] = Media::create( $response[$key] );

                }

                return response()->json( $ckeditor ? Arr::first( array_values( $response ) ) : $media );

            }

        }
        catch ( \Exception $e ) {


        }

    }

    public function delete(Request $request)
    {


    }

    public function getMedia($id)
    {

        return response()->json( Media::find( $id ) );

    }

}
