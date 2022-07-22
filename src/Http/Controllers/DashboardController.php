<?php

namespace Prodemmi\Lava\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Prodemmi\Lava\Facades\Lava;

class DashboardController extends Controller
{

    public function __constructor(){

        dd(session('auth_must_redirect'));

    }

    public function index()
    {
        
        return view( 'lava::dashboard' );
        
    }

    public function globalSearch(Request $request){

        $search = trim($request->input('search'));

        $allResources = collect(Lava::getActivePanel()->getResources());

        $resources = $allResources->where('tool', '=', false);
        $tools     = $allResources->where('tool', '=', true);

        if(strlen($search) === 0) {
            return $this->getAll($resources, $tools);
        }

        $data = [];

        $tools->each(function ($tool) use ($search, &$data, &$hasTools) {

            $resolved = resolve($tool['class']);

            $label = $resolved->label();
            $route = $resolved->route();

            if(str_contains($route, $search) || str_contains($label, $search)){

                $data [] = [
                    'label'    => $label,
                    'subtitle' => $route,
                    'value'    => [
                        'name'       => 'tool',
                        'params'     => [
                            'name'   => $route
                        ]
                    ]
                ];

            }

        });

        if(count($data)){

            array_unshift($data, [
                'header' => true,
                'label'  => "Tools",
                'value'    => [
                    'name'       => 'tools'
                ]
            ]);

        }

        $resources->each(function ($resource) use ($search, &$data) {

            $resource = resolve($resource['resource']);

            $primaryKey = $resource->getPrimaryKey();
            $subtitle   = $resource->subtitle;

            $model = $resource->getModelInstance();

            $route = $resource->route();

            $searchIn = $resource->getSearches();

            if($resource->subtitle){
                $searchIn[] = $resource->subtitle;
            }

            foreach ( $searchIn as $searchColumn ) {

                $model = $model->orWhere( $searchColumn, 'LIKE', "%$search%" );

            }
            
            $avatar = $resource->hasAvatar();

            $selects = [$primaryKey, "$primaryKey as value"];

            if($avatar){

                $avatar = $avatar->column;

                $selects[] = "$avatar as avatar";
                
            }

            if($subtitle){

                $selects[] = "$subtitle as subtitle";
                
            }

            $toPush = [];

            $model->select($selects)->limit(12)->get()->each(function($item) use (&$toPush, $route, $primaryKey, $resource){

                $toPush [] = [
                    'label'    => $item->{$primaryKey},
                    'subtitle' => $item->subtitle,
                    'avatar'   => $item->avatar,
                    'value'  => [
                        'name'       => 'detail',
                        'params'     => [
                            'resource'   => $route,
                            'primaryKey' => $item->{$primaryKey}
                        ]
                    ]
                ];

            });

            if(count($toPush)){

                array_unshift($toPush, [
                    'header' => true,
                    'label'  => $resource->pluralLabel(),
                    'value'  => [
                        'name'       => 'index',
                        'params'     => [
                            'resource'   => $route
                        ]
                    ]
                ]);
                
            }

            $data = array_merge($data, $toPush);

        });

        return response()->json($data);

    }

    protected function getAll($resources, $tools){

        $data = $tools->map(function($tool){

            $tool = resolve($tool['class']);

            return [
                    'label'    => $tool->label(),
                    'value'    => [
                        'name'      => 'tool',
                        'params'    => [
                            'name'  => $tool->route()
                    ]
                ]
            ];

        });

        if($data->count()){
            $data->prepend([
                'header'   => true,
                'label'    => "Tools",
                'value'    => [
                    'name'      => 'tools'
                ]
            ]);
        }

        $resources = $resources->map(function($resource){

            $resource = resolve($resource['class']);

            return [
                    'label'    => $resource->pluralLabel(),
                    'value'    => [
                        'name'      => 'index',
                        'params'    => [
                            'resource'  => $resource->route()
                    ]
                ]
            ];

        });

        if($resources->count()){
            $resources->prepend([
                'header'   => true,
                'label'    => "Resources",
                'value'    => [
                    'name'      => 'resources'
                ]
            ]);
        }

        return response()->json($data->merge($resources));

    }

    public function getOptions()
    {

        $options = $limitRecord = DB::table('lava_options')->get();
        
        return $options->map(function($row){
    
            $array = explode('.', $row->key);
            $grp   = last($array);
    
            array_pop($array);
    
            return [
                "key"   => Str::of(implode(' ', $array))->headline(),
                "value" => $row->value,
                "group" => $grp
            ];
    
        })->groupBy("group");

        
    }

}
