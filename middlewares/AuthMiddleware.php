<?php
/** User: Sabo */

namespace app\core\middlewares;

use app\core\Application;
use app\core\exception\ForbiddenException;

/** 
 * Class AuthMiddleware
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package app\core\middlewares
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