 <?php

/*******w******** 
    
    Name: Lance Fuentes
    Date: January 28, 2023
    Description: The process to create, update, or delete a post in the database.

****************/

define('MASTER_LOGIN','ghostface');

define('MASTER_PASSWORD','killa');

if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])

    || ($_SERVER['PHP_AUTH_USER'] != MASTER_LOGIN)

    || ($_SERVER['PHP_AUTH_PW'] != MASTER_PASSWORD)) {

header('HTTP/1.1 401 Unauthorized');

header('WWW-Authenticate: Basic realm="Our Blog"');

exit("Access Denied: Username and password invalid.");

}
?>