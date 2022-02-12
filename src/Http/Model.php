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

class Model
{
    public Database $database;
    /** 
     * @param string $database Nombre de la base de datos.
     */
    public function __construct(string $database)
    {
        $inf = Api::getConfig()->getDatabases()->find($database);
        $this->database = new Database($inf->type, $inf->dataConnection);  
    }
}