<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Phpnv\Api\ApiException;

class ObjectBody
{
    public function __construct(array|null|object $body)
    {
        if ($body){
            $body = (array)$body;
            $errors = [];
            foreach ($this as $key=>$value){
                if (array_key_exists($key, $body)){
                    try {
                        $this->$key = $body[$key];
                    } catch (\Throwable $th) {
                        $errors[] = "$key : Type invalido";
                    }
                }else{
                    $errors[] = "$key : No encotrado";
                }
            }
        }else{
            throw new ApiException(['Faltan datos en el body']);
        }
    }
}