<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\form;
use sabosuke\bit_mvc_core\Model;

/** 
 * Class TextareaField
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\form
*/

class TextareaField extends BaseField{

    public string $placeholder = "";

    /**
     * InputField constructor
     * 
     * @param \sabosuke\bit_mvc_core\Model $model
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