<?php

namespace Davidpiesse\Map;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Grimzy\LaravelMysqlSpatial\Types\Geometry;

class Map extends Field
{
    public $component = 'map-field';

    public $showOnUpdate = false;

    public $showOnIndex = false;

    public function height($height = '300px'){
        return $this->withMeta([
            'height' => $height
        ]);
    }

    public function spatialType($type){
        return $this->withMeta([
            'spatialType' => $type
        ]);
    }

    public function colors($colors){
        return $this->withMeta([
            'colors' => $colors
        ]);
    }

    public function zoom($zoom = 13){
        return $this->withMeta([
            'zoom' => $zoom
        ]);
    }

    public function latitude($latitude_field){
        $this->attribute = null;

        return $this->withMeta([
            'latitude_field' => $latitude_field
        ]);
    }

    public function longitude($longitude_field){
        $this->attribute = null;

        return $this->withMeta([
            'longitude_field' => $longitude_field
        ]);
    }

    public function geojson($geojson_field){
        $this->attribute = $geojson_field;

        return $this->withMeta([
            'geojson_field' => $geojson_field
        ]);
    }

    public function rawGeojson($geojson_data){
        $this->attribute = null;

        return $this->withMeta([
            'geojson_data' => $geojson_data //pass in string
        ]);
    }

    public function resolveAttribute($resource, $attribute = null){
        switch($this->meta['spatialType']){
            case 'LatLon':
                return [
                    'lat' => $resource->{$this->meta['latitude_field']},
                    'lon' => $resource->{$this->meta['longitude_field']},
                ];
            break;
            case 'LatLonField':
                $parts = collect(explode(',',$resource->{$attribute}))->map(function($item){
                    return trim($item);
                });

                return [
                    'lat' => $parts[0],
                    'lon' => $parts[1],
                ];
            break;
            case 'GeoJSON':
                return $resource->{$attribute};
            break;
            case 'RawGeoJSON':
                return json_decode($this->meta['geojson_data']);
            break;
            default:
                return $resource->{$attribute};
            break;
        }
    }

    // protected function fillAttributeFromRequest(NovaRequest $request,
    //                                             $requestAttribute,
    //                                             $model,
    //                                             $attribute)
    // {
    //     if ($request->exists($requestAttribute)) {
    //         $geometry = Geometry::fromJson($request[$requestAttribute]);
    //         $model->{$attribute} = $geometry;
    //     }
    // }
}
