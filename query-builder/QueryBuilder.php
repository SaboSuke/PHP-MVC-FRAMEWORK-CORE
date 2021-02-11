<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\query_builder;
use sabosuke\bit_mvc_core\Application;

/** 
 * Class QueryBuilder
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\query_builder
*/

class QueryBuilder extends BaseQueryBuilder{

    /**
     * QueryBuilder constructor
     * 
     */
    public function __construct(){
        echo "this is a test";
    }

    public static function select(string $tableName, string $tableAlias = null, array $columns = []): string{
        if ($columns == [])
            return "SELECT * FROM $tableName $tableAlias";
        elseif ($columns !== []){   
            $map = implode(' ', array_map(fn($attr) =>"$attr,", $columns)); //attr1, attr2, attr3, 
            $map = substr($columns, strlen($columns) - 2, strlen($columns)); //attr1, attr2, attr3
            return "SELECT $map FROM $tableName $tableAlias";
        }
    }

    public static function selectUsingFunction(
        string $tableName, 
        string $columnName, 
        string $function,
        string $tableAlias = null, 
        string $columnAlias = null
    ): string{
        if ($columnAlias != null)
            return "SELECT $function($columnName) as $columnAlias FROM $tableName $tableAlias";
    }

    public static function insert(string $tableName, array $columns = [], array $values): string{
        $values_map = implode('', array_map(fn($attr)=> "$attr,", $values));
        if ($columns == []){
            return "INSERT INTO $tableName VALUES($values_map)";
        }else{
            $columns_map = implode('', array_map(fn($attr)=> "$attr,", $columns));
            return "INSERT INTO $tableName ($columns_map) VALUES($values_map)";
        }
    }

    public static function update(string $tableName, array $columns, array $values): string{
        $columns_map = array_map(fn($attr)=> "$attr = ", $columns); //['id=', 'name=','last_name=']
        $values_map = array_map(fn($attr)=> "$attr, ", $values); //['1,' , 'essam, ','abed, ']
        $map = implode($values_map, $columns_map); //id = 1, name = essam, last_name = abed
        return "UPDATE $tableName SET $map";
    }
    
    public static function delete(string $tableName): string{
        return "DELETE FROM $tableName";
    }
    
    /**
     * base join function
     *
     * @param string $tableName
     * @param string $tableAlias
     * @param string $condition
     * @return string
     */
    private static function join(string $tableName, string $tableAlias = null, string $condition): string{
        return " JOIN $tableName $tableAlias ON $condition";
    }

    public static function innerJoin(string $tableName, string $tableAlias = null, string $condition): string{
        return "INNER".static::join($tableName, $tableAlias , $condition);
    }
    
    public static function leftJoin(string $tableName, string $tableAlias = null, string $condition): string{
        return "LEFT".static::join($tableName, $tableAlias , $condition);
    }
    
    public static function rightJoin(string $tableName, string $tableAlias = null, string $condition): string{
        return "RIGHT".static::join($tableName, $tableAlias , $condition);
    }
    
    public static function outerJoin(string $tableName, string $tableAlias = null, string $condition): string{
        return "OUTER".static::join($tableName, $tableAlias , $condition);
    }

    public static function where(string $condition): string{
        return "WHERE $condition";
    }
    
    public static function andWhere(string $condition, string $selectStatement): string{
        return "WHERE $condition($selectStatement)";
    }

    public static function having(string $condition): string{
        return "HAVING $condition";
    }

    private static function removeNotation($string, $position): string{
        return substr($string, $position, strlen($string)); //attr1, attr2, attr3
    }

    public static function orderBy(mixed $columns, string $sortBy = "ASC"): string{
        if(!is_string($columns)){
            $columns = implode('', array_map(fn($attr)=> "$attr,", $columns)); //attr1,attr2,attr3,
            $columns = static::removeNotation($columns, strlen($columns)-1); //attr1,attr2,attr3
        }
        return "ORDER BY $columns $sortBy"; 
    }

    public static function groupBy(string $column): string{
        return "GROUP BY $column";
    }

    public static function getQuery(string $query): string{
        return $query.';';   
    }

    public static function getResult(string $query): mixed{
        $res = Application::$app->db->prepare($query);
        $res->execute();
        return $res->fetchAll(\PDO::FETCH_OBJ);
    }

}