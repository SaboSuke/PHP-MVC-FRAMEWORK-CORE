<?php
/** User: Sabo */

namespace app\core\form;
use app\core\Model;

/**
 * Class Form
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package app\core\form
*/

class Form{

    public static function begin(string $action, string $method){
        echo sprintf('<form action="%s" method="%s">', $action, $method);
        return new Form();
    }
    
    public static function end(){
        return '</form>';
    }

    public function field(Model $model, string $attribute, string $placeholder){
        return new InputField($model, $attribute, $placeholder);
    }
    
    public function TextareaField(Model $model, string $attribute, string $placeholder){
        return new TextareaField($model, $attribute, $placeholder);
    }

}