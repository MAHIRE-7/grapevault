<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];


if (!isset($admin_id)) {
    header('location:login.php');
    exit();
}

// Add new eBook
if (isset($_POST['add_ebook'])) {
    $title = htmlspecialchars(strip_tags($_POST['title']), ENT_QUOTES, 'UTF-8');
    $author = htmlspecialchars(strip_tags($_POST['author']), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(strip_tags($_POST['description']), ENT_QUOTES, 'UTF-8');
    

    $cover_image = $_FILES['cover_image']['name'];
    $cover_image = htmlspecialchars(strip_tags($cover_image), ENT_QUOTES, 'UTF-8');

    $cover_image_tmp_name = $_FILES['cover_image']['tmp_name'];
    $cover_image_folder = 'uploaded_img/' . $cover_image;

    $file = $_FILES['file_path']['name'];
    $file = htmlspecialchars(strip_tags($file), ENT_QUOTES, 'UTF-8');

    $file_tmp_name = $_FILES['file_path']['tmp_name'];
    $file_folder = 'uploaded_files/' . $file;

    // Check if the eBook already exists
    $select_ebooks = $conn->prepare("SELECT * FROM `ebooks` WHERE title = ?");
    $select_ebooks->execute([$title]);

    if ($select_ebooks->rowCount() > 0) {
        $message[] = 'eBook title already exists!';
    } else {
        $insert_ebooks = $conn->prepare("INSERT INTO `ebooks` (title, author, description, cover_image, file_path) VALUES (?, ?, ?, ?, ?)");
        $insert_ebooks->execute([$title, $author, $description, $cover_image, $file]);

        if ($insert_ebooks) {
            move_uploaded_file($cover_image_tmp_name, $cover_image_folder);
            move_uploaded_file($file_tmp_name, $file_folder);
            $message[] = 'New eBook added!';
        }
    }
}

// Delete eBook
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $select_delete = $conn->prepare("SELECT cover_image, file_path FROM `ebooks` WHERE id = ?");
    $select_delete->execute([$delete_id]);
    $fetch_delete = $select_delete->fetch(PDO::FETCH_ASSOC);

    unlink('uploaded_img/' . $fetch_delete['cover_image']);
    unlink('uploaded_files/' . $fetch_delete['file_path']);

    $delete_ebook = $conn->prepare("DELETE FROM `ebooks` WHERE id = ?");
    $delete_ebook->execute([$delete_id]);
    header('location:admin_ebooks.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eBooks</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="add-products">
    <h1 class="title">Add New eBook</h1>

    <form action="" method="POST" enctype="multipart/form-data">
        <style>
            .file-label {
    font-size: 15px;
    color: #666;
    margin-bottom: 5px;
    display: block;
}
        </style>
        
            <div class="inputBox">
                <input type="text" name="title"  class="box" required placeholder="Enter eBook Title">
                <input type="text" name="author" class="box" required placeholder="Enter Author Name">

                <label for="cover_image" style="size: 25px;" class="file-label">Upload Cover Image (JPG/PNG)</label>
                <input type="file" name="cover_image" required class="box"  accept="image/jpg, image/jpeg, image/png">

                <label for="cover_image" class="file-label">Upload Ebook (PDF)</label>
                <input type="file" name="file_path" required class="box" accept="application/pdf">
            
            
        </div>
        <textarea name="description" class="box" required placeholder="Enter eBook Description" ></textarea>
        <input type="submit" class="btn" value="Add eBook" name="add_ebook">
    </form>
</section>

<section class="show-products">
    <h1 class="title">Available eBooks</h1>

    <div class="box-container">
        <?php
        $show_ebooks = $conn->prepare("SELECT * FROM `ebooks`");
        $show_ebooks->execute();
        if ($show_ebooks->rowCount() > 0) {
            while ($fetch_ebooks = $show_ebooks->fetch(PDO::FETCH_ASSOC)) {  
        ?>
        <div class="box">
            <img src="uploaded_img/<?= $fetch_ebooks['cover_image']; ?>" alt="">
            <div class="name"><?= $fetch_ebooks['title']; ?></div>
            <div class="author">Author: <?= $fetch_ebooks['author']; ?></div>
            <div class="description"><?= $fetch_ebooks['description']; ?></div>
            <div class="flex-btn">
                <a href="uploaded_files/<?= $fetch_ebooks['file_path']; ?>" class="download-btn" download>Download</a>
                <a href="admin_ebooks.php?delete=<?= $fetch_ebooks['id']; ?>" class="delete-btn" onclick="return confirm('Delete this eBook?');">Delete</a>
            </div>
        </div>
        <?php
            }
        } else {
            echo '<p class="empty">No eBooks added yet!</p>';
        }
        ?>
    </div>
</section>

<script src="js/script.js"></script>
</body>
</html>
