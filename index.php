<?php 

require 'Routing.php';
require 'functions.php';
require 'connection.php';

$router = new Route;

$sql    = new sqlManager;

//Routing set

$router->get('/login','login');
$router->get('/register','register');
$router->get('/home','home');

$router->post('/login',function(){
 
   $validate = new validate;
   $validate->make($_REQUEST,[
       'email' => 'required|email',
       'password' => 'required|min:8|max:20'
   ]);

   if($validate->fails()) {  
       header('location:/login');
   } else {
    Auth::login($_REQUEST['email'],$_REQUEST['password']);
   }

});

$router->post('/register',function(){
    $validate = new validate;
    $validate->make($_REQUEST,[
        'name'  => 'required|min:3',
        'email' => 'required|email',
        'password' => 'requrired|min:3'
    ]);
    
    if($validate->fails()) {
        header('location:/register');
    } else {
        Auth::register($_REQUEST['email'],$_REQUEST['name'],$_REQUEST['password']);
    }
});

$router->resolve();