<?php
/** User: Sabo */

namespace app\core\db;
use app\core\Model;
use app\core\Application;

/** 
 * Class Database
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package app\core\db
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
    
    abstract public function attributes(): array; //return all database column names
    
    abstract public function primaryKey(): string;

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

    public function findOne($where){ // [email => sabo@example.com, first_name => sabo]
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