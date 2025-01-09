<?php
include 'config.php';

if (isset($_POST['submit'])) {
    
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $pass = md5($_POST['pass']);
    $cpass = md5($_POST['cpass']);
    $dob = filter_var($_POST['dob'], FILTER_SANITIZE_STRING);
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        
        $image = $_FILES['image']['name'];
    } else {
        
        $image = ''; 
    }
    


    
    if ($pass !== $cpass) {
        $message[] = 'Passwords do not match!';
        $pass = $cpass = '';
    } else {
        
        $check_restricted = $conn->prepare("SELECT * FROM restricted WHERE email = ?");
        $check_restricted->execute([$email]);
        
        if ($check_restricted->rowCount() > 0) {
           
            $restricted_user = $check_restricted->fetch(PDO::FETCH_ASSOC);
            $stored_dob = $restricted_user['dob'];
            
            
            if ($dob !== $stored_dob) {
                $message[] = 'Sorry we cant proceed with you !!';
            } else {
                
                $birthdate = new DateTime($dob);
                $today = new DateTime();
                $age = $today->diff($birthdate)->y;

                if ($age < 22) {
                    $message[] = 'You must be 22 years or older to register.';
                } else {
                    
                    $insert_user = $conn->prepare("INSERT INTO users (name, email, password, dob, image, created_at, user_type) VALUES (?, ?, ?, ?, ?, NOW(), 'user')");
                    $inserted = $insert_user->execute([$name, $email, $pass, $dob, $image]);

                    if ($inserted) {
                        
                        $delete_restricted = $conn->prepare("DELETE FROM restricted WHERE email = ?");
                        $delete_restricted->execute([$email]);
                        
                        move_uploaded_file($image_tmp_name, $image_folder);
                        $message[] = 'Registration successful! Please log in.';
                        header('Location: login.php');
                        exit();
                    } else {
                        $message[] = 'Registration failed. Please try again.';
                    }
                }
            }
        } else {
            
            $check_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $check_email->execute([$email]);
            
            if ($check_email->rowCount() > 0) {
                $message[] = 'Email already registered! Please log in.';
            } else {
               
                $image = $_FILES['image']['name'];
                $image_tmp_name = $_FILES['image']['tmp_name'];
                $image_folder = 'uploaded_img/' . $image;

                // Inserting new user data into the restricted table if age < 22
                $birthdate = new DateTime($dob);
                $today = new DateTime();
                $age = $today->diff($birthdate)->y;

                if ($age < 22) {
                    // Inserting into restricted table
                    $insert_restricted = $conn->prepare("INSERT INTO restricted (email, dob) VALUES ( ?, ?)");
                    $insert_restricted->execute([ $email, $dob]);

                    $message[] = 'Registration restricted due to age. You will be eligible once you turn 22.';
                } else {
                    // Inserting into users table
                    $insert_user = $conn->prepare("INSERT INTO users (name, email, password, dob, image, created_at, user_type) VALUES (?, ?, ?, ?, ?, NOW(), 'user')");
                    $inserted = $insert_user->execute([$name, $email, $pass, $dob, $image]);

                    if ($inserted) {
                        move_uploaded_file($image_tmp_name, $image_folder);
                        $message[] = 'Registration successful! Please log in.';
                        header('Location: login.php');
                        exit();
                    } else {
                        $message[] = 'Registration failed. Please try again.';
                    }
                }
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
    <title>Register</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <span>' . $msg . '</span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        ';
    }
}
?>

<div class="container-fluid d-flex" style="height: 100vh;">
    <!-- Left section with background image -->
    <div class="left-section" style="background-image: url('images/dwine.jpg');"></div>

    <!-- Right section with registration form -->
    <div class="right-section d-flex justify-content-center align-items-center">
    <div class="login-card">
            <h3 class="text-center">Register Now</h3>
            <form action="" enctype="multipart/form-data" method="POST">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="Enter your name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <input type="password" name="pass" class="form-control" placeholder="Enter your password" required>
                </div>
                <div class="form-group">
                    <input type="password" name="cpass" class="form-control" placeholder="Confirm your password" required>
                </div>
                <div class="form-group">
                    <input type="text" name="dob" class="form-control" placeholder="Enter your Date of Birth (YYYY-MM-DD)" value="<?php echo isset($dob) ? htmlspecialchars($dob) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <input type="file" name="image" class="form-control" required accept="image/jpg, image/jpeg, image/png">
                </div>
                <button type="submit" name="submit" class="btn btn-primary btn-block">Register Now</button>
                <p class="text-center mt-3">Already have an account? <a href="login.php">Login now</a></p>
            </form>
        </div>
    </div>
</div>

</body>
</html>
