<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Ipoh Drum Laravel API",
 *      description="This is a swagger-generated API documentation for the project Ipoh Drum. (Only supports OpenAPI Annotations for now.)",
 *      @OA\Contact(
 *          email="henry_lcz97@hotmail.com"
 *      )
 * )
 */
/**
 *  @OA\Server(
 *      url="http://172.104.45.205/",
 *      description="The URL that IpohDrum Laravel API is running on."
 *  )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
