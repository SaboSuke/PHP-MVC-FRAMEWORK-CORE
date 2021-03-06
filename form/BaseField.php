<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\form;
use sabosuke\bit_mvc_core\Model;

/** 
 * Class BaseField
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\form
*/

abstract class BaseField{

    public string $type;
    public Model $model;

    abstract public function renderInput(): string;

    /**
     * Field constructor
     * 
     * @param \sabosuke\bit_mvc_core\Model $model
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