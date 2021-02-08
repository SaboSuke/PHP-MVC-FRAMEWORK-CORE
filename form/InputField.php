<?php
/** User: Sabo */

namespace app\core\form;
use app\core\Model;

/** 
 * Class InputField
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package app\core\form
*/

class InputField extends BaseField{

    public const TYPE_TEXT = "text";
    public const TYPE_PASSWORD = "password";
    public const TYPE_EMAIL = "email";
    public const TYPE_NUMBER = "number";
    
    public string $type;
    public string $placeholder;

    /**
     * InputField constructor
     * 
     * @param \app\core\Model $model
     * @param string $attribute
     * @param string $placeholder
     */
    public function __construct(Model $model, string $attribute, string $placeholder){
        $this->type = self::TYPE_TEXT;
        $this->placeholder = $placeholder;
        parent::__construct($model, $attribute);
    }

    public function passwordInputField(){
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }
    
    public function emailInputField(){
        $this->type = self::TYPE_EMAIL;
        return $this;
    }

    public function renderInput(): string{
        return sprintf(
            '<input type="%s" name="%s" value="%s" class="form-control%s" placeholder="%s..">',
            $this->type,
            $this->attribute,
            $this->model->{$this->attribute},
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
            $this->placeholder
        );
    }

}