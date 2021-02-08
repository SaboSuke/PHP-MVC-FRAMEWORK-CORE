<?php
/** User: Sabo */

namespace app\core;
use app\core\Router;
use app\core\Request;
use app\core\Response;
use app\core\Controller;
use app\core\db\DbModel;
use app\core\db\Database;
use app\core\View;

/** 
 * Class Application
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package app\core
*/

class Application{

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
    public ?UserModel $user; //=> it might be null
    public View $view;


    /**
     * Application constructor
     * 
     * @param $ROOT_DIRECTORY
     */
    public function __construct($rootPath, array $config){
        $this->userClass = $config['userClass'];
        self::$ROOT_DIRECTORY = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();
        
        $this->db = new Database($config['db']);

        $primaryValue = $this->session->get('user');
        if ($primaryValue){
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        }else
            $this->user = null;
    }

    public function run(){
        try{
            echo $this->router->resolve();
        }catch(\Exception $e){   
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('_exception', [
                    'exception' => $e
            ]);
        }
    }

    /**
     * @param \app\core\Controller $controller
     */
    public function getController(){
        return $this->controller;
    }
    
    /**
     * @param \app\core\Controller $controller
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