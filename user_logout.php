 <?php

/*******w********
    
    Name: Lance Fuentes
    Date: March 20, 2023
    Description: Sign out of a user account.

****************/

session_start();

if(isset($_SESSION)){
    session_destroy();
    header('location: user_login.php');
    exit();
}

?>