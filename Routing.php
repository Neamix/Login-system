<?php 


class Route {
    private array $route = [];

    public function get($path,$page) 
    {
       $this->route['get'][$path] = [$page];
    }

    public function post($path,$page)
    {
       $this->route['post'][$path] = [$page];
    }

    public function method()
    {
       return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function path()
    {
       return strtolower($_SERVER['REQUEST_URI']);
    }

    public function resolve() 
    {
       $path   = $this->path();
       $method = $this->method();

       $page   = $this->route[$method][$path] ?? '404';
       
       (is_callable($page)) ? 'yea' : 'nope';
       if(is_callable($page[0])) {
          call_user_func_array($page[0],[]);
       } else {
        if($page !== '404') {
            include("asset/components/$page[0].php");
        } else {
            echo '404';
        }
       }
    }
}