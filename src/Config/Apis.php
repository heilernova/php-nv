<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api\Config;

use Phpnv\Api\Config\Apis\ApiInfo;
use Phpnv\Api\Config\ConfigInfo;

class Apis
{
    public function __construct(private $data, private $dir = ''){}

    public function find($name):ApiInfo|null
    {
        // echo json_encode($this->data->apis, 128);
        $listApis = (array)$this->data->apis;

        if (array_key_exists($name, $listApis)){
            $d = $listApis[$name];
            
            $d->dir = trim($this->dir, '/') . ($d->dir !='' ? "/$d->dir" : '');

            return new ApiInfo($name, $d->namespace, $d->dir, $d->resourcesDir, $d->defaultDatabase, $d->cors);
        }else{
            return null;
        }
    }

    public function add($name, $namespace, $dir, $resourcesDir, $default_database, $cor)
    {
        $this->data->apis->$name = new ApiInfo($name, $namespace, $dir, $resourcesDir, $default_database, $cor);
    }

    public function count():int
    {
        return count((array)$this->data->apis);
    }

}