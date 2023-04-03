<?php

/*******w********
    
    Name: Lance Fuentes
    Date: March 24, 2023
    Description: Edit products in the database.

****************/

require('connect.php');
session_start();

if(!isset($_SESSION['user_type']) && ($_SESSION['user_type'] != 'Master' || $_SESSION['user_type'] != 'Admin')){
    header('location: index.php');
    exit();
}

if(isset($_GET['product_id'])){
    $id = $_GET['product_id'];

    $prodQuery = "SELECT * FROM products WHERE cloth_id = $id";
    $prodStatement = $db->prepare($prodQuery);
    $prodStatement->execute(); 

    if(isset($_POST['submit']) && isset($_POST['cloth_type']) && isset($_POST['price']) && isset($_POST['category'])){
        $clothType = filter_input(INPUT_POST, 'cloth_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $color = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
        $query = "UPDATE products SET cloth_type = :clothType, description = :description, color = :color, price = :price, category = :category WHERE cloth_id = $id";
    
        $statement = $db->prepare($query);
        $statement->bindValue(":clothType", $clothType);
        $statement->bindValue(":description", $description);
        $statement->bindValue(":color", $color);
        $statement->bindValue(":price", $price);
        $statement->bindValue(":category", $category);
    
        $statement->execute(); 
        header("Refresh:0");
    }
}

if(isset($_GET['command']) && isset($_GET['product_id']) && $_GET['command'] == 'delete'){
    $category = $_GET['category'];
    $query = "DELETE FROM products WHERE cloth_id = {$_GET['product_id']}";
    $statement = $db->prepare($query);
    $statement->execute();

    header("Location: index.php?category=$category");
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
        <li><a class="nav" href="index.php?category=men">Men</a></li>
        <li><a class="nav" href="index.php?category=women">Women</a></li>
        <li><a class="nav" href="index.php?category=kids">Kids</a></li>
        <li><a class="nav" href="index.php?category=custom">Custom</a></li>
        <li><a class="nav" href="index.php?category=about">About</a></li>
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

    <?php $row = $prodStatement->fetch()?>
        <?php $productName = $row['product_name']; $cloth_type = $row['cloth_type']; $description= $row['description']; $color = $row['color'];
            $price = $row['price']; $category = $row['category']; ?>
        <?php if(isset($_GET['command']) && isset($_GET['product_id']) && $_GET['command'] == 'delete') : unlink("images/happy_pink/$category/$productName"); endif?>
        
        <div class="product">
            <h1><?= substr($productName, 0, strpos($productName, '.')) ?></h1>
            <img src="images/happy_pink/men/<?=$productName?>" alt="<?=$productName?>">
            <div id="details">
                <p>Price: $<?=$price?></p>
                <p>Color: <?=$color?></p>
            </div>
            <p>Details:</p>
            <p style="margin: 5px 0px 0px 40px;"><?=$description?>...</p>
        </div>

        <div id="add-product">
            <form action="product_edit.php?category=<?=$category?>&product_id=<?=$row['cloth_id']?>" method="post">
                <h3>Edit Product</h3>
                <label for="category">Category:</label>
                    <select name="category">
                        <option value="men">Men</option>
                        <option value="women">Women</option>
                        <option value="kids">kids</option>
                        <option value="custom">custom</option>
                    </select>
                <input type="text" name="cloth_type" placeholder="Clothing Type">
                <input type="text" name="color" placeholder="Product Color">
                <input type="number" min="0" step="0.01" title="Currency" pattern="^\d+(?:\.\d{1,2})?$" name="price" placeholder="Product Price">
                <textarea rows="5" cols="40" name="description" spellcheck="true" placeholder="Description"></textarea>
                <input type='submit' name='submit' value='Update Product'>
            </form>
        </div>
</body>
</html>