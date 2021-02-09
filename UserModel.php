<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core;
use sabosuke\bit_mvc_core\db\DbModel;

/** 
 * Class UserModel
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core
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