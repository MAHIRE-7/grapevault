<?php
@include 'config.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
    $select_user->execute([$user_id]);
    $user = $select_user->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo '<div class="card" id="userDetailsCard">';
        echo '<div class="user-info">'; 
        echo '<img class="user-image" src="uploaded_img/' . $user['image'] . '" alt="User Image">';
        echo '<div class="user-details">'; 
        echo '<h2>User Details</h2>';
        echo '<p>User ID: <strong>' . $user['id'] . '</strong></p>';
        echo '<p>Name: <strong>' . $user['name'] . '</strong></p>';
        echo '<p>Email: <strong>' . $user['email'] . '</strong></p>';
        echo '<p>User Type: <strong>' . $user['user_type'] . '</strong></p>';
        echo '</div>'; 
        echo '</div>'; 
        echo '</div>'; 
    } else {
        echo 'User not found.';
    }
} else {
    echo 'No user ID provided.';
}
?>

<style>
    /* Card styles */
    .card {
        width: 90%;  
        max-width: 600px; 
        height: auto;  
        padding: 15px; 
        background-color: #fff; 
        border-radius: 10px; 
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); 
        display: flex;
        align-items: center; 
        margin: 20px auto; 
    }

    .user-info {
        display: flex; 
        align-items: center;
        width: 100%; 
    }

    .user-image {
        width: 200px; 
        height: 200px; 
        border-radius: 5px; 
        object-fit: cover;
        margin-right: 15px; 
    }

    .user-details {
        flex: 1; 
    }

    h2 {
        margin-bottom: 10px;
        font-size: 20px; 
    }

    p {
        margin: 5px 0;
        font-size: 14px; 
    }

    @media (max-width: 600px) {
        .user-info {
            flex-direction: column; 
            align-items: flex-start;
        }

        .user-image {
            margin-bottom: 10px; 
            margin-right: 0; 
        }
    }
</style>

<script>
    
    document.getElementById('userDetailsCard').classList.add('show');
</script>
