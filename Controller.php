<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core;
use sabosuke\bit_mvc_core\middlewares\BaseMiddleware;

/** 
 * Class Controller
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core
*/

class Controller{
    
    public string $layout = 'main';
    public string $action = '';
    /**
     * @var \sabosuke\bit_mvc_core\middlewares\BaseMiddleware[]
     */
    protected array $middlewares = [];

    /**
     * Controller constructor
     * 
     */
    public function __construct(){
        //
    }

    public function setLayout($layout){
        $this->layout = $layout;
    }

    public function render($view, $params = []){
        return Application::$app->view->renderView($view, $params);
    }

    public function registerMiddleware(BaseMiddleware $middleware){
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares(): array{
        return $this->middlewares;
    }

}