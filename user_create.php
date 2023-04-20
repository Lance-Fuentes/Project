<?php

/*******w********
 
    Name: Lance Fuentes
    Date: March 20, 2023
    Description: Creation of user account.

****************/

require('connect.php');
session_start();

if(isset($_SESSION['user_id'])){
    header('location: index.php');
    exit();
}

$queryCat = 'SELECT * FROM categories';
$statementCat = $db->prepare($queryCat);
$statementCat->execute(); 

if(isset($_POST['userCommand'])){
    $_SESSION['form_user'] = $_POST['username'];
    $_SESSION['form_display'] = $_POST['displayname'];
    $_SESSION['form_pass'] = $_POST['password'];
    $_SESSION['form_confirm'] = $_POST['confirm-password'];
    $_SESSION['form_email'] = $_POST['email'];
}

$created = false;
$errors = [];

if (isset($_POST['userCommand']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['displayname']) && isset($_POST['password']) && isset($_POST['confirm-password']) 
    && $_POST['userCommand'] == "Sign Up" && !empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['displayname']) && !empty($_POST['password']) 
    && !empty($_POST['confirm-password']) && strlen($_POST['username']) <= 20 && strlen($_POST['displayname']) <= 20 
    && strlen($_POST['password']) >= 4 && strlen($_POST['password']) >= 4 && strlen($_POST['email']) <= 80) {

    $userName = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
    $displayName = trim(filter_input(INPUT_POST, 'displayname', FILTER_SANITIZE_SPECIAL_CHARS));
    $confirmedPass = trim(filter_input(INPUT_POST, 'confirm-password', FILTER_SANITIZE_SPECIAL_CHARS));
    $userType = 'Registrant';

    $options = array("cost"=>4);
    $hashedPass = password_hash($password, PASSWORD_BCRYPT, $options);

    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        if($password == $confirmedPass){

        $checkQuery = 'SELECT * FROM users WHERE user_name = :userName';
        $checkStatement = $db->prepare($checkQuery);
        $checkStatement->bindValue(":userName", $userName);
        $checkStatement->execute(); 

            if($checkStatement->rowCount() == 0){
                $query = "INSERT INTO users (user_type, display_name, user_name, email, password) VALUES (:user_type, :displayName, :userName, :email, :password)";
                $statement = $db->prepare(($query));
                
                $statement->bindValue(":user_type", $userType);
                $statement->bindValue(":displayName", $displayName);
                $statement->bindValue(":userName", $userName);
                $statement->bindValue(":email", $email);
                $statement->bindValue(":password", $hashedPass);

                $statement->execute();

                $created = true;
            }
            else{
                $errors[] = 'Username taken.';
            }
        }
        else{
            $errors[] = 'Passwords didn\'t match, try again.';
        }  
    }
    else{
        $errors[] = 'Invalid email.';
    }

     
}

if(isset($_POST['userCommand'])){
    if(!isset($_POST['username']) || empty($_POST['username']) || strlen($_POST['username']) > 20){
        $errors[] = 'Username can not be empty and has to be 20 characters or less.';
    }

    if(!isset($_POST['displayname']) || empty($_POST['displayname']) || strlen($_POST['displayname']) > 20){
        $errors[] = 'Display Name can not be empty and has to be 20 characters or less.';
    }

    if(!isset($_POST['password']) || empty($_POST['password']) || strlen($_POST['password']) <= 4){
        $errors[] = 'Password can not be empty and has to have 4 characters or more.';
    }

    if(!isset($_POST['confirm-password']) || empty($_POST['confirm-password']) || strlen($_POST['confirm-password']) <= 4){
        $errors[] = 'Confirm Password can not be empty and has to match your Password.';
    }

    if(!isset($_POST['email']) || empty($_POST['email']) || strlen($_POST['email']) > 80){
        $errors[] = 'Email can not be empty and has to be 80 characters or less.';
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Happy Pink</title>
</head>
<body>
<header>
		<a style="text-decoration:none" href="index.php"><h1>Happy Pink</h1></a>
        <form id="search-form" action="index.php?category=search" method="post">
            <input type="text" id="search-bar" name="search-item" placeholder="Search for products" value=<?= (isset($_SESSION['searchItem']) ? $_SESSION['searchItem'] : '') ?>>
            <input type="submit" name="userCommand" class="btn_log" value="Search">
            <label for="filter">Filter Search:</label>
            <select name="filter" id="filter">
            <option value="none">None</option>
            <?php $querySearchCat = 'SELECT * FROM categories';
                    $statementSearchCat = $db->prepare($querySearchCat);
                    $statementSearchCat->execute(); 
                    while($row = $statementSearchCat->fetch()):
                    $name = $row['name']; $display_name = $row['display_name'];?>
                    <option value=<?=$name?> <?= (isset($_SESSION['option']) && $_SESSION['option'] == $name? 'selected' : '') ?>><?=$display_name?></option>
                    <?php endwhile ?>
            </select>
        </form>
		
		<div id="user-links">

            <?php if(isset($_SESSION['user_id'])) :?>
                    <a href="#"><?=$_SESSION['display_name']?></a>
                    <a href="user_logout.php">Sign Out</a>
            <?php else : ?>
                <a href="user_login.php">Sign In</a>
			<?php endif ?>

			<a href="#">Purchases</a>
			<a href="#">Cart</a>
		</div>
	</header>

    <ul class="main-nav">
        <li><a class="nav" href="index.php">Home</a></li>
        <?php while($row = $statementCat->fetch()) : ?>
        <?php $name = $row['name']; $display_name = $row['display_name'];?>
            <li><a class="nav" href="index.php?category=<?=$name?>"><?=$display_name?></a></li>
        <?php endwhile ?>
        <li><a class="nav" href="index.php?category=about">About</a></li>
    </ul>

    <?php if(!$created) : ?>
        <div id="user-signup">      
            <h1>Sign Up</h1>

            <?php if(!empty($errors)) :?>
                <?php foreach($errors as $error) :?>
                    <p class="error"><?= $error ?></p>
                <?php endforeach ?>
            <?php endif ?>

            <form action=<?=$_SERVER["PHP_SELF"]?> method="post">
                <input type="text" name="username" id="username" placeholder="Username" value=<?= (isset($_SESSION['form_user']) ? $_SESSION['form_user'] : '')?>>

                <input type="text" name="email" id="email" placeholder="Email" value=<?= (isset($_SESSION['form_email']) ? $_SESSION['form_email'] : '')?>>

                <input type="text" name="displayname" id="displayname" placeholder="Display Name" value=<?= (isset($_SESSION['form_display']) ? $_SESSION['form_display'] : '')?>>

                <input type="password" name="password" id="password" placeholder="Password" value=<?= (isset($_SESSION['form_pass']) ? $_SESSION['form_pass'] : '')?>>

                <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm Password" value=<?= (isset($_SESSION['form_confirm']) ? $_SESSION['form_confirm'] : '')?>>

                <input type="submit" name="userCommand" class="btn_log" value="Sign Up">
            </form>
        </div>
    <?php else :?>
        <div id="created">
            <strong>Account Created!</strong>
            <a href="user_login.php">Sign in to your Account</a>
        </div>
    <?php endif; session_destroy(); ?>
    
</body>
</html>