<?php

/*******w******** 
    
    Name: Lance Fuentes
    Date: January 28, 2023
    Description: The process to create, update, or delete a post in the database.

****************/

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
$displayname = filter_input(INPUT_POST, 'displayname', FILTER_SANITIZE_SPECIAL_CHARS);
$usertype = filter_input(INPUT_POST, 'usertype', FILTER_SANITIZE_SPECIAL_CHARS);

?>