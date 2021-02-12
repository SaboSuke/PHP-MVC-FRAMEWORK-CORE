<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\query_builder;
use sabosuke\bit_mvc_core\db\Database;
use sabosuke\bit_mvc_core\Application;

/** 
 * Class BaseQueryBuilder
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\query_builder
*/

abstract class BaseQueryBuilder{

    public Database $db;

    /**
     * BaseQueryBuilder constructor
     * 
     */
    public function __construct(){
        
    }

    /**
     * select data from the database based on the parameters you
     * can select all columns or specify the once you want to select
     * you can also create an alias for the table name so you can use it
     * join tables afterwards
     * 
     * @param string $tableName
     * @param string $alias
     * @param array $columns
     * @return string
     */
    abstract protected static function select(string $tableName, ?string $tableAlias = null, ?array $columns = []): string;

    /**
     * select data from the database using a specified function
     * like: MIN, MAX, AVG, SUM, COUNT
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $function
     * @param string $tableAlias
     * @param string $columnAlias
     * @return string
     */
    abstract protected static function selectUsingFunction(
        string $tableName, 
        string $columnName, 
        string $function,
        ?string $tableAlias = null, 
        ?string $columnAlias = null
    ): string;
    
    /**
     * inserts a new record in the database
     *
     * @param string $tableName
     * @param array $columns
     * @param array $values
     * @return string
     */
    abstract protected static function insert(string $tableName, ?array $columns = [], array $values): string;

    /**
     * updates an existing record in the database
     *
     * @param string $tableName
     * @param array $columns
     * @param array $values
     * @return string
     */
    abstract protected static function update(string $tableName, array $columns, array $values): string;
    
    /**
     * deletes an existing record in the database
     *
     * @param string $tableName
     * @return string
     */
    abstract protected static function delete(string $tableName): string;

    /**
     * creates a condition on a select, delete, update function that you've 
     * previously executed.
     * the function supports multiple conditions
     *
     * @param string $condition
     * @return string
     */
    abstract protected static function where(string $condition): string;
    
    /**
     * adds anther select statement inside of the where
     * for example: 
     * WHERE EXISTS(SELECT column_name FROM table_name WHERE condition);
     * WHERE column_name operator ANY (SELECT column_name FROM table_name WHERE condition);
     * operators: =, <>, !=, >, >=, <, or <=
     * methods: All, IN, EXISTS
     * 
     * @param string $condition
     * @return string
     */
    abstract protected static function andWhere(string $condition, string $selectStatement): string;

    /**
     * because the WHERE keyword could not be used with aggregate functions.
     * having will take care of that work.
     *
     * @param string $condition
     * @return string
     */
    abstract protected static function having(string $condition): string;

    /**
     * sorts the result-set in ascending or descending order.
     * it accepts 1 column name(string)  or an array of column names(array of strings).
     * 
     * @param mixed $columns
     * @param string $sortBy
     * @return string
     */
    abstract protected static function orderBy($columns, ?string $sortBy = "ASC"): string;

    /**
     * statement groups rows that have the same values into summary rows, 
     * like "find the number of customers in each country"
     * it accepts a columnName or an alias for that column.
     *
     * @param string $columnName
     * @return string
     */
    abstract protected static function groupBy(string $column): string;
    
    /**
     * joins two tables on a specified condition
     * you can also create an alias for the table name
     *
     * @param string $tableName
     * @param string $tableAlias
     * @param string $condition
     * @return string
     */
    abstract protected static function joinTableOn(string $tableName, ?string $tableAlias = null, string $condition): string;

    /**
     * join 2 tables knowing that they have the same column name 
     * USING($columnName)
     *
     * @param string $tableName
     * @param string $columnName
     * @return string
     */
    abstract protected static function joinTableUsing(string $tableName, string $columnName): string;

    /**
     * inner joins two tables on a specified condition
     * you can also create an alias for the table name
     *
     * @param string $tableName
     * @param string $alias
     * @param string $condition
     * @return string
     */
    abstract protected static function innerJoin(string $tableName, string $tableAlias = null, string $condition): string;
    
    /**
     * left joins two tables on a specified condition
     * you can also create an alias for the table name
     *
     * @param string $tableName
     * @param string $alias
     * @param string $condition
     * @return string
     */
    abstract protected static function leftJoin(string $tableName, ?string $tableAlias = null, string $condition): string;
    
    /**
     * right joins two tables on a specified condition
     * you can also create an alias for the table name
     *
     * @param string $tableName
     * @param string $alias
     * @param string $condition
     * @return string
     */
    abstract protected static function rightJoin(string $tableName, ?string $tableAlias = null, string $condition): string;
    
    /**
     * outer or full joins two tables on a specified condition
     * you can also create an alias for the table name
     *
     * @param string $tableName
     * @param string $alias
     * @param string $condition
     * @return string
     */
    abstract protected static function outerJoin(string $tableName, ?string $tableAlias = null, string $condition): string;

    /**
     * returns finishing the query using select where or any other
     * method above this will return the final result of that query
     *
     * @param string $query
     * @return string
     */
    abstract protected static function getQuery(string $query): string;
    
    /**
     * returns the result of a given query
     *
     * @param string $query
     * @return array
     */
    abstract protected static function getResult(string $query): array;
    
}