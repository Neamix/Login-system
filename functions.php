<?php 

class validate extends session{
    public array  $errors = [];
    public  function make($request,$rules) 
    {
       foreach($rules as $key => $rule ) 
       {
           $exp = explode('|',$rule);
           foreach($exp as $item) {
               $exp = explode(':',$item);
            //    print_r($exp);
               if($exp[0] == 'required' ) 
               {
                 $this->required($request[$key],$key);
               } 
               else if ($exp[0] == 'min')
               {
                 $this->min($request[$key],$exp[1],$key);
               }
               else if ($exp[0] == 'max')
               {
                 $this->max($request[$key],$exp[1],$key);
               } else if ($exp[0] == 'email')
               {
                   $this->email($request[$key],$key);
               }
           }
       }  
       
    }

    private function required($val,$key) 
    {
        return (isset($val) && strlen($val) > 0) ? 'false' : $this->errors[$key]['requried'] = $this->message('required'); 
    }

    private function min($val,$rule,$key)
    {
        return (strlen($val) >= $rule) ? false : $this->errors[$key]['min'] = $this->message('min',$key,$rule); 
    }

    private function max($val,$rule,$key)
    {
        return (strlen($val) <= $rule) ? false : $this->errors[$key]['max'] = $this->message('max',$key,$val); 
    }

    private function email($val,$key)
    {

        return (filter_var($val,FILTER_VALIDATE_EMAIL)) ? false : $this->errors[$key]['email'] = $this->message('email');
    }

    private function message($error,$key='',$val='')
    {
        $messages = [
            'required' => 'This field is required',
            'min'      => "Too short $key it must be at least. $val characters",
            'max'      => "Too long $key it must be at max. $val",
            'email'    => 'Enter valid email'
        ];

        return $messages[$error];
    }

    public function fails() 
    {
        return (COUNT($this->errors) > 0) ? true : false;
    }

    public function error($field) 
    {
       if(!empty($this->errors)) {
           session::insert('errors',$this->errors);
       } else {
           session::delete('errors');
       }
    }
}

class session {
    public function create() 
    {
        if(session_status() !== PHP_SESSION_ACTIVE ) 
        {
            session_start();
        }
    }

    protected static function insert($key,$value)
    {
       (new self)->create();

       $_SESSION[$key] = $value;
    }

    public static function get($key) 
    {
        (new self)->create();
        return (isset($_SESSION[$key])) ? $_SESSION[$key] : false;
    }

    public static function delete($key) 
    {
       if(self::get($key) !== null) {
           unset($_SESSION[$key]);
       }
    }
}

class Auth extends session {
    static string $loginError = '';
    public static function login($email,$password)
    {
        $email    = self::secure($email);
        $password = self::secure($password);

        if(filter_var($email,FILTER_VALIDATE_EMAIL)) 
        {
           $sql  = new sqlManager;
           $user = $sql->conn('select * from users where email=:email',['email'=>$email])[0];

           if(isset($user['id'])) 
           {
              if(isset($user['password']) && password_verify($password,$user['password']))
              {
                  (new self)->insert('user',$user);
              } else {
                self::$loginError = 'Wrong Credentials'; 
              }
           } else 
           {
            self::$loginError = 'Wrong Credentials';
           }

        } else 
        {
            self::$loginError = 'Wrong Credentials';
        }
        if(self::$loginError == 'Wrong Credentials') 
        { 
            session::insert('loginError','Wrong Credentials');
            header('location:/login');
        } else {
            session::delete('loginError');
            header('location:/home');
        };
    }

    public static  function secure($val)
    {
        return htmlspecialchars($val);
    }

    public static function register($email,$username,$password) 
    {
       $email    = self::secure($email);
       $password = self::secure($password);
       $username = self::secure($username);

       $password = password_hash($password,PASSWORD_BCRYPT);

       $sql  = new sqlManager;
       $user = $sql->conn('select * from users where email=:email',['email'=>$email]);

       $checkUniqnes = (empty($user)) ? true : false;

       if($checkUniqnes) {
           session::delete('registerUnique');
           $sql->conn("insert into users values ('','$username','$email','$password',NOW())");
           header('location:\home');
       } else {
           session::insert('registerUnique','this email has been used before');
           header('location:\register');
       }
    }
}


