<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\form;
use sabosuke\bit_mvc_core\Model;

/**  
 * Class SelectField
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\form
*/

class SelectField extends BaseField{

    public const TYPE_TEXT = "text";
    public const TYPE_PASSWORD = "password";
    public const TYPE_EMAIL = "email";
    public const TYPE_NUMBER = "number";

    /**
     * InputField constructor
     * 
     * @param \sabosuke\bit_mvc_core\Model $model
     * @param string $attribute
     * @param string $placeholder
     */
    public function __construct(Model $model, string $attribute){
        parent::__construct($model, $attribute);
        return sprintf(
            ''
        );
    }

    public function addOption(string $value, string $text, string $selected = ""){
        return sprintf(
            '<option value="%s" %s>%s</option>',
            $value, 
            $selected,
            $text, 
        );
    }
    
    public function defaultOption(string $value, string $text, string $selected = "selected"){
        return sprintf(
            '<option value="%s" %s>%s</option>',
            $value, 
            $selected,
            $text, 
        );
    }

    public static function selectEnd(){
        return "</select>";
    }

    public function renderInput(): string{
        return sprintf(
            '<select name="%s" class="form-control%s" >',
            $this->attribute,
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
        );
    }

}