<?php

class sqlManager {

    private function connection() 
    {
        $host     = 'localhost';
        $dbname   = 'ecommercial';
        $username = 'abdalrhman';
        $password = 'AG616433';


        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8",$username,$password);
        } catch (PDOException $e) {
            die(' failed: ' . $e->getMessage());
        }

        return $pdo;
    }

    public function conn($stmt = 'select * from users',$arr=[]) 
    {
        $sql = $this->connection();

        $stmt  = $sql->prepare($stmt);
        $stmt->execute($arr);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        return $result;
    }

}