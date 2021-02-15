<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\query_builder;
use sabosuke\bit_mvc_core\Application;

use sabosuke\bit_mvc_core\error_handler\exception\MethodNotFoundException;
use sabosuke\bit_mvc_core\error_handler\ErrorHandler;
use sabosuke\bit_mvc_core\error_handler\exception\QueryUnfinishedException;
use sabosuke\bit_mvc_core\error_handler\exception\BaseException;

/** 
 * Class QueryBuilder
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\query_builder
*/
//extends BaseBuilder
class QueryBuilder extends BaseBuilder{

    private $query = null;
    private $subQuery = null;
    private $METHODS = [
        "JOIN", "INNER_JOIN", "OUTER_JOIN", "LEFT_JOIN", "RIGHT_JOIN"
    ];
    private $FETCH_TYPES = [
        "\PDO::ATTR_DEFAULT_FETCH_MODE",
        "\PDO::FETCH_ASSOC", 
        "\PDO::FETCH_OBJ", 
        "\PDO::FETCH_UNIQUE",
        "\PDO::FETCH_BOTH",
        "\PDO::FETCH_COLUMN", 
        "\PDO::FETCH_CLASS", 
        "\PDO::FETCH_FUNC",
        "\PDO::FETCH_GROUP",
        "\PDO::FETCH_COLUMN | \PDO::FETCH_GROUP", //group by column
    ];

    private $RESPONSE_MESSAGES = [
        "success" => [
            "select" => "Data Has Been Selected Successfully",
            "insert" => "Data Has Been Inserted Successfully",
            "delete" => "Data Has Been Deleted Successfully",
            "update" => "Data Has Been Updated Successfully",
        ],
        "failed" => [
            "select" => "Failed To Select Data",
            "insert" => "Failed To Insert Data",
            "delete" => "Failed To Delete Data",
            "update" => "Failed To Update Data",
        ],
        "error"=>[]
    ];

    private array $action = [];
    private $isBindable = false;
    private array $bindableParameters = [];

    private array $prevException = [];
    private int $prevIndex = 0;

    /**
     * QueryBuilder constructor
     * 
     */
    public function __construct(){

    }

    private static function prepare($query){
        return Application::$app->db->prepare($query);
    }
    
    public function newTable(string $statement){
        return self::execStatement($statement);
    }
    public function dropTable(string $tableName){
        return self::execStatement("DROP TABLE $tableName;");
    }
    public function addColumn(string $statement){
        return self::execStatement($statement);
    }
    public function dropColumn(string $columnName, string $tableName){
        return self::execStatement("ALTER TABLE $tableName DROP COLUMN $columnName;");
    }
    public function modifyColumn(string $statement){
        return self::execStatement($statement);
    }

    public function initQuery(): self{
        $this->query = null;
        $this->subQuery = null;
        $this->action = [];
        $this->isBindable = false;
        $this->bindableParameters = [];
        return $this;
    }

    public function getQuery(): string{
        return $this->query;
    }

    public function verifyQuery($subQuery){
        if ($this->query != null) return $this->query . $subQuery;
        else return $subQuery;
    }

    /**
     * select data from the database based on the parameters you
     * can select all columns or specify the once you want to select
     * you can also create an alias for the table name so you can use it
     * join tables afterwards
     * 
     * @param string $tableName
     * @param string $alias
     * @param array|string $columns
     * @return self
     */
    public function select(string $tableName, ?string $tableAlias = null, ?array $columns = null): self{
        if ($columns == [])
            $this->subQuery = "SELECT * FROM $tableName $tableAlias ";
        elseif ($columns !== []){   
            //$map = implode('', array_map(fn($attr) =>"$attr,", $columns));
            $map = static::mapArrayIntoString($columns, ' '); //string = attr1, attr2, attr3, 
            $map = self::removeNotation($map, strlen($map)-1); //string = attr1, attr2, attr3
            $this->subQuery  = "SELECT $map FROM $tableName $tableAlias ";
        }
        $this->action = [
            'method' => 'select', 
            'bindable' => $this->isBindable = false,
            'complete' => true
        ];
        $this->query = $this->verifyQuery($this->subQuery);

        return $this;
    }

    /**
     * select data from the database using a specified function
     * like: MIN, MAX, AVG, SUM, COUNT
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $function
     * @param string $tableAlias
     * @param string $columnAlias
     * @return self
     */
    public function selectUsingFunction(
        string $tableName, 
        string $columnName,
        string $function,
        ?string $tableAlias = null, 
        ?string $columnAlias = null
    ): self{
        if ($columnAlias != null)
            $this->subQuery = "SELECT $function($columnName) as $columnAlias FROM $tableName $tableAlias ";
        else
            $this->subQuery = "SELECT $function($columnName) FROM $tableName $tableAlias ";

            $this->action = [
                'method' => 'select--func', 
                'bindable' => $this->isBindable = false,
                'complete' => true
            ];
        $this->query = $this->verifyQuery($this->subQuery);
        return $this;
    }

    private function bindableParameters(?array $columns = [], array $values){
        if(empty($columns)){
            $columns = $this->generatedColumnNames;
        }
        $i = 0;
        foreach($columns as $attribute) $this->bindableParameters[$attribute] = is_int($values[$i]) ? (int)$values[$i++] : $values[$i++];
        var_dump($this->bindableParameters);
    }

    /**
     * inserts a new record in the database
     *
     * @param string $tableName
     * @param array $columns | if null you have to pass all values for each column in the database in the same order 
     * @param array $values
     * @return self
     */
    public function insert(string $tableName, ?array $columns = null, array $values): self{
        if ($columns == null){
            for($i=0; $i < count($values); $i++){
                $this->generatedColumnNames[$i] = $this->generateColumnName('');
            }
            $values_map = implode('', array_map(fn($attr)=> ":$attr,", $this->generatedColumnNames));
            $values_map = self::removeNotation($values_map, strlen($values_map)-1);
            $this->subQuery = "INSERT INTO $tableName VALUES($values_map)";
            $this->bindableParameters($columns, $values);
        }else{
            $this->bindableParameters($columns, $values);
            $values_map = implode('', array_map(fn($attr)=> ":$attr,", $columns));
            $values_map = self::removeNotation($values_map, strlen($values_map)-1);
            
            $columns_map = implode('', array_map(fn($attr)=> "$attr,", $columns));
            $columns_map = self::removeNotation($columns_map, strlen($columns_map)-1);
            $this->subQuery = "INSERT INTO $tableName ($columns_map) VALUES($values_map)";
        }
        
        $this->query = $this->verifyQuery($this->subQuery);
        $this->action = [
            'method' => 'insert', 
            'bindable' => $this->isBindable = true, 
            'complete' => true
        ];
        
        return $this;
    }

    /**
     * updates an existing record in the database
     *
     * @param string $tableName
     * @param array $columns
     * @param array $values
     * @return self
     */
    public function update(string $tableName, array $columns, array $values): self{
        $columns_map = array_map(fn($attr)=> "$attr = %s", $columns); //['id = %s', 'name = %s','last_name = %s']
        for($i=0; $i < count($values); $i++){
            $this->generatedColumnNames[$i] = $this->generateColumnName('');
        }
        $values_map = array_map(fn($attr)=> ":$attr, ", $this->generatedColumnNames); //['1, ' , 'essam, ','abed, ']
        $a = [];
        $i = 0; 
        foreach($columns_map as $val){
            $a[$i] = sprintf(
                $val,
                $values_map[$i]
                );
                $i++;
        }
        $this->bindableParameters($this->generatedColumnNames, $values);
        
        $map = trim(implode("", $a)); //id = 1, name = essam, last_name = abed,
        $map = self::removeNotation($map, strlen($map)-1); //id = 1, name = essam, last_name = abed

        $this->query = $this->verifyQuery($this->subQuery = "UPDATE $tableName SET $map ");
        $this->action = [
            'method' => 'update', 
            'bindable' => $this->isBindable = true, 
            'complete' => false
        ];
        
        return $this;
    }

    /**
     * creates a condition on a select, delete, update function that you've 
     * previously executed.
     * the function supports multiple conditions
     *
     * @depends select | delete | update
     * @param string $condition
     * @return self
     */
    public function where(string $condition): self{
        $this->query = $this->verifyQuery($this->subQuery = " WHERE $condition ");
        $this->action["complete"] = true;
        
        return new static();
    }

    /**
     * get the final result of the query
     *
     * @param string|null $FETCH_TYPE
     * @return mixed
     */
    public function getResult(?string $FETCH_TYPE = "\PDO::FETCH_ASSOC"){
        if(in_array($FETCH_TYPE, $this->FETCH_TYPES)){
            try{
                if($this->isBindable && $this->action['complete'] && !empty($this->query)){
                    $res = self::prepare($this->query);
                    echo $this->query;
                    foreach($this->bindableParameters as $column => $value){
                        if(is_int($value))
                            $res->bindValue(":$column", $value, \PDO::PARAM_INT);
                        elseif(is_string($value))
                            $res->bindValue(":$column", $value, \PDO::PARAM_STR);
                    }
                }elseif(!$this->action['complete']){
                    $e = new QueryUnfinishedException();
                    $e->set_logger($e->generateExceptionErrorMessage($e, $e->getMessage()));
                    var_dump($e::$calls);
                    exit();
                }else{
                    $res = self::prepare($this->query);
                }
                
                $res->execute();

                switch($this->action['method']){
                    case 'select': case 'select--func':
                        return $res->fetchAll((int)$FETCH_TYPE);
                        break;
                    case 'insert': case 'update': case 'delete':
                        $a ['success']  =  $res->rowCount() ? 1 : 0;
                        $a ['message'] = $a ['success']  
                            ? $this->RESPONSE_MESSAGES['success'][$this->action['method']] 
                            : $this->RESPONSE_MESSAGES['failed'][$this->action['method']] ;
                        return $a;
                        break;
                    default:
                        return $res->fetchAll((int)$FETCH_TYPE);
                        break;
                }
            }catch(\Exception $e){/*ignoring sql syntax violation exception*/}
        }else{
            $e = new \Exception("Fetch Type Not Found: The fetch type you entered does not exist");
            $exception = new BaseException();
            $exception->set_logger($e->getMessage());
            var_dump($exception::$calls);
            exit();
        }
    }
    
    /**
     * deletes an existing record in the database
     *
     * @param string $tableName
     * @return self
     */
    public function delete(string $tableName): self{
        $this->query = $this->verifyQuery($this->subQuery = "DELETE FROM $tableName ");
        $this->action["complete"] = false;
        return $this;
    }
    
    /**
     * joins two tables on a specified condition with a specific method of your choosing
     * note that you can create an alias for the table name
     *
     * @param string $method = JOIN | INNER_JOIN | OUTER_JOIN | LEFT_JOIN | RIGHT_JOIN
     * @param string $tableName
     * @param string $tableAlias
     * @param string $condition
     * @return self
     */
    public function join(string $method = "INNER_JOIN", string $tableName, ?string $tableAlias = null, string $condition): self{
        if (in_array(!$method, $this->METHODS)){
            $error = new ErrorHandler();
            if ($error->registerError())
            throw new MethodNotFoundException(
                $this->prevException[$this->prevIndex++] = strval(
                    $error->handleException(new MethodNotFoundException())
                )
            );
        }else{
            $m = ["JOIN", "INNER_JOIN", "OUTER_JOIN", "LEFT_JOIN", "RIGHT_JOIN"];
            $m = " JOIN $tableName $tableAlias ON $condition ";
            switch($method){
                case $this->METHODS[0]: 
                    $this->subQuery = $m;
                    break;
                case $this->METHODS[1]: case $this->METHODS[2]: case $this->METHODS[3]: case $this->METHODS[4]:
                    $this->subQuery = ' ' . $method . $m;
                    break;
                default:
                    break;
            }
        }
        $this->query = $this->verifyQuery($this->subQuery);
        return $this;
    }
    
    /**
     * join 2 tables knowing that they have the same column name 
     * USING($columnName)
     *
     * @param string $tableName
     * @param string $columnName
     * @return self
     */
    public function joinUsing(string $tableName, string $columnName): self{
        $this->query = $this->verifyQuery($this->subQuery = " JOIN $tableName USING($columnName) ");
        return $this;
    }

    /**
     * Adds anther select statement inside of the where
     * for example: 
     * WHERE EXISTS(SELECT column_name FROM table_name WHERE condition);
     * WHERE column_name operator ANY (SELECT column_name FROM table_name WHERE condition);
     * operators: =, <>, !=, >, >=, <, or <=
     * methods: All, IN, EXISTS
     * 
     * @depends select | delete | update
     * @param string $condition
     * @return self
     */
    public function andWhere(string $condition, string $selectStatement): self{
        $this->query = $this->verifyQuery($this->subQuery = " WHERE $condition($selectStatement) ");
        $this->action["complete"] = true;
        return $this;
    }

    /**
     * because the WHERE keyword could not be used with aggregate functions.
     * having will take care of that work.
     *
     * @param string $condition
     * @return string
     */
    public function having(string $condition): self{
        $this->query = $this->verifyQuery($this->subQuery = " HAVING $condition ");
        return $this;
    }

    /**
     * sorts the result-set in ascending or descending order.
     * it accepts 1 column name(string)  or an array of column names(array of strings).
     * 
     * @param mixed $columns
     * @param string $sortBy
     * @return self
     */
    public function orderBy($columns, ?string $sortBy = "ASC"): self{
        if(!is_string($columns)){
            $columns = implode('', array_map(fn($attr)=> "$attr,", $columns)); //attr1,attr2,attr3,
            $columns = static::removeNotation($columns, strlen($columns)-1); //attr1,attr2,attr3
        }
        $this->query = $this->verifyQuery($this->subQuery = " ORDER BY $columns $sortBy ");
        return $this;
    }

    /**
     * statement groups rows that have the same values into summary rows, 
     * like "find the number of customers in each country"
     * it accepts a columnName or an alias for that column.
     *
     * @param string $columnName
     * @return self
     */
    public function groupBy(string $column): self{
        $this->query = $this->verifyQuery($this->subQuery = " GROUP BY $column ");
        return $this;
    }

}