<?php
/** User: Sabo */

namespace app\core;
use app\core\db\DbModel;

/** 
 * Class UserModel
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package app\core
*/

abstract class UserModel extends DbModel{

    /**
     * UserModel constructor
     * 
     */
    public function __construct(){
        //
    }
    
    abstract public function displayName(): string; 

}