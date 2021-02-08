<?php
/** User: Sabo */

namespace app\core;

/** 
 * Class View
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package app\core
*/

class View{
    
    public string $title = '';  

    public function renderView($view, $params = []){
        $view_content = $this->renderOnlyView($view, $params);
        $layout_content = $this->layoutContent();
        return str_replace('{{content}}', $view_content, $layout_content);
    }

    //content rendering passed in parameter
    public function renderContent($view_content){
        $layout_content = $this->layoutContent();
        return str_replace('{{content}}', $view_content, $layout_content);
    }
    
    protected function layoutContent(){
        $layout = Application::$app->layout;
        if(Application::$app->controller){
            $layout = Application::$app->controller->layout;
        }
        //start the output caching ==> nothing is outputted to the browser
        ob_start();
        include_once Application::$ROOT_DIRECTORY."/views/layouts/$layout.php";
        return ob_get_clean(); // returns whatever is already buffered and clears the buffer
    }

    protected function renderOnlyView($view, $params){
        foreach($params as $key => $val){
            /*  $key = "name"
                $$key = $name   */
            $$key = $val;
        }
        ob_start();
        include_once Application::$ROOT_DIRECTORY."/views/$view.php"; 
        return ob_get_clean();  
    }

}