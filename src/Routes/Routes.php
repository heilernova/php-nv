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

class Routes
{
    /**
     * @var Route[]
     */
    private static array $routes = [];

    private static array|null $parentCanActivate =  null;
    private static string|null $parentPath =  null;

    public static function parents($path, $canActivate = null):void
    {
        self::$parentPath = $path;
        self::$parentCanActivate = $canActivate;
    }

    public static function parentsClear():void
    {
        self::$parentCanActivate = null;
        self::$parentPath = null;
    }

    private static function add(string $path, array|string|callable $controller, $method, array $canActivate = [])
    {
        try {

            if (self::$parentPath) $path = self::$parentPath . "/$path";

            if (self::$parentCanActivate) $canActivate = array_merge(self::$parentCanActivate, $canActivate);

            $route = new Route($path, $controller, $method, $canActivate);

            Router::addRoute($route);

            // // Validamos que array tiene los parametros completos.
            // if (is_array($controller)){
            //     if (count($controller) != 2){
            //         throw new ApiException(["Faltan parametros en array [namespace, function]  : " . json_encode($controller)]);
            //     }else{
            //         if (!is_string($controller[0]) || !is_string($controller[1])){
            //             throw new ApiException(["El tipo de datos de los item de array no son string[namespace, function]  : " . json_encode($controller)]);
            //         }
            //     }
            // }

            // // Validamos el ruta no este en uso.
            // if (array_key_exists($route->id, $_ENV['phpnv-routes'])){
            //     throw new ApiException(["La ruta de acceso ya esta asociada a un controlador."]);
            // }else{
            //     if (self::$parentPath){
            //         $route->path = self::$parentPath . "/" . $route->path;
            //     }
            //     if (self::$parentCanActivate){
            //         $route->canActivate = [self::$parentCanActivate, ...$route->canActivate];
            //     }
            //     self::$routes[$route->id] = $route;
            //     $_ENV['phpnv-routes'][$route->id] = $route;
            // }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    
    /**
     * Agrega una ruta de acceso a una función expecifica de un controlador en le HTTP METHOD GET.
     * @param string                                $path url a por la cual se accedera al controlador.
     * @param array<namespace, method>|callable     $controller función o array del namespace y el metodo del controlador
     * @param callable[]                            $canActivate
     * @throws ApiException  Retorna error en caso de que la ruta ya este en uso, o en caso
     * de que el parametro controller este incompleto.
     */
    public static function get(string $path, array|callable $controller, array $canActivate = [])
    {
        self::add($path, $controller,'get', $canActivate);
    }

    /**
     * Agrega una ruta de acceso a una función expecifica de un controlador en le HTTP METHOD POST.
     * @param string                                $path url a por la cual se accedera al controlador.
     * @param array<namespace, method>|callable     $controller función o array del namespace y el metodo del controlador
     * @param callable[]                            $canActivate
     * @throws ApiException  Retorna error en caso de que la ruta ya este en uso, o en caso
     * de que el parametro controller este incompleto.
     */
    public static function post(string $path, array|callable $controller, array $canActivate = [])
    {
        self::add($path, $controller,'post', $canActivate);
    }

    /**
     * Agrega una ruta de acceso a una función expecifica de un controlador en le HTTP METHOD PUT.
     * @param string                                $path url a por la cual se accedera al controlador.
     * @param array<namespace, method>|callable     $controller función o array del namespace y el metodo del controlador
     * @param callable[]                            $canActivate
     * @throws ApiException  Retorna error en caso de que la ruta ya este en uso, o en caso
     * de que el parametro controller este incompleto.
     */
    public static function put(string $path, array|callable $controller, array $canActivate = [])
    {
        self::add($path, $controller,'put', $canActivate);
    }

    /**
     * Agrega una ruta de acceso a una función expecifica de un controlador en le HTTP METHOD PATCH.
     * @param string                                $path url a por la cual se accedera al controlador.
     * @param array<namespace, method>|callable     $controller función o array del namespace y el metodo del controlador
     * @param callable[]                            $canActivate
     * @throws ApiException  Retorna error en caso de que la ruta ya este en uso, o en caso
     * de que el parametro controller este incompleto.
     */
    public static function patch(string $path, array|callable $controller, array $canActivate = [])
    {
        self::add($path, $controller,'patch', $canActivate);
    }

    /**
     * Agrega una ruta de acceso a una función expecifica de un controlador en le HTTP METHOD DELETE.
     * @param string                                $path url a por la cual se accedera al controlador.
     * @param array<namespace, method>|callable     $controller función o array del namespace y el metodo del controlador
     * @param callable[]                            $canActivate
     * @throws ApiException  Retorna error en caso de que la ruta ya este en uso, o en caso
     * de que el parametro controller este incompleto.
     */
    public static function delete(string $path, array|callable $controller, array $canActivate = [])
    {
        self::add($path, $controller,'delete', $canActivate);
    }
}