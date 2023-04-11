 <?php

/*******w********
    
    Name: Lance Fuentes
    Date: March 20, 2023
    Description: Sign in to a user account.

****************/

require('connect.php');
session_start();

$queryCat = 'SELECT * FROM categories';
$statementCat = $db->prepare($queryCat);
$statementCat->execute(); 

$logged = false;

if(isset($_SESSION['user_id'])){
    header('location: index.php');
    exit();
}

if(isset($_POST['userCommand'])){
    $_SESSION['formlog_user'] = $_POST['username'];
    $_SESSION['formlog_pass'] = $_POST['password'];
}

$errors = [];

if (isset($_POST['userCommand']) && isset($_POST['username']) && isset($_POST['password']) && $_POST['userCommand'] == "Sign In" 
    && !empty($_POST['username'])  && !empty($_POST['password'])) {

    $userName = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));

    $query = 'SELECT * FROM users WHERE user_name = :userName';
    $statement = $db->prepare($query);
    $statement->bindValue(":userName", $userName);
    $statement->execute(); 

    if($statement->rowCount() > 0){
        $row = $statement->fetch();
        if(password_verify($password, $row['password'])){
			$_SESSION = $row;
            header('location: login_success.php');
        }
        else{
            $errors[] = 'Incorrect password';
        }
    }
    else{
        $errors[] = "Invalid username";
    }
}
else if(isset($_POST['userCommand'])){
    $errors[] = 'Enter your username and password';
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
		<input type="text" id="search-bar" placeholder="Search for products">
		<div id="user-links">
			<a href="user_login.php">Sign In</a>
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

    <div id="user-signin">
        <h1>Sign In</h1>

        <?php if(!empty($errors)) :?>
            <?php foreach($errors as $error) :?>
                <p class="error"><?= $error ?></p>
            <?php endforeach ?>
        <?php endif ?>

        <form action=<?=$_SERVER["PHP_SELF"]?> method="post">
            <input type="text" name="username" id="username" placeholder="Username" value=<?= (isset($_SESSION['formlog_user']) ? $_SESSION['formlog_user'] : '')?>>

            <input type="password" name="password" id="password" placeholder="Password" value=<?= (isset($_SESSION['formlog_pass']) ? $_SESSION['formlog_pass'] : '')?>>

            <input type="submit" name="userCommand" class="btn_log" value="Sign In">
        </form>
    </div>

    <div id="account-signup">
        <strong>New To Happy Pink</strong>
        <a href="user_create.php">Create An Account</a>
    </div>
</body>
</html>