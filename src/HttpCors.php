<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api;

class HttpCors
{
    public static function load()
    {
        $cors = Api::getApiInfo()->cors;
        // echo json_encode($cors, 128); exit;

        if ($cors->origin) header("Access-Control-Allow-Origin:  $cors->origin");
        if ($cors->headers) header("Access-Control-Allow-Headers: $cors->headers");
        if ($cors->methods) header("Access-Control-Allow-Methods: $cors->methods");
        // ------------------------ CORS

        if (isset($_SERVER['HTTP_Origin'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_Origin']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
        
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: *");
        
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            exit(0);
        }
    }
}