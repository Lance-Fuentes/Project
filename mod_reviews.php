<?php

/*******w********
    
    Name: Lance Fuentes
    Date: April 21, 2023
    Description: Manage reviews in the database.

****************/

require('connect.php');
session_start();

$queryCat = 'SELECT * FROM categories';
$statementCat = $db->prepare($queryCat);
$statementCat->execute(); 

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'Master'){
    header('location: index.php');
    exit();
}

$query = 'SELECT * FROM reviews ORDER BY id';
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
            <li><a href="#">Moderate Reviews</a></li>
        </ul>
    </div>
    <?php endif ?>

    <?php if ($statement->rowCount() > 0) : ?>
        <table class="dbs-table">
            <tr>
                <th>Edit</th>
                <th>Delete</th>
                <th>Name</th>
                <th>Cloth ID</th>
                <th>User ID</th>
                <th>Date Created</th>
                <th>Date Updated</th>
                <th>Review</th>
            </tr>

            <?php while($row = $statement->fetch()) : ?>
                <?php $name= $row['name']; $clothId = $row['cloth_id']; $userId = $row['user_id']; $created = $row['date_created']; 
                    $updated = $row['date_updated']; $review = $row['review']; $ID = $row['id']?>
                
                <tr>
                    <td><a href="mod_reviews_process.php?command=edit&cloth_id=<?=$clothId?>&rev_id=<?=$ID?>">Edit Review</a></td>
                    <td><a href="mod_reviews_process.php?command=delete&cloth_id=<?=$clothId?>&rev_id=<?=$ID?>" onclick="return confirm('Are you sure you wish to delete this review?')">Delete Review</a></td>
                    <td><?= $name ?></td>
                    <td><?= $clothId ?></td>
                    <td><?= $userId ?></td>
                    <td><?= $created ?></td>
                    <td><?= $updated ?></td>
                    <td><?= $review ?></td>
                </tr>
            <?php endwhile ?>
        </table>
    <?php endif ?>
    <?php if(isset($_GET['deleted'])) : ?>
        <h2><?= $_GET['deleted'] ?></h2>
    <?php endif ?>

</body>
</html>