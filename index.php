<?php

/*******w******** 
    
    Name: Lance Fuentes
    Date: March 20, 2023
    Description: The main page of Happy Pink website.

****************/

require('connect.php');
session_start();
use \Gumlet\ImageResize;
use \Gumlet\ImageResizeException;
include '\xampp\htdocs\wd2\Project\ImageResize.php';
include '\xampp\htdocs\wd2\Project\ImageResizeException.php';

$queryCat = 'SELECT * FROM categories';
$statementCat = $db->prepare($queryCat);
$statementCat->execute(); 

if(isset($_GET['category']) && $_GET['category'] == 'men'){
    $query = 'SELECT * FROM products WHERE category = "men" ORDER BY cloth_id';
    $statement = $db->prepare($query);
    $statement->execute(); 
}

function file_upload_path($original_filename) {
    $upload_subfolder_name = "images/happy_pink";
    $current_folder = dirname(__FILE__);
    
    $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
    
    return join(DIRECTORY_SEPARATOR, $path_segments);
 }

 function file_is_an_image($temporary_path, $new_path) {
     $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
     $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];
     
     $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
     $actual_mime_type        = getimagesize($temporary_path)['mime'];
     
     $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
     $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);
     
     return $file_extension_is_valid && $mime_type_is_valid;
 }
 
 $image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);
 $upload_error_detected = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);

 if ($image_upload_detected) { 
     $image_filename        = $_FILES['image']['name'];
     $temporary_image_path  = $_FILES['image']['tmp_name'];
     $new_image_path        = file_upload_path($image_filename);
     if (file_is_an_image($temporary_image_path, $new_image_path)) {
        if(isset($_POST['cloth_type']) && isset($_POST['price']) && isset($_POST['category'])){
            $clothType = filter_input(INPUT_POST, 'cloth_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $color = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_FULL_SPECIAL_CHARS);


            $checkQuery = 'SELECT * FROM products WHERE product_name = :productName';
            $checkStatement = $db->prepare($checkQuery);
            $checkStatement->bindValue(":productName", $image_filename);
            $checkStatement->execute(); 

            if($checkStatement->rowCount() === 0){
                $query = "INSERT INTO products (product_name, cloth_type, description, color, price, category) VALUES (:product_name, :cloth_type, :description, :color, :price, :category)";
                $statement = $db->prepare(($query));
                
                $statement->bindValue(":product_name", $image_filename);
                $statement->bindValue(":cloth_type", $clothType);
                $statement->bindValue(":description", $description);
                $statement->bindValue(":color", $color);
                $statement->bindValue(":price", $price);
                $statement->bindValue(":category", $category);

                $statement->execute();

                move_uploaded_file($temporary_image_path, $new_image_path);
                $image = new ImageResize($new_image_path);
                $image->resizeToBestFit(600, 600);
                $image->save($new_image_path);

                header("Location:index.php?category=$category");
            }
            else{
                $exists = true;
            }
        } 
     }
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
        <form id="search-form" action="search.php" method="post">
            <input type="text" id="search-bar" placeholder="Search for products">
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

    <?php if(!isset($_SESSION['user_id'])) :?>
    <div id="account-signin">
        <strong>Shop and customize the products you love</strong>
        <a href="user_login.php">Sign In or Create An Account</a>
    </div>
    <?php endif ?>

    <?php if(!isset($_GET['category'])) : ?>
        <div id="home-content">
            <img src="images/happy_pink/model1.png" alt="Brown jacket model">
            <img src="images/happy_pink/model2.png" alt="White shirt model">
        </div>
    <?php endif ?>

    <?php if(isset($_GET['category']) && $_GET['category'] == 'men') : ?>

        <?php if(isset($_SESSION['user_type']) && ($_SESSION['user_type'] == 'Master' || $_SESSION['user_type'] == 'Admin')) :?>
            <div id="add-product">
                <form method='post' enctype='multipart/form-data'>
                    <h3>Add New Product</h3>
                    <label for='image'>Upload Image:</label>
                    <input type='file' name='image' id='image'>
                    <?php if ($upload_error_detected): ?>
                    <p style="color: red;">Error occured while uploading image; Please make sure you are uploading correctly.</p>
                    <?php endif ?>
                    <?php if (isset($exists) && $exists): ?>
                    <p style="color: red;">Image filename exists; Please upload a new image.</p>
                    <?php endif ?>
                    <label for="category">Category:</label>
                        <select name="category">
                            <?php $statementCat->execute(); while($row = $statementCat->fetch()) : ?>
                            <?php $name = $row['name']; $display_name = $row['display_name'];?>
                                <option value=<?=$name?>><?=$display_name?></option>
                            <?php endwhile ?>
                        </select>
                    <input type="text" name="cloth_type" placeholder="Clothing Type">
                    <input type="text" name="color" placeholder="Product Color">
                    <input type="number" min="0" step="0.01" title="Currency" pattern="^\d+(?:\.\d{1,2})?$" name="price" placeholder="Product Price">
                    <textarea rows="5" cols="40" name="description" spellcheck="true" placeholder="Description"></textarea>
                    <input type='submit' name='submit' value='Upload Product' class="btn_log">
                </form>
            </div>
        <?php endif ?>

        <div class="category-content">
        <?php while($row = $statement->fetch()) : ?>
                <?php $productName = $row['product_name']; $cloth_type = $row['cloth_type']; $description= $row['description']; $color = $row['color'];
                    $price = $row['price']; $category = $row['category']; $clothId = $row['cloth_id']?>

                <div class="product">
                    <a href="product_page.php?id=<?=$clothId?>">
                        <h1><?= substr($productName, 0, strpos($productName, '.')) ?></h1>
                        <img src="images/happy_pink/<?=$productName?>" alt="<?=$productName?>">
                    </a>
                    <div id="details">
                        <p>Price: $<?=$price?></p>
                        <p>Color: <?=$color?></p>
                        <p>Clothing Type: <?=$cloth_type?></p>
                    </div>
                    <div class="cart-btn">
                        <a href="#">
                            <input type="submit" class="btn_log" value="Add To Cart"/>  
                        </a>
                    </div>
                    <div class="product-cud">
                        <p><a href="product_edit.php?category=men&product_id=<?=$row['cloth_id']?>">Edit</a></p>
                        <p><a href="product_edit.php?command=delete&category=<?=$category?>&product_id=<?=$row['cloth_id']?>" onclick="return confirm('Are you sure you wish to delete this post?')">Delete Product</a></p>
                    </div>
                    <p>Details:</p>
                    <p style="margin: 5px 0px 0px 40px;"><?=$description?>...</p>
                </div>
        <?php endwhile ?>
        </div>
    <?php endif ?>
</body>
</html>