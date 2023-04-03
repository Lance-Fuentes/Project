<?php

/*******w********
    
    Name: Lance Fuentes
    Date: March 24, 2023
    Description: Manage users in the database.

****************/

require('connect.php');
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'Master'){
    header('location: index.php');
    exit();
}

$query = 'SELECT * FROM users ORDER BY user_id';
$statement = $db->prepare($query);
$statement->execute(); 


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

    <h2><a href="manage_process.php?command=create">Create A New User</a></h2>

    <?php if ($statement->rowCount() > 0) : ?>
        <table class="dbs-table">
            <tr>
                <th>Edit</th>
                <th>Delete</th>
                <th>User ID</th>
                <th>User Type</th>
                <th>Display Name</th>
                <th>Email</th>
            </tr>

            <?php while($row = $statement->fetch()) : ?>
                <?php $userId = $row['user_id']; $userType = $row['user_type']; $displayName = $row['display_name']; $email = $row['email']; ?>
                
                <tr>
                    <td><a href="manage_process.php?command=edit&userId=<?=$userId?>">Edit User</a></td>
                    <td><a href="manage_process.php?command=delete&userId=<?=$userId?>" onclick="return confirm('Are you sure you wish to delete this user?')">Delete User</a></td>
                    <td><?= $userId ?></td>
                    <td><?= $userType ?></td>
                    <td><?= $displayName ?></td>
                    <td><?= $email ?></td>
                </tr>
            <?php endwhile ?>
        </table>
    <?php endif ?>
    <?php if(isset($_GET['deleted'])) : ?>
        <h2><?= $_GET['deleted'] ?></h2>
    <?php endif ?>

</body>
</html>