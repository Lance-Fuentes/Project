<?php

/*******w********
    
    Name: Lance Fuentes
    Date: March 24, 2023
    Description: Create users 

 ****************/

require('connect.php');
session_start();

$queryCat = 'SELECT * FROM categories';
$statementCat = $db->prepare($queryCat);
$statementCat->execute(); 

$created = false;

if(isset($_GET['another']) && $_GET['another'] == true){
    $created = false;
}

if(!isset($_SESSION['user_type']) && ($_SESSION['user_type'] != 'Master' || $_SESSION['user_type'] != 'Admin')){
    header('location: index.php');
    exit();
}

if ($_GET['command'] == 'create') {
    if (isset($_POST['userCommand']) && isset($_POST['name']) && isset($_POST['displayname']) 
    && $_POST['userCommand'] == "Create Category" && !empty($_POST['name']) && !empty($_POST['displayname'])
    && strlen($_POST['name']) <= 30 && strlen($_POST['displayname']) <= 30){

    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
    $displayName = trim(filter_input(INPUT_POST, 'displayname', FILTER_SANITIZE_SPECIAL_CHARS));

    $checkQuery = 'SELECT * FROM categories WHERE name = :name';
    $checkStatement = $db->prepare($checkQuery);
    $checkStatement->bindValue(":name", $name);
    $checkStatement->execute(); 

        if($checkStatement->rowCount() == 0){
            $query = "INSERT INTO categories (name, display_name) VALUES (:name, :displayName)";
            $statement = $db->prepare(($query));
            
            $statement->bindValue(":name", $name);
            $statement->bindValue(":displayName", $displayName);

            $statement->execute();

            $created = true;
        }
        else{
            $errors[] = 'Name value taken.';
        }

    }

    if(isset($_POST['userCommand']) && $_POST['userCommand'] == 'Create Account'){
        if(!isset($_POST['username']) || empty($_POST['username']) || strlen($_POST['username']) > 30){
            $errors[] = 'Category Name can not be empty and has to be 30 characters or less.';
        }

        if(!isset($_POST['displayname']) || empty($_POST['displayname']) || strlen($_POST['displayname']) > 30){
            $errors[] = 'Display Name can not be empty and has to be 30 characters or less.';
        }
    }
}

if (isset($_GET['command']) && $_GET['command'] == 'edit' && isset($_GET['id']) && filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = 'SELECT * FROM categories WHERE id = :id';
    $navStatement = $db->prepare($query);
    $navStatement->bindValue(":id", $id);
    $navStatement->execute();

    if(isset($_POST['userCommand']) && $_POST['userCommand'] == 'Update Category'){
        if(isset($_POST['displayname']) && isset($_POST['name']) && !empty($_POST['displayname']) && !empty($_POST['name'])
            && strlen($_POST['displayname']) <= 30 && strlen($_POST['name']) <= 30) {

            $displayName = trim(filter_input(INPUT_POST, 'displayname', FILTER_SANITIZE_SPECIAL_CHARS));
            $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

            $query = "UPDATE categories SET name = :name, display_name = :displayName WHERE id = $id";

            $statement = $db->prepare($query);
            $statement->bindValue(":name", $name);
            $statement->bindValue(":displayName", $displayName);

            $statement->execute(); 
            header("Refresh:0");
        }

        if(!isset($_POST['displayname']) || empty($_POST['displayname']) || strlen($_POST['displayname']) > 30){
            $errors[] = 'Display Name can not be empty and has to be 20 characters or less.';
        }

        if(!isset($_POST['name']) || empty($_POST['name']) || strlen($_POST['name']) > 30){
            $errors[] = 'Name can not be empty and has to be 30 characters or less.';
        }
    }
} 

if (isset($_GET['command']) && $_GET['command'] == 'delete' && isset($_GET['id']) && filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)){
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "DELETE FROM categories WHERE id = $id";
    $statement = $db->prepare($query);
    $statement->execute();

    $success = "Successfully deleted a category with an ID of $id.";
    header("Location: admin_nav.php?deleted=$success");
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
                <h1>Create Category Navigation</h1>

                <?php if (!empty($errors)) : ?>
                    <?php foreach ($errors as $error) : ?>
                        <p class="error"><?= $error ?></p>
                    <?php endforeach ?>
                <?php endif ?>

                <form action=<?= 'admin_nav_process.php?command=create' ?> method="post">

                    <input type="text" name="displayname" id="displayname" placeholder="Display Name">

                    <input type="text" name="name" id="name" placeholder="Category Name">

                    <input type="submit" name="userCommand" class="btn_log" value="Create Category">
                </form>
            </div>
        <?php else : ?>
            <div id="created">
                <strong>Category Created!</strong>
                <a href="admin_nav.php">Refresh to see category</a>
                <a href="admin_nav_process.php?command=create&another=true">Create another category?</a>
            </div>
        <?php endif ?>
    <?php endif ?>

    <?php if ($_GET['command'] == 'edit') : ?>
                <?php $row = $navStatement->fetch() ?>
                <?php $id = $row['id']; $name=$row['name']; $displayName = $row['display_name']; ?>

                <div class="edit">
                    <h2>Category Information</h2>
                    <table>
                        <tr>
                            <th>Category ID</th>
                            <th>Name</th>
                            <th>Display Name</th>
                        </tr>

                        <tr>
                            <td><?= $id ?></td>
                            <td><?=$name?></td>
                            <td><?= $displayName ?></td>
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

                    <form action="admin_nav_process.php?command=edit&id=<?=$id?>" method="post">

                        <label for="displayname">Display Name:</label>
                        <input type="text" name="displayname" id="displayname" placeholder="Display Name" value=<?= $displayName ?>>

                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" placeholder="Category Name" value=<?=$name?>>

                        <input type="submit" name="userCommand" class="btn_log" value="Update Category">
                    </form>

                    <a href="admin_nav.php">  
                        <input type="submit" class="btn_log" value="Done"/>  
                    </a>
                </div>
    <?php endif ?>
</body>

</html>