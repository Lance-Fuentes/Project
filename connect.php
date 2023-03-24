<?php

/*******w******** 
    
    Name: Lance Fuentes
    Date: March 20, 2023
    Description: The authentication for serverside database.

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