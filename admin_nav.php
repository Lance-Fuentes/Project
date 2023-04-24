<?php

/*******w********
    
    Name: Lance Fuentes
    Date: April 5, 2023
    Description: Manage category navigation in the database.

****************/

require('connect.php');
session_start();

if(!isset($_SESSION['user_type']) && ($_SESSION['user_type'] != 'Master' || $_SESSION['user_type'] != 'Admin')){
    header('location: index.php');
    exit();
}

$queryCat = 'SELECT * FROM categories';
$statementCat = $db->prepare($queryCat);
$statementCat->execute(); 




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

    <?php if(isset($_SESSION['user_type']) && ($_SESSION['user_type'] == 'Master' || $_SESSION['user_type'] == 'Admin')) :?>
    <div id="authorized_menu">
        <ul>
            <?php if($_SESSION['user_type'] == 'Master') : ?>
                <li><a href="manage_user.php">Users</a></li> 
            <?php endif?>
            <li><a href="admin_nav.php">Edit Navigation</a></li>
            <li><a href="#">Upload Images</a></li>
            <li><a href="mod_reviews.php">Moderate Reviews</a></li>
        </ul>
    </div>
    <?php endif ?>

    <h2><a href="admin_nav_process.php?command=create">Create A New Category</a></h2>

    <?php $statementCat->execute(); if ($statementCat->rowCount() > 0) : ?>
        <table class="dbs-table">
            <tr>
                <th>Edit</th>
                <th>Delete</th>
                <th>Category ID</th>
                <th>Name</th>
                <th>Display Name</th>
            </tr>

            <?php while($row = $statementCat->fetch()) : ?>
            <?php $id = $row['id']; $displayName = $row['display_name']; $name = $row['name']; ?>
                
                <tr>
                    <td><a href="admin_nav_process.php?command=edit&id=<?=$id?>">Edit Category</a></td>
                    <td><a href="admin_nav_process.php?command=delete&id=<?=$id?>" onclick="return confirm('Are you sure you wish to delete this category?')">Delete Category</a></td>
                    <td><?= $id ?></td>
                    <td><?= $name ?></td>
                    <td><?= $displayName ?></td>
                </tr>
            <?php endwhile ?>
        </table>
    <?php endif ?>
    <?php if(isset($_GET['deleted'])) : ?>
        <h2><?= $_GET['deleted'] ?></h2>
    <?php endif ?>

</body>
</html>