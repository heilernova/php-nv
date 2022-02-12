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

use mysqli;
use mysqli_result;
use mysqli_stmt;
use PDO;
use Phpnv\Api\ApiException;
use ReflectionMethod;
use Throwable;

class Database
{
    private mysqli|null $dbMysql = null;
    private DatabaseExecute $databaseQuery;
    private array $errorList = [];
    private mysqli_stmt $stmt;
    private string|null $lastCommand = null;

    /**
     * @param string $type Tipo de base de datos [ mysql, sqlserver.].
     * @param array $data_connet array de la conexión.
     */
    public function __construct(private string $type,private array $dataConnection)
    {
        if ($type != 'mysql') throw new ApiException(['No hay soperte para el tipo de la base de datos', "tipo: $type"]);
        $this->databaseQuery = new DatabaseExecute($this);
    }

    public function getConnection():mysqli
    {
        if ($this->dbMysql){
            return $this->dbMysql;
        }else{
            try {
                $dataConnection = $this->dataConnection;
                $this->dbMysql = mysqli_connect(
                    $dataConnection['hostname'],
                    $dataConnection['username'],
                    $dataConnection['password'],
                    $dataConnection['database']
                );
                $this->dbMysql->autocommit(false);
                return $this->dbMysql;
            } catch (Throwable $th) {
                $message = [
                    'Error al extablecer la conexión Mysqli con la base de datos MySql.',
                    'Datos de conexion:',
                    [
                        'Hostname: ' . ($dataConnection['hostname'] ?? ' [ variable indefinida ]'),
                        'username: ' . ($dataConnection['username'] ?? ' [ variable indefinida ]'),
                        'password: ' . ($dataConnection['password'] ?? ' [ variable indefinida ]'),
                        'database: ' . ($dataConnection['database'] ?? ' [ variable indefinida ]'),
                    ]
                ];
                throw new ApiException($message, $th);
            }
        }
    }

    /**
     * Ejecuta una consulta en la base de datos.
     * @param string $slq comando sql a ejecutar.
     * @param array $params Paramentros de la consulta sql.
     */
    public function query(string $sql, ?array $params = null):bool|mysqli_result
    {
        return $this->queryMySql($sql, $params);
    }

    /**
     * Llama un objeto para ejecutar consultas en la base de datos.
     */
    public function execute():DatabaseExecute
    {
        return $this->databaseQuery;
    }

    public function commit():bool{
        $this->lastCommand = null;
        return $this->dbMysql->commit();
    }

    private function queryMySql(string $sql, array $params = null):bool|mysqli_result
    {
        if ($sql == $this->lastCommand){
            $stmt = $this->stmt;
        }else{
            $stmt = $this->getConnection()->prepare($sql);
            $this->stmt = $stmt;
        }
        if ($stmt){
            if ($params){
                try {
                    $refValues = ['']; // Creamos un array con un valor vacio
                    foreach($params as $key=>$value){
                        $refValues[0] .= is_string($value) ? 's' : (is_int($value) ? 'i' : 'd');
                        $refValues[] = &$params[$key];
                    }
                    $ref = new ReflectionMethod($stmt, 'bind_param');
                    $ref->invokeArgs($stmt, $refValues);
                } catch (\Throwable $th) {
                    throw new ApiException(['Error con el bind_param', $sql], $th);
                }
            }
            if ($stmt->execute()){
                $result = $stmt->get_result();
                return $result ? $result : true;
            }else{
                $this->errorList[] = ["Sql command: $sql", "parametros: " . json_encode($params)];
                return false;
            }
        }else{
            throw new ApiException(['Error con la prepación de la consulta sql.', $this->dbMysql->error]);
        }
    }
}