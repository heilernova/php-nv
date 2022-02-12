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

use Phpnv\Api\Config\Apis\ApiInfo;
use Phpnv\Api\Config\Config;
use Phpnv\Api\Http\Resources;
use Phpnv\Api\Routes\Route;
use Phpnv\Api\Routes\Router;

class Api
{
    private static string $dir = '';
    private static ApiInfo $apiInfo;
    private static ApiConfig $config;
    public static function run(string $url, string $dir)
    {
        self::$dir = $dir;
        try {
            $url = strtolower(trim($url, '/'));
            self::$config = new ApiConfig("$dir/api.json");
            if (empty($url)){
                require __DIR__.'/view/index.php';
            }else{
                require __DIR__ . '/Routes/list.php';
                Api::loadApi($url)->echo();
            }
        } catch (\Phpnv\Api\ApiException $apiEx) {
            $apiEx->echo();
        } catch (\Throwable $ex){
            $r = new \Phpnv\Api\ApiException(['Error inesperado.'], $ex,'Error - server');
            $r->echo();
        }
    }

    /**
     * Retorna el directorio dende se encuentra alogado los scripts de la api.
     */
    public static function getDir():string
    {
        return self::$dir;
    }

    /**
     * Retorna la cofiguración del archivo api.json
     */
    public static function getConfig():ApiConfig
    {
        return self::$config;
    }

    /**
     * Retorna la un objecto la información de la api en ejecución.
     */
    public static function getApiInfo():ApiInfo
    {
        return  self::$apiInfo;
    }

    private static function loadApi(string $url):Response
    {
        if (Api::getConfig()->isMultiApi()){
            
            $idx = strpos($url, '/');
            if ($idx){
                $name_api = $idx ? substr($url,0, $idx) : $url;

                $url = substr($url, $idx + 1);
            }else{
                $name_api = $url;
            }
            $api = self::$config->getApis()->find($name_api);

        }else{
            $api = self::$config->getApis()->find('api');
        }

        if (!$api) return new Response("Not Found", 404);
        self::$apiInfo = Convert::objectToObject($api, ApiInfo::class);
        
        HttpCors::load();

        // Valimos si se solicita un recurso.
        if (str_starts_with($url, 'resources/public/')){
            return Resources::public(substr($url, strlen('resources/public/')));
        }elseif(str_starts_with($url, 'resources/private')){
            return Resources::private(substr($url, strlen('resources/public/')));
        }

        require self::$dir . (self::$apiInfo->dir == '' ? '' : ('/' . self::$apiInfo->dir)) . '/' . (self::$apiInfo->getName() . "-routes.php");

        // Buscamos la ruta 
        $route = Router::find($url);
        
        if ($route){
            return $route->callAction();
        }else{
            return new Response("Not Found' - 404", 404);
        }

    }
    
}