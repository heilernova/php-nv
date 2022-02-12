<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Phpnv\Api\Routes;

use Phpnv\Api\ApiException;
use Phpnv\Api\Routes\Route;

class Router
{
    /**
     * @var Route[]
     */
    private static array $routes = [];
    private static string $method = '';

    /**
     * Agrega una ruta al router
     */
    public static function addRoute(Route $route):void
    {
        try{
            self::$routes[] = $route;
        } catch (\Throwable $th){
            throw new ApiException(['Error al agregar una ruta en el router'], $th);
        }
    }

    public static function getMethod():string
    {
        return self::$method;
    }

    /**
     * Busca la route corresponiente a la url ingresada por el usuario.
     */
    public static function find(string $url):?Route
    {
        self::$method = strtolower($_SERVER['REQUEST_METHOD']);
        $url = trim($url, '/');
        $url_items = explode('/', $url);
        $url_num = count($url_items);

        $routes = array_filter(self::$routes, function(Route $route) use ($url, $url_items, $url_num){
            return $route->validRoute($url, $url_items, $url_num);
        });

        uasort($routes, function($a, $b){ return (strcmp($b->pathActionsCount, $a->pathActionsCount)); });

        return array_shift($routes);
    }
}