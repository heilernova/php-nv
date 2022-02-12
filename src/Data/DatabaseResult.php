<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api\Data;

use mysqli_result;

class DatabaseResult
{
    public $status = false;
    public $insertId = 0;
    public $affectedRows = 0;

    public function __construct(private ?mysqli_result $result = null)
    {
       
    }

    /**
     * Obtiene le objeto mysqli_result de la consulta realizada
     * @throws ApiException Retorna un exeptcion en caso de que la cosulta no retornara un mysqli_result
     */
    public function getMysqliResult():mysqli_result{
        return $this->result;
    }

    /**
     * Retorna un array de objecto representado las información de los campos
     */
    public function fetchFields():array
    {
        return $this->result->fetch_fields();
    }

    /**
     * Retorna un array con el nombre de los campos de la cosulta sql.
     */
    public function fetchFieldsName():array
    {
        return array_map(function($element){ return $element->name; }, $this->fetchFields());
    }

    /**
     * Retorna un array associativo de la filas resultandes de la cosulta sql
     */
    public function fetchAll(bool $assoc = true):array
    {
        return $this->result->fetch_all($assoc ? MYSQLI_ASSOC : MYSQLI_NUM);
    }

    /**
     * Retorn un array de la primera fila del resulta de la consulta sql.
     */
    public function fetchAssoc():array|false|null{
        return $this->result->fetch_assoc();
    }

    /**
     * Retorna un array numerico de la primera fila del resultado de la cosulta sql.
     */
    public function fetchArray():array|false|null{
        return $this->result->fetch_array();
    }
}