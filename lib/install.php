<?php

    require "data/test.php";

    function installBlog(PDO $pdo){

        $root = realpath(__DIR__);
        $database = $root . '/data/data.sqlite';


        $error = '';

        // a security measure in case you try resetting database if it already exists
        if(is_readable($database) && filesize($database) > 0)
        {
            $error = "please delete previous database manually";
        }

        // create an empty file as database
        if(!$error)
        {
            $createdOk = @touch($database);
            if(!$createdOk)
            {
                $error = sprintf('could not create database, pls allow server to create new files in \'%s\'', dirname($database));
            }
        }

        //  now running the sql commands
        if(!$error)
        {
            $sql = file_get_contents($root . '/data/init.sql');
            if($sql === false)
            {
                $error = 'cannot find sql file';
            }
        }

        // connect to new sql database to run the sql commands
        if(!$error)
        {
            // $pdo = new PDO($dsn);
            $result = $pdo->exec($sql);
            if($result === false)
            {
                $error = 'could not run sql: '. print_r($pdo->errorInfo(), true);
            }
        }

        // to see how many rows we created
        $count = array();
        foreach(array('post', 'comment') as $tableName)
        {
            if(!$error){
                $sql = "SELECT COUNT(*) AS c FROM $tableName";
                $stmt = $pdo->query($sql);
                if($stmt)
                {
                    $count['tableName'] = $stmt->fetchColumn();
                }
            }
        }
        return array($count, $error);
    }

    function createUser(PDO $pdo, $username, $length =10){
        $alphabet = range(ord('A'), ord('z'));
        $alphabetLength = count($alphabet);
        
        $password = '';
        for($i = 0; $i < $length; $i++){
            $passChar .= $alphabet[rand(0, $alphabetLength -1)];
            $password .= chr($passChar);
        }

        $error = '';

        $sql = 'INSERT INTO user (username, password, created_at) VALUES (:username, :password, :created_at)';
        $stmt = $pdo->prepare($sql);

        if($stmt === false){
            $error = "could not prepare user creation";
        }

        if(!$error){
            // create hash of password
            $hash = password_hash($password, PASSWORD_DEFAULT);
            if($hash === false){
                $error = "password hashing failed";
            }
        }

        // inserting user details
        if(!$error){
            $result = $stmt->execute(array('username'=> $username, 'passowrd' => $hash, 'created_at' => getSqlDateForNow()));
            if($result === false){
                $error = "could not run user creation";
            }
        }

        if($error){
            $password = '';
        }

        return array($password, $error);
    }

?>