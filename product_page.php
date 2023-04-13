
<?php

/*******w********
    
    Name: Lance Fuentes
    Date: April 12, 2023
    Description: Page of a single product with reviews

****************/

require('connect.php');
session_start();

$queryCat = 'SELECT * FROM categories';
$statementCat = $db->prepare($queryCat);
$statementCat->execute(); 

if(isset($_GET['cloth_id'])){
    $cloth_id = $_GET['cloth_id'];

    $prodQuery = "SELECT * FROM products WHERE cloth_id = :cloth_id";
    $prodStatement = $db->prepare($prodQuery);
    $prodStatement->bindValue(':cloth_id', $cloth_id);
    $prodStatement->execute(); 

    $revQuery = "SELECT * FROM reviews WHERE cloth_id = :cloth_id ORDER BY id DESC";
    $revStatement = $db->prepare($revQuery);
    $revStatement->bindValue(':cloth_id', $cloth_id);
    $revStatement->execute();
}
else{
    header('location:index.php');
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

    <?php $row = $prodStatement->fetch()?>
    <?php $productName = $row['product_name']; $cloth_type = $row['cloth_type']; $description= $row['description']; $color = $row['color'];
        $price = $row['price']; $category = $row['category']; ?>
    <?php if(isset($_GET['command']) && isset($_GET['product_id']) && $_GET['command'] == 'delete') : unlink("images/happy_pink/$productName"); endif?>
    
    <div class="product-single">
        <h1><?= substr($productName, 0, strpos($productName, '.')) ?></h1>
        <img src="images/happy_pink/<?=$productName?>" alt="<?=$productName?>">
        <div id="details">
            <p>Price: $<?=$price?></p>
            <p>Color: <?=$color?></p>
            <p>Clothing Type: <?=$cloth_type?></p>
        </div>
        <p>Details:</p>
        <p style="margin: 5px 0px 0px 40px;"><?=$description?>...</p>
    </div>

    <h1 class="edit">Product Reviews</h1>
    
    <div class="cart-btn">
        <a class="create-review" href="review_process.php?command=create&cloth_id=<?=$cloth_id?>">
            <input type="submit" class="btn_log" value="Create Review"/>  
        </a>
    </div>

    <?php while($row = $revStatement->fetch()): ?>
    <?php $name = $row['name']; $dateCreated = strtotime($row['date_created']); $dateUpdated= strtotime($row['date_updated']); $review = $row['review']; $userId = $row['user_id']; $id=$row['id']?>
        <div id="reviews">
            <div class="reviewer">
                <h3 class="name" style="word-break: break-word;"><?= $row['name']?></h3>

                <div class="dates">
                    <p><small>Review Created: <?=date('F j, Y g:i a', $dateCreated)?></small></p>
                    <p><small>Last Updated: <?=date('F j, Y g:i a', $dateUpdated)?></small></p>
                    <?php if(isset($_SESSION['user_id']) && $userId == $_SESSION['user_id']) :?>
                        <p><small><a href="review_process.php?command=edit&rev_id=<?=$id?>&cloth_id=<?=$cloth_id?>">Edit Review</a></small></p>
                    <?php endif ?>
                </div>

                <div class='content' style="word-break: break-word;">
                    <?= nl2br($row['review']) ?>
                </div>
            </div>
        </div>
    <?php endwhile ?>


</body>
</html>