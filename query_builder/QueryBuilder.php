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

class QueryBuilder{ //extends QueryBuilder

    /**
     * QueryBuilder constructor
     * 
     */
    public function __construct(){
        
    }

    private static function mapArrayIntoString(array $array, string $implodeNotation){
        return implode($implodeNotation, array_map(fn($attr) => "$attr,", $array));
    }

    private static function removeNotation($string, $length): string{
        return substr($string, 0, $length); 
    }

    protected static function select(string $tableName, ?string $tableAlias = null, ?array $columns = []): string{
        if ($columns == [])
            return "SELECT * FROM $tableName $tableAlias ";
        elseif ($columns !== []){   
            //$map = implode('', array_map(fn($attr) =>"$attr,", $columns));
            $map = static::mapArrayIntoString($columns, ' '); //string = attr1, attr2, attr3, 
            $map = self::removeNotation($map, strlen($map)-1); //string = attr1, attr2, attr3
            return "SELECT $map FROM $tableName $tableAlias ";
        }
    }

    protected static function selectUsingFunction(
        string $tableName, 
        string $columnName,
        string $function,
        ?string $tableAlias = null, 
        ?string $columnAlias = null
    ): string{
        if ($columnAlias != null)
            return "SELECT $function($columnName) as $columnAlias FROM $tableName $tableAlias ";
        else
            return "SELECT $function($columnName) FROM $tableName $tableAlias ";
    }

    protected static function insert(string $tableName, ?array $columns = [], array $values): string{
        $values_map = implode('', array_map(fn($attr)=> "'$attr',", $values));
        $values_map = self::removeNotation($values_map, strlen($values_map)-1);
        if ($columns == []){
            return "INSERT INTO $tableName VALUES($values_map)";
        }else{
            $columns_map = implode('', array_map(fn($attr)=> "$attr,", $columns));
            $columns_map = self::removeNotation($columns_map, strlen($columns_map)-1);
            return "INSERT INTO $tableName ($columns_map) VALUES($values_map)";
        }
    }

    protected static function update(string $tableName, array $columns, array $values): string{
        $columns_map = array_map(fn($attr)=> "$attr = %s", $columns); //['id = %s', 'name = %s','last_name = %s']
        $values_map = array_map(fn($attr)=> "'$attr', ", $values); //['1, ' , 'essam, ','abed, ']
        $a = [];
        $i = 0; 
        foreach($columns_map as $val){
            $a[$i] = sprintf(
                $val,
                $values_map[$i]
                );
                $i++;
        }
        
        $map = trim(implode("", $a)); //id = 1, name = essam, last_name = abed,
        $map = self::removeNotation($map, strlen($map)-1); //id = 1, name = essam, last_name = abed
        return "UPDATE $tableName SET $map ";
    }
    
    protected static function delete(string $tableName): string{
        return "DELETE FROM $tableName ";
    }
    
    /**
     * base join function
     *
     * @param string $tableName
     * @param string $tableAlias
     * @param string $condition
     * @return string
     */
    private static function join(string $tableName, ?string $tableAlias = null, string $condition): string{
        return " JOIN $tableName $tableAlias ON $condition ";
    }

    protected static function joinTableOn(string $tableName, ?string $tableAlias = null, string $condition): string{
        return static::join($tableName, $tableAlias, $condition);
    }
    
    protected static function joinTableUsing(string $tableName, string $columnName): string{
        return " JOIN $tableName USING($columnName)";
    }

    protected static function innerJoin(string $tableName, ?string $tableAlias = null, string $condition): string{
        return " INNER".static::join($tableName, $tableAlias , $condition);
    }
    
    protected static function leftJoin(string $tableName, ?string $tableAlias = null, string $condition): string{
        return " LEFT".static::join($tableName, $tableAlias , $condition);
    }
    
    protected static function rightJoin(string $tableName, ?string $tableAlias = null, string $condition): string{
        return " RIGHT".static::join($tableName, $tableAlias , $condition);
    }
    
    protected static function outerJoin(string $tableName, ?string $tableAlias = null, string $condition): string{
        return " OUTER".static::join($tableName, $tableAlias , $condition);
    }

    protected static function where(string $condition): string{
        return " WHERE $condition ";
    }

    protected static function andWhere(string $condition, string $selectStatement): string{
        return " WHERE $condition($selectStatement) ";
    }

    protected static function having(string $condition): string{
        return " HAVING $condition ";
    }

    protected static function orderBy($columns, ?string $sortBy = "ASC"): string{
        if(!is_string($columns)){
            $columns = implode('', array_map(fn($attr)=> "$attr,", $columns)); //attr1,attr2,attr3,
            $columns = static::removeNotation($columns, strlen($columns)-1); //attr1,attr2,attr3
        }
        return " ORDER BY $columns $sortBy ";
    }

    protected static function groupBy(string $column): string{
        return " GROUP BY $column ";
    }

    protected static function getQuery(string $query): string{
        return $query.';';   
    }

    protected static function getResult(string $query): array{
        $res = Application::$app->db->prepare($query);
        $res->execute();
        return $res->fetchAll(\PDO::FETCH_OBJ);
    }

}