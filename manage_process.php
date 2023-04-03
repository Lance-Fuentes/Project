<?php

/*******w********
    
    Name: Lance Fuentes
    Date: March 24, 2023
    Description: Create users 

 ****************/

require('connect.php');
session_start();

if(isset($_POST['userCommand']) && $_POST['userCommand'] == 'Create Account'){
    $_SESSION['userType'] = $_POST['userType'];
    $_SESSION['form_user'] = $_POST['username'];
    $_SESSION['form_display'] = $_POST['displayname'];
    $_SESSION['form_pass'] = $_POST['password'];
    $_SESSION['form_confirm'] = $_POST['confirm-password'];
    $_SESSION['form_email'] = $_POST['email'];
}

$created = false;

if(isset($_GET['another']) && $_GET['another'] == true){
    $created = false;
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'Master') {
    header('location: index.php');
    exit();
}

if ($_GET['command'] == 'create') {
    if (isset($_POST['userCommand']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['displayname']) && isset($_POST['password']) && isset($_POST['confirm-password']) 
    && $_POST['userCommand'] == "Create Account" && !empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['displayname']) && !empty($_POST['password']) 
    && !empty($_POST['confirm-password']) && strlen($_POST['username']) <= 20 && strlen($_POST['displayname']) <= 20 && isset($_POST['userType']) && !empty($_POST['userType'])
    && strlen($_POST['password']) >= 4 && strlen($_POST['password']) >= 4 && strlen($_POST['email']) <= 80) {

    $userName = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
    $displayName = trim(filter_input(INPUT_POST, 'displayname', FILTER_SANITIZE_SPECIAL_CHARS));
    $confirmedPass = trim(filter_input(INPUT_POST, 'confirm-password', FILTER_SANITIZE_SPECIAL_CHARS));
    $userType = trim(filter_input(INPUT_POST, 'userType', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

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

if(isset($_POST['userCommand']) && $_POST['userCommand'] == 'Create Account'){
    if(!isset($_POST['username']) || empty($_POST['username']) || strlen($_POST['username']) > 20){
        $errors[] = 'Username can not be empty and has to be 20 characters or less.';
    }

    if(!isset($_POST['displayname']) || empty($_POST['displayname']) || strlen($_POST['displayname']) > 20){
        $errors[] = 'Display Name can not be empty and has to be 20 characters or less.';
    }

    if(!isset($_POST['email']) || empty($_POST['email']) || strlen($_POST['email']) > 80){
        $errors[] = 'Email can not be empty and has to be 80 characters or less.';
    }

    if(!isset($_POST['password']) || empty($_POST['password']) || strlen($_POST['password']) <= 4){
        $errors[] = 'Password can not be empty and has to have 4 characters or more.';
    }

    if(!isset($_POST['confirm-password']) || empty($_POST['confirm-password']) || strlen($_POST['confirm-password']) <= 4){
        $errors[] = 'Confirm Password can not be empty and has to match your Password.';
    }
}
}

if (isset($_GET['command']) && $_GET['command'] == 'edit' && isset($_GET['userId']) && filter_input(INPUT_GET, 'userId', FILTER_VALIDATE_INT)) {
    $userId = filter_input(INPUT_GET, 'userId', FILTER_SANITIZE_NUMBER_INT);

    $query = 'SELECT * FROM users WHERE user_id = :user_id';
    $userStatement = $db->prepare($query);
    $userStatement->bindValue(":user_id", $userId);
    $userStatement->execute();

    if(isset($_POST['userCommand']) && $_POST['userCommand'] == 'Update User'){
        if(isset($_POST['email']) && isset($_POST['displayname']) && isset($_POST['userType']) &&
            !empty($_POST['userType']) && !empty($_POST['displayname']) && !empty($_POST['email'])
            && strlen($_POST['displayname']) <= 20 && strlen($_POST['email']) <= 80) {

            $displayName = trim(filter_input(INPUT_POST, 'displayname', FILTER_SANITIZE_SPECIAL_CHARS));
            $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
            $userType = trim(filter_input(INPUT_POST, 'userType', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $query = "UPDATE users SET user_type = :userType, display_name = :displayName, email = :email WHERE user_id = $userId";

                $statement = $db->prepare($query);
                $statement->bindValue(":userType", $userType);
                $statement->bindValue(":displayName", $displayName);
                $statement->bindValue(":email", $email);

                $statement->execute(); 
            }
            else{
                $errors[] = 'Invalid email.';
            }
        }

        if(!isset($_POST['displayname']) || empty($_POST['displayname']) || strlen($_POST['displayname']) > 20){
            $errors[] = 'Display Name can not be empty and has to be 20 characters or less.';
        }

        if(!isset($_POST['email']) || empty($_POST['email']) || strlen($_POST['email']) > 80){
            $errors[] = 'Email can not be empty and has to be 80 characters or less.';
        }

        if(!isset($_POST['userType']) || empty($_POST['userType'])){
            $errors[] = 'Invalid user type';
        }
    }
} 

if (isset($_GET['command']) && $_GET['command'] == 'delete' && isset($_GET['userId']) && filter_input(INPUT_GET, 'userId', FILTER_VALIDATE_INT)){
    $userId = filter_input(INPUT_GET, 'userId', FILTER_SANITIZE_NUMBER_INT);

    $query = "DELETE FROM users WHERE user_id = $userId";
    $statement = $db->prepare($query);
    $statement->execute();

    $success = "Successfully deleted a user with an ID of $userId.";
    header("Location: manage_user.php?deleted=$success");
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
        <a style="text-decoration:none" href="index.php">
            <h1>Happy Pink</h1>
        </a>
        <input type="text" id="search-bar" placeholder="Search for products">
        <div id="user-links">

            <?php if (isset($_SESSION['user_id'])) : ?>
                <a href="#"><?= $_SESSION['display_name'] ?></a>
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
        <li><a class="nav" href="#men">Men</a></li>
        <li><a class="nav" href="#women">Women</a></li>
        <li><a class="nav" href="#news">Kids</a></li>
        <li><a class="nav" href="#about">About</a></li>
    </ul>

    <?php if (isset($_SESSION['user_type']) && ($_SESSION['user_type'] == 'Master' || $_SESSION['user_type'] == 'Admin')) : ?>
        <div id="authorized_menu">
            <ul>
                <?php if ($_SESSION['user_type'] == 'Master') : ?>
                    <li><a href="manage_user.php">Users</a></li>
                <?php endif ?>
                <li><a href="#">Edit Navigation</a></li>
                <li><a href="#">Upload Images</a></li>
                <li><a href="#">Moderate Reviews</a></li>
            </ul>
        </div>
    <?php endif ?>

    <?php if ($_GET['command'] == 'create') : ?>
        <?php if (!$created) : ?>
            <div id="user-signup">
                <h1>Create Account</h1>

                <?php if (!empty($errors)) : ?>
                    <?php foreach ($errors as $error) : ?>
                        <p class="error"><?= $error ?></p>
                    <?php endforeach ?>
                <?php endif ?>

                <form action=<?= 'manage_process.php?command=create' ?> method="post">

                    <label for="userType">User Type:</label>
                    <select name="userType" id="userType">
                    <option value="Master">Master</option>
                    <option value="Admin">Admin</option>
                    <option value="Registrant">Registrant</option>
                    </select>

                    <input type="text" name="username" id="username" placeholder="Username" value=<?= (isset($_SESSION['form_user']) ? $_SESSION['form_user'] : '') ?>>

                    <input type="text" name="email" id="email" placeholder="Email" value=<?= (isset($_SESSION['form_email']) ? $_SESSION['form_email'] : '') ?>>

                    <input type="text" name="displayname" id="displayname" placeholder="Display Name" value=<?= (isset($_SESSION['form_display']) ? $_SESSION['form_display'] : '') ?>>

                    <input type="password" name="password" id="password" placeholder="Password" value=<?= (isset($_SESSION['form_pass']) ? $_SESSION['form_pass'] : '') ?>>

                    <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm Password" value=<?= (isset($_SESSION['form_confirm']) ? $_SESSION['form_confirm'] : '') ?>>

                    <input type="submit" name="userCommand" class="btn_log" value="Create Account">
                </form>
            </div>
        <?php else : ?>
            <div id="account-signin">
                <strong>Account Created!</strong>
                <a href="manage_process.php?command=create&another=true">Create another Account?</a>
            </div>
        <?php endif ?>
    <?php endif ?>

    <?php if ($_GET['command'] == 'edit') : ?>
                <?php $row = $userStatement->fetch() ?>
                <?php $userId = $row['user_id']; $userType = $row['user_type']; $displayName = $row['display_name']; $email = $row['email']; ?>

                <div class="edit">
                    <h2>User Information</h2>
                    <table>
                        <tr>
                            <th>User ID</th>
                            <th>User Type</th>
                            <th>Display Name</th>
                            <th>Email</th>
                        </tr>

                        <tr>
                            <td><?= $userId ?></td>
                            <td><?= $userType ?></td>
                            <td><?= $displayName ?></td>
                            <td><?= $email ?></td>
                        </tr>
                    </table>
                </div>

                <div class="edit">
                    <h2>Update Information</h2>

                    <?php if (!empty($errors)) : ?>
                        <?php foreach ($errors as $error) : ?>
                            <p class="error"><?= $error ?></p>
                        <?php endforeach ?>
                     <?php endif ?>

                    <form action="manage_process.php?command=edit&userId=<?=$userId?>" method="post">
                        <label for="userType">User Type:</label>
                        <select name="userType" id="userType">
                        <option value="Master" <?php if($userType == "Master"){echo 'selected';} ?>>Master</option>
                        <option value="Admin" <?php if($userType == "Admin"){echo 'selected';} ?>>Admin</option>
                        <option value="Registrant" <?php if($userType == "Registrant"){echo 'selected';} ?>>Registrant</option>
                        </select>

                        <label for="displayname">Display Name:</label>
                        <input type="text" name="displayname" id="displayname" placeholder="Display Name" value=<?= $displayName ?>>

                        <label for="email">Email:</label>
                        <input type="text" name="email" id="email" placeholder="Email" value=<?= $email ?>>

                        <input type="submit" name="userCommand" class="btn_log" value="Update User">
                    </form>

                    <a href="manage_user.php">  
                        <input type="submit" class="btn_log" value="Done"/>  
                    </a>
                </div>
    <?php endif ?>

    <?php if ($_GET['command'] == 'delete') : ?>

    <?php endif ?>
</body>

</html>