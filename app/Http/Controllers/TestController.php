<?php
namespace App\Http\Controllers;

use PRedis;
use Cache;
use Response;
use App\Http\Service\ElectronicFenceService;

class TestController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = array(
            'items' => array(
                array(
                    "id" => 1001,
                    "city_id" => 11,
                    "district_id" => 123,
                    "block_id" => 456,
                    "name" => "莱茵清净",
                    "alias" => "莱茵清净",
                    "address" => "淮海中路 1500 弄 1 栋 1703 室",
                    "lng" => 304.1,
                    "lat" => 304.1,
                    "zoom" => 2,
                    "created_at" => '2014-12-10 10:00:00'
                ),
                array(
                    "id" => 1002,
                    "city_id" => 11,
                    "district_id" => 123,
                    "block_id" => 456,
                    "name" => "京都苑",
                    "alias" => "京都苑",
                    "address" => "淮海中路 1500 弄 1 栋 1703 室",
                    "lng" => 334.1,
                    "lat" => 334.1,
                    "zoom" => 1,
                    "created_at" => '2014-12-10 10:00:00'
                ),
            )
        );
        return Response::json($data)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}
