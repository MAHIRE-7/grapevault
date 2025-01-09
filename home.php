

<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

if(isset($_POST['add_to_wishlist'])){
   
}

if(isset($_POST['add_to_cart'])){
   
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home page</title>

  
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/home-product.css">
   <link rel="stylesheet" href="css/chatbot.css"> 
</head>
<body>
   
<?php include 'header.php'; ?>

<div class="home-bg">

   <section class="home">

      <div class="content">
         <span style="color:aliceblue">Welcome to India's First Wine Selling Platform</span>
         <h3 style="color:#360000">GrapeVault</h3>
         <p>Preserving the Essence of Fine Wines, So You Can Relish Every Drop, Every Time.</p>
         <a href="about.php" style="background:#a31818" class="btn">about us</a>
      </div>

   </section>

</div>

<section class="home-category">

   <h1 class="title" style="color: #360000">shop by category</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/red_wine.jpg" alt="">
         <h3>Red Wine</h3>
         <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Exercitationem, quaerat.</p>
         <a href="category.php?category=red_wine" class="btn" style="background: #360000">Red Wine</a>
      </div>

      <div class="box">
         <img src="images/white_wine.jpg" alt="">
         <h3>White Wine</h3>
         <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Exercitationem, quaerat.</p>
         <a href="category.php?category=white_wine" class="btn" style="background: #360000">White Wine</a>
      </div>

      <div class="box">
         <img src="images/sparkling_wine.jpg" alt="">
         <h3>Sparkling Wine</h3>
         <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Exercitationem, quaerat.</p>
         <a href="category.php?category=sparkling_wine" class="btn" style="background: #360000">Sparkling Wine</a>
      </div>

      <div class="box">
         <img src="images/rose_wine.jpg" alt="">
         <h3>Rose Wine</h3>
         <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Exercitationem, quaerat.</p>
         <a href="category.php?category=rose_wine" class="btn" style="background: #360000">Rose Wine</a>
      </div>

   </div>

</section>

<section class="products">
   <h1 class="title">Latest Products</h1>
   <div class="box-container">
      <?php
         $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 6");
         $select_products->execute();
         if ($select_products->rowCount() > 0) {
            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) { 
      ?>
      <form action="" class="box" method="POST">
         <div class="price">₹<span><?= $fetch_products['price']; ?></span>/-</div>
         <a href="view_page.php?pid=<?= $fetch_products['id']; ?>" class="fas fa-eye"></a>
         <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="Product Image">
         <div class="name"><?= $fetch_products['name']; ?></div>
         <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
         <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
         <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
         <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
         <input type="number" min="1" value="1" name="p_qty" class="qty">
         <input type="submit" value="Add to Wishlist" class="option-btn" name="add_to_wishlist">
         <input type="submit" value="Add to Cart" class="btn" name="add_to_cart">
      </form>
      <?php
            }
         } else {
            echo '<p class="empty">No products added yet!</p>';
         }
      ?>
   </div>
</section>

<?php include 'footer.php'; ?>

<!-- Chatbot  -->
<div class="chatbot-icon" onclick="toggleChatbot()">
   <i class="fas fa-comments"></i>
</div>

<!-- chatbot interface -->
<div id="chatbot-container" style="display: none;">
   <p>Chatbot will be here...</p>
</div>

<script src="js/script.js"></script>
<script>
   function toggleChatbot() {
      const chatbot = document.getElementById('chatbot-container');
      chatbot.style.display = chatbot.style.display === 'none' ? 'block' : 'none';
   }
</script>

</body>
</html>
