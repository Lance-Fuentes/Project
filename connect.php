<?php

/*******w******** 
    
    Name: Lance Fuentes
    Date: January 28, 2023
    Description: The process to create, update, or delete a post in the database.

****************/

     define('DB_DSN','mysql:host=localhost;dbname=serverside;charset=utf8');
     define('DB_USER','serveruser');
     define('DB_PASS','gorgonzola7!');     
     
     try {
         $db = new PDO(DB_DSN, DB_USER, DB_PASS);
     } catch (PDOException $e) {
         print "Error: " . $e->getMessage();
         die();
     }
 ?>