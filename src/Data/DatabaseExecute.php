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
use Phpnv\Api\ApiException;

class DatabaseExecute
{
    public function __construct(private Database $database){}

    public function selectTable(string $name)
    {

    }

    private function exe(string $sql, array|object $params = null):null|DatabaseResult{
        $result = $this->database->query($sql, (array)$params);
        // echo json_encode($result::class == mysqli_result::class); exit;
        if ($result){
            $connection = $this->database->getConnection();
            $db = new DatabaseResult(gettype($result) == mysqli_result::class ? $result : null);
            $db->insertId = $connection->insert_id;
            $db->affectedRows = $connection->affected_rows;
            return $db;
        }else{
            return null;
        }
    }

    /**
     * Ejecuta un comando sql en la base de datos.
     * @param string $slq Instrución sql a ejeuctar el la base de datos.
     */
    public function command(string $sql, array $params = null):null|DatabaseResult
    {
        return $this->exe($sql, $params);
    }

    /**
     * @param array|object $params Array asositativo o objeto.
     */
    public function insert(array|object $params, string $table):null|DatabaseResult
    {
        $fields = '';
        $values = '';
        foreach ($params as $key => $value){
            $fields .= ", $key";
            $values .= ', ?';
        }
        $fields = ltrim($fields, ', ');
        $values = ltrim($values, ', ');
        return $this->exe("INSERT INTO $table($fields) VALUES($values)", $params);
    }

    /**
     * @param array $params
     * @param array<string, array> $condition
     */
    public function update(object|array $params, array $condition, string $table):null|DatabaseResult
    {
        $values = '';
        foreach ($params as $key=>$value){
            $values .= ", $key=?";
        }
        $values = ltrim($values, ', ');
        if (isset($condition[1])) $params = array_merge((array)$params, (array)$condition[1]);
        return $this->exe("UPDATE $table SET $values WHERE " . $condition[0], $params);
    }

    /**
     * Ejecuta un delete en la base de datos
     */
    public function delete(array $condition, string $table)
    {
        return $this->exe("DELETE FROM $table WHERE " . $condition[0], $condition[1] ?? null);
    }

    /**
     * Ejecuta una funcion en la base de datos.
     */
    public function function(string $name, array $params = null):null|DatabaseResult
    {
        $p = '';
        if ($p){
            $p = str_repeat(', ?', count($params));
            $p = ltrim($p, ', ');
        }
        return $this->exe("SELECT $name($p)", $params);
    }

    /**
     * Ejecuta un procedimiento en la base de datos
     */
    public function procedure(string $name, array $params = null):null|DatabaseResult
    {
        $p = '';
        if ($p){
            $p = str_repeat(', ?', count($params));
            $p = ltrim($p, ', ');
        }
        return $this->exe("CALL $name($p)", $params);
    }
}