<?php

/*******w******** 
    
    Name: Lance Fuentes
    Date: March 20, 2023
    Description: The main page of Happy Pink website.

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
    <title>Happy Pink</title>
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

    <ul class="main-nav">
        <li><a class="nav" href="index.php">Home</a></li>
        <li><a class="nav" href="#men">Men</a></li>
        <li><a class="nav" href="#women">Women</a></li>
        <li><a class="nav" href="#news">Kids</a></li>
        <li><a class="nav" href="#about">About</a></li>
    </ul>

    <?php if(isset($_SESSION['user_type']) && ($_SESSION['user_type'] == 'Master' || $_SESSION['user_type'] == 'Admin')) :?>
    <div id="authorized_menu">
        <ul>
            <?php if($_SESSION['user_type'] == 'Master') : ?>
                <li><a href="manage_user.php">Users</a></li> 
            <?php endif?>
            <li><a href="#">Edit Navigation</a></li>
            <li><a href="#">Upload Images</a></li>
            <li><a href="#">Moderate Reviews</a></li>
        </ul>
    </div>
    <?php endif ?>

    <?php if(!isset($_SESSION['user_id'])) :?>
    <div id="account-signin">
        <strong>Shop and customize the products you love</strong>
        <a href="user_login.php">Sign In or Create An Account</a>
    </div>
    <?php endif ?>

    <div id="home-content">
        <img src="images/happy_pink/model1.png" alt="Brown jacket model">
        <img src="images/happy_pink/model2.png" alt="White shirt model">

    </div>
</body>
</html>