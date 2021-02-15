<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core\middlewares;

use sabosuke\bit_mvc_core\Application;
use sabosuke\bit_mvc_core\error_handler\exception\ForbiddenException;
use sabosuke\bit_mvc_core\error_handler\exception\BaseForbiddenException;
use sabosuke\bit_mvc_core\error_handler\ErrorHandler;

/** 
 * Class AuthMiddleware
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core\middlewares
*/

class AuthMiddleware extends BaseMiddleware{

    public array $actions;
    public array $prevException = [];
    public  int $prevIndex = 0;
    
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
                $error = new ErrorHandler();
                $error->registerError();
                throw new BaseForbiddenException(
                    $this->prevException[$this->prevIndex++] = strval(
                        $error->handleException(new BaseForbiddenException())
                    )
                );
            }
        }
    }

}