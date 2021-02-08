<?php
/** User: Sabo */

namespace app\core\form;
use app\core\Model;

/** 
 * Class BaseField
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package app\core\form
*/

abstract class BaseField{

    public string $type;
    public Model $model;

    abstract public function renderInput(): string;

    /**
     * Field constructor
     * 
     * @param \app\core\Model $model
     * @param string $attribute
     */
    public function __construct(Model $model, string $attribute){
        $this->model = $model;
        $this->attribute = $attribute;
    }

    public function __toString(){
        return sprintf(
            '<div class="mb-3">
                <label class="form-label">%s</label>
                %s
                <div class="invalid-feedback">
                    %s
                </div>
            </div>', 
            $this->model->getLabel($this->attribute),
            $this->renderInput(),
            $this->model->getFirstError($this->attribute),
        );
    }
    
}