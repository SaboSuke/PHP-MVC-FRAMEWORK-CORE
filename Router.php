<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core;
use sabosuke\bit_mvc_core\Request;
use sabosuke\bit_mvc_core\Response;

use sabosuke\bit_mvc_core\error_handler\exception\PageNotFoundException;
use sabosuke\bit_mvc_core\error_handler\ErrorHandler;

/** 
 * Class Router
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core
*/

class Router{
    
    public string $title;
    public Request $request;
    public Response $response;
    protected array $routes = [];
    public array $prevException = [];
    public int $prevIndex = 0;

    /**
     * Router constructor
     * 
     * @param \core\app\Request $request
     * @param \core\app\Response $response
     */
    public function __construct(Request $request, Response $response){
        $this->request = $request;
        $this->response = $response;
    }

    public function get($path, $callback){
        $this->routes['get'][$path] = $callback;
    }
    
    public function post($path, $callback){ 
        $this->routes['post'][$path] = $callback;
    }

    public function resolve(){
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if($callback === false){
            $error = new ErrorHandler();
            if ($error->registerError())
            throw new PageNotFoundException(
                $this->prevException[$this->prevIndex++] = strval(
                    $error->handleException(new PageNotFoundException())
                )
            );
        }
            
        if(is_string($callback)){
            return Application::$app->view->renderView($callback);
        }

        if(is_array($callback)){
            //creating an instance of the class $callback[0] = SiteController::class
            /** @var \sabosuke\bit_mvc_core\Controller $controller */
            $controller = new $callback[0](); //controller name
            Application::$app->controller = $controller;
            $controller->action = $callback[1];
            $callback[0] = $controller;
            foreach($controller->getMiddlewares() as $middleware){
                $middleware->execute();
            }
        }
        return call_user_func($callback, $this->request, $this->response);
    }

}