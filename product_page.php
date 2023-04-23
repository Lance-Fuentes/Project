
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

    if(isset($_POST['userCommand']) && $_POST['userCommand'] == 'Sort'){
        $order = $_POST['sortRev'];

        if($order == 'name'){
            $sortQuery = 'name';
        }
        else if($order == 'nameDESC'){
            $sortQuery = 'name DESC';
        }
        else if($order == 'created'){
            $sortQuery = 'date_created';
        }
        else if($order == 'updated'){
            $sortQuery = 'date_updated DESC';
        }
        else{
            $sortQuery = 'id DESC';
        }

        $revQuery = "SELECT * FROM reviews WHERE cloth_id = :cloth_id ORDER BY $sortQuery";
        $revStatement = $db->prepare($revQuery);
        $revStatement->bindValue(':cloth_id', $cloth_id);
        $revStatement->execute();
    }

}
else{
    header('location:index.php');
    exit();
}

$images = array(
    "images/Captcha/Cap1.png" => "ABFE", 
    "images/Captcha/Cap2.png" => "UVQS", 
    "images/Captcha/Cap3.png" => "DSPRA"
  ); 
  if(!isset($_SESSION['captcha_image'])){
    $_SESSION['captcha_image'] = array_rand($images);
  }

if(isset($_POST['userCommand']) && $_POST['userCommand'] == 'Submit Captcha'){
    if(!empty($_POST['captcha']) && $_POST['captcha'] == $images[$_SESSION['captcha_image']]){
        $_SESSION['person'] = true;
    }
    else{
        $_SESSION['userCaptchaText'] = $_POST['captcha'];
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
    
    <?php if((isset($_SESSION['person']) || isset($_SESSION['user_id'])) === false) :?>
        <div class="captcha-form">
            <img src="<?= $_SESSION['captcha_image'] ?>" alt="Captcha">
            <form action="product_page.php?cloth_id=<?=$_GET['cloth_id']?>" method="post" class="form-cap">
                <input type="text" name="captcha" id="captcha" placeholder="Enter text to create review" value="<?=isset($_SESSION['userCaptchaText']) ? $_SESSION['userCaptchaText'] : ""?>">
                <input type="submit" class="btn_log" name="userCommand" value="Submit Captcha">
            </form>
        </div>
    <?php endif ?>

    <?php if(isset($_SESSION['person']) || isset($_SESSION['user_id'])) :?>
        <div class="cart-btn">
            <a class="create-review" href="review_process.php?command=create&cloth_id=<?=$cloth_id?>">
                <input type="submit" class="btn_log" value="Create Review"/>  
            </a>
        </div>
    <?php endif ?>

    <form action="product_page.php?cloth_id=<?=$cloth_id?>" method="post">
        <label for="sortRev">Sort Reviews:</label>
        <select name="sortRev" id="sortRev">
        <option value="default">Default</option>
        <option value="name">Name Ascending</option>
        <option value="nameDESC">Name Descending</option>
        <option value="created">Date Created</option>
        <option value="updated">Date Updated Descending</option>
        </select>
        <input type="submit" name="userCommand" class="btn_log" value="Sort">
    </form>

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