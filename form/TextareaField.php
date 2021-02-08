<?php
/** User: Sabo */

namespace app\core\form;
use app\core\Model;

/** 
 * Class TextareaField
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package app\core\form
*/

class TextareaField extends BaseField{

    public string $placeholder = "";

    /**
     * InputField constructor
     * 
     * @param \app\core\Model $model
     * @param string $attribute
     * @param string $placeholder
     */
    public function __construct(Model $model, string $attribute, string $placeholder){
        $this->placeholder = $placeholder;
        parent::__construct($model, $attribute);
    }

    public function renderInput(): string{
        return sprintf(
            '<textarea name="%s" class="form-control%s" placeholder="%s">%s</textarea>',
            $this->attribute,
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
            $this->placeholder,
            $this->model->{$this->attribute},
        );
    }

}