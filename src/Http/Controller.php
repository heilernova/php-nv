<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api\Http;

use Phpnv\Api\Api;
use Phpnv\Api\Data\Database;
use Phpnv\Api\Main;
use Phpnv\Api\Package;

class Controller
{
    public Database $database;
    public function __construct()
    {
        $name_database = Api::getApiInfo()->defaultDatabase;
        $dbInf = Api::getConfig()->getDatabases()->find($name_database);
        $this->database = new Database($dbInf->type, $dbInf->dataConnection);
    }

    /**
     * Retorna el JSON decodificado enviado en el body.
     * @param bool $associative En caso de que se true retornara un array asociativo del objeto json enviado
     */
    public function getBody(?bool $associative = false):object|array|null
    {
        return json_decode(file_get_contents('php://input'), $associative);
    }
}