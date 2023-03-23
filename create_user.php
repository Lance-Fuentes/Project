<?php

/*******w******** 
    
    Name: Lance Fuentes
    Date: January 28, 2023
    Description: The process to create, update, or delete a post in the database.

****************/

require('connect.php');


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Create Account</h1>

    <form action="new_account.php" method="post">
        <label for="username">Username</label>
        <input name="username" id="username">

        <label for="password">Password</label>
        <input name="password" id="password">

        <label for="displayname">Display Name</label>
        <input name="displayname" id="displayname">

        <label for="usertype">User Type</label>
        <select name="usertype" id="usertype">
            <option value="Registrant"></option>
        </select>

        <input type="submit" name="userCommand" value="Create">
    </form>
    
</body>
</html>