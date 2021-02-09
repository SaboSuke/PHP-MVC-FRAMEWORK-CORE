<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\middlewares;

use sabosuke\bit_mvc_core\Application;
use sabosuke\bit_mvc_core\exception\ForbiddenException;

/** 
 * Class AuthMiddleware
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\middlewares
*/

class AuthMiddleware extends BaseMiddleware{

    public array $actions;
    
    /**
     * AuthMiddleware constructor
     * 
     */
    public function __construct(array $actions = []){
        $this->actions = $actions;
    }

    public function execute(){
        if(Application::isGuest()){
            if(empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)){
                throw new ForbiddenException();
            }
        }
    }

}