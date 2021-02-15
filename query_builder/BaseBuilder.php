<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\query_builder;
use sabosuke\bit_mvc_core\Application;

use sabosuke\bit_mvc_core\error_handler\exception\MethodNotFoundException;
use sabosuke\bit_mvc_core\error_handler\ErrorHandler;
use sabosuke\bit_mvc_core\error_handler\exception\QueryUnfinishedException;
use sabosuke\bit_mvc_core\error_handler\exception\BaseException;

/** 
 * Class BaseBuilder
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\query_builder
*/

class BaseBuilder{
    
    protected array $generatedColumnNames = [];

    /**
     * QueryBuilder constructor
     * 
     */
    public function __construct(){

    }

    protected static function mapArrayIntoString(array $array, string $implodeNotation){
        return implode($implodeNotation, array_map(fn($attr) => "$attr,", $array));
    }

    protected static function removeNotation($string, $length): string{
        return substr($string, 0, $length); 
    }

    protected static function execStatement($query){
        try{
            Application::$app->db->pdo->exec($query);
        }catch(\Exception $e){
            return false;
        }
        return true;
    }

    protected function randomizeString($prev){
        $seed = str_split('abcdefghijklmnopqrstuvwxyz'.'ABCDEFGHIJKLMNOPQRSTUVWXYZ'); // only alphabetic characters
        shuffle($seed); // probably optional since array_is randomized; this may be redundant
        $rand = '';
        foreach (array_rand($seed, 10) as $k) $rand .= $seed[$k];
        if($rand != $prev)
            return $rand;
        else
            return $this->randomizeString($rand);
    }

    protected function generateColumnName(string $prevColumnName = ""){
        $rand = $this->randomizeString($prevColumnName);
        
        if (in_array($rand, $this->generatedColumnNames))
            return $this->generateColumnName($rand);
        else
            return $rand;
    }
        
}