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
use Phpnv\Api\Response;
use ReflectionFunction;
use ReflectionMethod;

class Route
{
    public string   $id = '';
    private string   $url = '';
    private array    $urlItems = [];
    private int      $urlItemsNum = 0;
    private array    $urlValid = [];

    private array    $urlKeys = [];
    private int      $urlKeysNum = 0;
    private array    $urlParamas = [];

    
    private string   $httpRequest = '';
    
    private string  $path = '';

    /** @var array|callable */
    private         $action;
    private string  $method = '';

    /** @var callable[] */
    private array    $canActivate = [];
    
    private int     $pathCount = 0;
    private array   $pathActions = [];
    public int     $pathActionsCount = 0;
    private array   $pathParams = [];
    private int     $pathParamsCount = 0;
    private array   $pathItems = [];


    /**
     * @param string                    $path
     * @param string|array|callable     $action
     * @param callable[]                $canActivate
     */
    public function __construct(string $path,string|array|callable $action, string $http_method, array $can_activate = [])
    {
        $path = trim($path, '/');
        $this->path = $path;
        $this->action = $action;
        $this->method = $http_method;
        $this->canActivate = $can_activate;

        // Ceparamos la keys y los parametros.

        $items = [];
        $this->id ="$http_method:";
        $i = 0;
        $this->pathItems = explode('/', $path);
        foreach ($this->pathItems as $item){

            if (str_starts_with($item, '{')){
                $item = explode(':', trim($item, "{\}"));
                $optional = false;
                if (str_ends_with($item[1] ?? '', '?')){
                    $item[1] =  rtrim($item[1], '?');
                    $optional = true;
                }

                $this->pathParams[] = (object)[
                    'index'=>$i,
                    'name'=>$item[0],
                    'type'=>($item[1] ?? 'mixed'),
                    'optional'=>$optional
                ];

                $this->id .= "{p}/";

            }else{
                $this->pathActions[] = $i;
                $this->id .= "$item/";
            }

            $i++;
        }

        $this->id = rtrim($this->id, '/');
        $this->pathActionsCount = count($this->pathActions);
        $this->pathParamsCount = count($this->pathParams);
        $this->pathCount = $this->pathActionsCount + $this->pathParamsCount;
    }

    public function addCanActivate(callable $canActivate):void
    {
        $this->canActivate[] = $canActivate;
    }

    public function validRoute(string $url, array $url_explode, int $url_num):bool
    {
        if ($this->method == Router::getMethod() && ($this->pathCount == $url_num || $this->pathCount == ($url_num + 1))){
            // echo json_encode([$this->pathActions, $this->id, $this->pathItems, $url_explode], 128); exit;
            foreach ($this->pathActions as $index){
                if ($this->pathItems[$index] == ($url_explode[$index] ?? '')){
                    $this->httpRequest = $url;
                }else{
                    return false;
                }
            }
            return true;
        }else{
            return false;
        }
    }
    
    
    /**
     * @param array Return an array of parameters.
     */
    private function getHttpParams():array
    {
        $url_items = explode('/', $this->httpRequest);
        $params = [];

        foreach ($this->pathParams as $param) {
            $index = $param->index;
            $name = $param->name;
            $type = $param->type;
            $optinal = $param->optional;

            if (isset($url_items[$index])){
                $value = $url_items[$index];
                if ($optinal && $value == ''){
                    // $params[$name] = null;
                }else{
                    $params[$name] = match ($type){
                        'int'   => (int)$url_items[$index],
                        'float' => (float)$url_items[$index],
                        default => $url_items[$index]
                    };
                }
            }
        }
        return $params;
    }


    /**
     * @return string|array|callable Returns the controller associeated with the path
     */
    private function getAction():string|array|callable
    {
        return $this->controller;
    }

    private function getHttpRequest():string
    {
        return $this->httpRequest;
    }

    /**
     * Ejecuta la acción asignada a la ruta de acceso. y retorna un objeto response
     */
    public function callAction():Response
    {
        $http_method = Router::getMethod();

        // Validamos lo canActivates;
        foreach ($this->canActivate as $canActivate)
        {
            $result = $canActivate();
            if ($result){
                return $result;
            }
        }

        $action = $this->action;
        if (is_callable($this->action)){
            $reflection = new ReflectionFunction($this->action);
        }else{
            // Inicalizamos el controlador
            $namespace = $this->action[0];
            $controller_method = $this->action[1] ?? $http_method;

            $controller = new $namespace();

            if (!method_exists($controller, $controller_method)){
                throw new ApiException(
                    ['No se encontrol el metodo ha ejecutar en el controladro', "$namespace::$controller_method"],
                    null,
                    'Error controller'
                );
            }

            $reflection = new ReflectionMethod($controller, $controller_method);

        }

        $params = $this->getHttpParams();
        $params_count = count($params);
        $num_params = $reflection->getNumberOfParameters();
        $num_params_require = $reflection->getNumberOfRequiredParameters();

        if ($num_params == 0 && $params_count > 0){
            return new Response("Method not allowed for URL - params > required", 404);
        }else{
            if ($num_params_require <= $params_count){

                if ($reflection::class == ReflectionFunction::class){
                    return $reflection->invokeArgs($params);
                }else{
                    return $reflection->invokeArgs($controller, $params);
                }

            }else{
                return new Response("Error - params - url|", 400);
            }
        }

        return new Response([$params, $num_params, $num_params_require]);
    }
}