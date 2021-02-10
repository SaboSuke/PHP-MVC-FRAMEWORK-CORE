<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\theme;
use sabosuke\bit_mvc_core\Application;
use sabosuke\bit_mvc_core\db\DbModel;

/** 
 * Class ThemeModel
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\theme
*/

abstract class ThemeModel extends DbModel{
    
    public static ThemeModel $theme;

    /**
     * ThemeModel constructor
     * 
     */
    public function __construct(){
        
    }

    abstract public function displayName(): string; 
    
}