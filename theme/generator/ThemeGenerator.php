<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\theme\generator;

/** 
 * Class ThemeGenerator
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\theme\generator
*/

class ThemeGenerator{

    /**
     * ThemeModel constructor
     * 
     */
    public function __construct(){
        
    }

    /**
     * generates a string of options out of an array
     *
     * @param array $options
     * @return string
     */
    public static function generateThemeOptions(array $options): string{
        $attributes = array_values($options); //array_keys($options);
        $map = array_map(fn($attr)  => "<li><a class=\"dropdown-item\" target=\"_self\" href=\"/change-theme?name=$attr\">$attr</a></li>", $attributes);
        $map = implode('', $map); 
        return $map;
    }
    
    /**
     * generates a selection element for the themes using bootstrap
     *
     * @param string $buttonName
     * @param array $options
     * @param string $icon
     * @return string
     */
    public static function generateThemeSelection(
        string $buttonName, 
        array $options,
        string $icon=''
        ): string{
        return sprintf(
            '<div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <div class="d-flex">
                        <div class="mr-2 align-self-center">%s</div>
                        <div class="d-flex">%s</div></div>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    %s
                </ul>
            </div>',
            $icon,
            $buttonName,
            self::generateThemeOptions($options)
        );
    }
    
}