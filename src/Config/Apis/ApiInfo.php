<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api\Config\Apis;

use Phpnv\Api\Api;
use Phpnv\Api\Config\Databases\DatabaseInfo;
use Phpnv\Api\Data\Database;
use Stringable;

class ApiInfo
{

    /**
     * @param string $name nombre del proyecto este nombre debe ser unico.
     * @param string $namespace
     * @param string $dir Directorio relativo donde se encuentran alojados los componentes de la api.
     * @param string $resourcesDir Nombre del direcctorio donde se encuentran alojados los recursos multimedio y documentos.
     * @param string $defaultDatabase  Nombre de la base de datos 
     * @param object $objecto con la informacion de los cors
     */
    public function __construct(
        private string $name,
        public string $namespace,
        public string $dir,
        public string $resourcesDir,
        public string $defaultDatabase,
        public object $cors
    )
    {
    
    }

    /**
     * Retrona el nombre de la api.
     */
    public function getName():string
    {
        return $this->name;
    }

    /**
     * Retorna el directorio donde se encuentra alojados los componentes de la api.
     */
    public function getDirFull():string
    {
        return Api::getDir() . "/$this->name";
    }

    public function getResourcesDir():string
    {
        return Api::getDir() . "/$this->resourcesDir";
    }

    /**
     * Retorna un objete para interactura con la base de datos
     */
    public function getDefaultDatabase():Database
    {
        $data = Api::getConfig()->databases->find($this->defaultDatabase);
        return new Database($data->type, $data->dataConnection);
    }
}