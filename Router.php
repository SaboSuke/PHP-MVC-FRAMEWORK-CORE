<?php
/** User: Sabo */

namespace app\core;
use app\core\Request;
use app\core\Response;
use app\core\exception\NotFoundException;

/** 
 * Class Router
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package app\core
*/

class Router{
    
    public string $title;
    public Request $request;
    public Response $response;
    protected array $routes = [];

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
            throw new NotFoundException();
        }
            
        if(is_string($callback)){
            return Application::$app->view->renderView($callback);
        }

        if(is_array($callback)){
            //creating an instance of the class $callback[0] = SiteController::class
            /** @var \app\core\Controller $controller */
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