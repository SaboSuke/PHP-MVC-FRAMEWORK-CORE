<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\db;
use sabosuke\bit_mvc_core\Model;
use sabosuke\bit_mvc_core\Application;

/** 
 * Class Database
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\db
*/

abstract class DbModel extends Model{

    /**
     * DbModel constructor
     * 
     */
    public function __construct(){
        $this->db = Application::$app->db;
    }
    
    abstract public function tableName(): string; //return table name
    
    abstract public function attributes(): array; //return all database columns names
    
    abstract public function primaryKey(): string;

    /**
     * saves a new record inside the database
     *
     * @return boolean
     */
    public function save(){
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr)=> ":$attr", $attributes);

        $st = self::prepare(
            "INSERT INTO $tableName (".implode(',', $attributes).")
            VALUES(".implode(',', $params).")"
        );

        foreach($attributes as $attribute){
            $st->bindValue(":$attribute", $this->{$attribute});
        }

        $st->execute();
        return true;
    }

    public static function prepare($query){
        return Application::$app->db->pdo->prepare($query);
    }

    /**
     * find all records in the database
     *
     * @return array
     */
    public function findAll(): array {
        $tableName = static::tableName();
        $st = self::prepare(
            "SELECT * FROM $tableName"
        );
        $st->execute();
        
        return $st->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * fetch one field from the database and returns it
     *
     * @param array $where
     * @return array
     */
    public function findOne_obj(array $where): array{
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $query = implode("AND", array_map(fn($attr) =>"$attr = :$attr", $attributes));
        $st = self::prepare(
            "SELECT * FROM $tableName
            WHERE $query"
        );
        foreach($where as $key => $item){
            $st->bindValue(":$key", $item);
        }
        $st->execute();
        
        return $st->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * find one record in the database depends on the where and return an object of type class
     *
     * @param array $where
     * @return object
     */
    public function findOne(array $where): object{ // [email => sabo@example.com, first_name => sabo]
        $tableName = static::tableName();
        $attributes = array_keys($where);
        // $array = array_map(fn($attr) =>"$attr = :$attr", $attributes);
        // $combining = implode("AND", $array);
        $query = implode("AND", array_map(fn($attr) =>"$attr = :$attr", $attributes));
        $st = self::prepare(
            "SELECT * FROM $tableName
            WHERE $query"
        );
        foreach($where as $key => $item){
            $st->bindValue(":$key", $item);
        }
        $st->execute();
        return $st->fetchObject(static::class);
    }

}