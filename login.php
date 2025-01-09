<?php
@include 'config.php';
session_start();

if(isset($_POST['submit'])){
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_EMAIL);
   $pass = md5($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $sql = "SELECT * FROM `users` WHERE email = ? AND password = ?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$email, $pass]);
   $rowCount = $stmt->rowCount();
   $row = $stmt->fetch(PDO::FETCH_ASSOC);

   if($rowCount > 0){
      if($row['user_type'] == 'admin'){
         $_SESSION['admin_id'] = $row['id'];
         header('location:admin_page.php');
      } elseif($row['user_type'] == 'user'){
         $_SESSION['user_id'] = $row['id'];
         header('location:home.php');
      } else {
         $message[] = 'No user found!';
      }
   } else {
      $message[] = 'Incorrect email or password!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://accounts.google.com/gsi/client" async defer></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/login.css">
</head>
<style>
   /* Styling for the custom alert */
.custom-alert {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1050;
    background-color: #f8d7da; 
    color: #721c24; 
    border: 1px solid #f5c6cb; 
    border-radius: 0;
    font-weight: bold;
}

.custom-alert .close {
    color: #721c24;
}

.custom-alert .alert-icon {
    margin-right: 8px;
}

</style>
<body>

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '
      <div class="alert custom-alert alert-dismissible fade show" role="alert">
         <span class="alert-icon"><i class="fas fa-exclamation-circle"></i></span>
         '.$msg.'
         <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
         </button>
      </div>
      ';
   }
}
?>


<div class="container-fluid vh-100 d-flex p-0">
   <div class="row w-100 h-100 no-gutters">
      <div class="col-md-6 d-none d-md-flex align-items-center bg-cover" style="background-image: url('images/dwine4.jpg'); background-size: cover;"></div>
      <div class="col-md-6 d-flex justify-content-center align-items-center p-4">
         <div class="login-card w-100" style="max-width: 400px;">
            <h3 class="text-center mb-4">Login Now</h3>
            <form action="" method="POST">
               <div class="form-group">
                  <input type="email" name="email" class="form-control" placeholder="Enter your email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
               </div>
               <div class="form-group">
                  <input type="password" name="pass" class="form-control" placeholder="Enter your password" required>
               </div>
               <button type="submit" name="submit" class="btn btn-primary btn-block">Login Now</button>
               <p class="text-center mt-3">Don't have an account? <a href="register.php">Register Now</a></p>
            </form>
            
            <div class="text-center mt-4">
              
               </div>
               <div class="g_id_signin" 
                    data-type="standard"
                    data-shape="rectangular"
                    data-theme="outline"
                    data-text="sign_in_with"
                    data-size="large">
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
   function handleCredentialResponse(response) {
      const responsePayload = decodeJwtResponse(response.credential);
      window.location.href = 'process_google_login.php?id_token=' + response.credential;
   }

   function decodeJwtResponse(token) {
      var base64Url = token.split('.')[1];
      var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
      var jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function(c) {
         return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
      }).join(''));
      return JSON.parse(jsonPayload);
   }
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
