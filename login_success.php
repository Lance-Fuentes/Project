 <?php

/*******w********
    
    Name: Lance Fuentes
    Date: March 20, 2023
    Description: Successful login page.

****************/

session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Document</title>
</head>
<body>
    <header>
		<a style="text-decoration:none" href="index.php"><h1>Happy Pink</h1></a>
		<input type="text" id="search-bar" placeholder="Search for products">
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

    <ul>
        <li><a class="nav" href="index.php">Home</a></li>
        <li><a class="nav" href="#men">Men</a></li>
        <li><a class="nav" href="#women">Women</a></li>
        <li><a class="nav" href="#news">Kids</a></li>
        <li><a class="nav" href="#about">About</a></li>
    </ul>

    <div id="success_message">
        <strong>Log In Successful!</strong>
        <a href="index.php">Home Page</a>
    </div>

</body>
</html>