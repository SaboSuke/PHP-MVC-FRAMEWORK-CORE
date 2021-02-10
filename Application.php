<?php
/** User: Sabo */

namespace sabosuke\bit_mvc_core;

use sabosuke\bit_mvc_core\Router;
use sabosuke\bit_mvc_core\Request;
use sabosuke\bit_mvc_core\Response;
use sabosuke\bit_mvc_core\Controller;
use sabosuke\bit_mvc_core\db\DbModel;
use sabosuke\bit_mvc_core\db\Database;
use sabosuke\bit_mvc_core\View;

use \sabosuke\bit_mvc_core\theme\ThemeModel;

/** 
 * Class Application
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package sabosuke\bit_mvc_core
*/

class Application{

    public const EVENT_BEFORE_REQUEST = 'beforeRequest';
    public const EVENT_AFTER_REQUEST = 'afterRequest';

    protected array $eventListeners = [];

    public string $userClass;

    public string $layout = 'main';
    public static string $ROOT_DIRECTORY;
    public static Application $app;
    public Router $router;
    public Request $request;
    public Response $response;
    public ?Controller $controller = null;
    public Session $session;
    public Database $db;
    public ?UserModel $user;
    public View $view;

    /**
     * Application constructor
     * 
     * @param $ROOT_DIRECTORY
     */
    public function __construct($rootPath, array $config){
        self::$ROOT_DIRECTORY = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();
        
        $this->db = new Database($config['db']);
        
        if ($config['userClass']?? false){

            $this->userClass = $config['userClass'];
            $primaryValue = $this->session->get('user');
            if ($primaryValue){
                $primaryKey = $this->userClass::primaryKey();
                $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
            }else
                $this->user = null;
        }
    }

    public static function InitTheme(){
        return new ThemeModel();
    }

    public function run(){
        $this->triggerEvent(self::EVENT_BEFORE_REQUEST);
        try{
            echo $this->router->resolve();
        }catch(\Exception $e){   
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('_exception', [
                    'exception' => $e
            ]);
        }
    }

    public function triggerEvent($eventName){
        $callbacks = $this->eventListeners[$eventName] ?? [];
        foreach($callbacks as $callback){
            call_user_func($callback);
        }
    }

    public function on($eventName, $callback){
        $this->eventListeners[$eventName][] = $callback;
    }

    /**
     * @param \sabosuke\bit_mvc_core\Controller $controller
     */
    public function getController(){
        return $this->controller;
    }
    
    /**
     * @param \sabosuke\bit_mvc_core\Controller $controller
     */
    public function setController(Controller $controller){
        $this->controller = $controller;
    }

    public function login(UserModel $user){
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }

    public function logout(){
        $this->user = null;
        $this->session->remove('user');
    }

    public static function isGuest(){
        return !self::$app->user;
    }

}