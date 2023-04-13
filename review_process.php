<?php

/*******w********
    
    Name: Lance Fuentes
    Date: April 12, 2023
    Description: The process of create, modify and delete reviews

 ****************/

require('connect.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('location: index.php');
    exit();
}

$created = false;

$queryCat = 'SELECT * FROM categories';
$statementCat = $db->prepare($queryCat);
$statementCat->execute(); 

$queryRevUser = 'SELECT * FROM reviews';
$statementRevUser = $db->prepare($queryRevUser);
$statementRevUser->execute();

if(isset($_GET['cloth_id'])){
    $cloth_id = $_GET['cloth_id'];
}

date_default_timezone_set('Canada/Central');

if ($_GET['command'] == 'create') {
    if (isset($_POST['userCommand']) && isset($_POST['content']) && $_POST['userCommand'] == "Create Review" && !empty($_POST['content']) && strlen($_POST['content']) <= 600) {

    $content = trim(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS));
    $name = $_SESSION['display_name'];
    $createdDate = new DateTime('now');
    $updatedDate = new DateTime('now');
    $user_id = $_SESSION['user_id'];
    $review = $content;

    $query = "INSERT INTO reviews (name, date_created, date_updated, user_id, cloth_id, review) VALUES (:name, :date_created, :date_updated, :user_id, :cloth_id, :review)";
    $statement = $db->prepare(($query));
    
    $statement->bindValue(":name", $name);
    $statement->bindValue(":date_created", $createdDate->format('Y-m-d H:i:s'));
    $statement->bindValue(":date_updated", $updatedDate->format('Y-m-d H:i:s'));   
    $statement->bindValue(":user_id", $user_id);
    $statement->bindValue(":cloth_id", $cloth_id);
    $statement->bindValue(":review", $review);

    $statement->execute();

    $created = true;
    header("Location: product_page.php?cloth_id=$cloth_id");
    exit();
     
    }
    else if(isset($_POST['userCommand'])){
        $errors[] = 'Review can not be empty or more than 600 characters.';
    }
}

if (isset($_GET['command']) && $_GET['command'] == 'edit' && isset($_GET['rev_id'])){
    $rev_id = $_GET['rev_id'];
    $updatedDate = new DateTime('now');

    $query = "SELECT * FROM reviews WHERE id = $rev_id";
    $revStatement = $db->prepare($query);
    $revStatement->execute();

    if(isset($_POST['userCommand']) && $_POST['userCommand'] == 'Update Review' && 
        isset($_POST['content']) && !empty($_POST['content']) && strlen($_POST['content']) <= 600) {
            $content = trim(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS));
            $review = $content;

            $query = "UPDATE reviews SET review = :review, date_updated = :dateUpdated WHERE id = $rev_id";
            $statement = $db->prepare($query);
            $statement->bindValue(":review", $review);
            $statement->bindValue(":dateUpdated", $updatedDate->format('Y-m-d H:i:s'));  
            $statement->execute();
            
            header("Location: product_page.php?cloth_id=$cloth_id");
            exit();
    }
}

if(isset($_POST['userCommand']) && $_POST['userCommand'] == 'delete' && isset($_GET['rev_id'])){
    $rev_id = $_GET['rev_id'];
    $query = "DELETE FROM reviews WHERE id = $rev_id";
    $statement = $db->prepare($query);
    $statement->execute();

    header("Location: product_page.php?cloth_id=$cloth_id");
    exit();
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
        <?php while($row = $statementCat->fetch()) : ?>
        <?php $name = $row['name']; $display_name = $row['display_name'];?>
            <li><a class="nav" href="index.php?category=<?=$name?>"><?=$display_name?></a></li>
        <?php endwhile ?>
        <li><a class="nav" href="index.php?category=about">About</a></li>
    </ul>

    <?php if (isset($_SESSION['user_type']) && ($_SESSION['user_type'] == 'Master' || $_SESSION['user_type'] == 'Admin')) : ?>
        <div id="authorized_menu">
            <ul>
                <?php if ($_SESSION['user_type'] == 'Master') : ?>
                    <li><a href="manage_user.php">Users</a></li>
                <?php endif ?>
                <li><a href="admin_nav.php">Edit Navigation</a></li>
                <li><a href="#">Upload Images</a></li>
                <li><a href="#">Moderate Reviews</a></li>
            </ul>
        </div>
    <?php endif ?>
    

    <?php if ($_GET['command'] == 'create') : ?>
        <?php if (!$created) : ?>
            <div id="user-signup">
                <h1>Create Review</h1>

                <?php if (!empty($errors)) : ?>
                    <?php foreach ($errors as $error) : ?>
                        <p class="error"><?= $error ?></p>
                    <?php endforeach ?>
                <?php endif ?>

                <form action=<?= "review_process.php?command=create&cloth_id=$cloth_id" ?> method="post">
                    <textarea placeholder="Write a review..." name="content"></textarea>
                    <input type="submit" name="userCommand" class="btn_log" value="Create Review">
                </form>
            </div>
        <?php endif ?>
    <?php endif ?>

    <?php if ($_GET['command'] == 'edit') : ?>
        <div class="edit">
            <h2>Update Review</h2>

            <?php if (!empty($errors)) : ?>
                <?php foreach ($errors as $error) : ?>
                    <p class="error"><?= $error ?></p>
                <?php endforeach ?>
                <?php endif ?>

                <?php $revRow = $revStatement->fetch(); $content = $revRow['review']; $rev_id = $revRow['id'];?>
            <form action=<?= "review_process.php?command=edit&rev_id=$rev_id&cloth_id=$cloth_id" ?> method="post">
                <textarea placeholder="Write a review..." name="content"><?=$content?></textarea>
                <input type="submit" name="userCommand" class="btn_log" value="Update Review">
                <input type='submit' name='userCommand' value='delete' class="btn_log"  onclick="return confirm('Are you sure you wish to delete this review?')">
            </form>
        </div>
    <?php endif ?>
</body>

</html>