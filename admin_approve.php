<?php
@include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit();
}

// admin with id 3 to approve and deny new admin
if ($admin_id != 3) {
    echo "<script>alert('Access denied! Only admin with ID 3 can approve or deny new admins.');</script>";
    header('location:admin_page.php');
    exit();
}

// Fetch pending admin registrations
$select_pending_admins = $conn->prepare("SELECT * FROM `admin_reg`");
$select_pending_admins->execute();
$pending_admins = $select_pending_admins->fetchAll(PDO::FETCH_ASSOC) ?: []; // Ensure it defaults to an empty array

// Storing message
$message = [];

if (isset($_GET['approve'])) {
    $pending_id = $_GET['approve'];

    // Fetching admin registration data from admin_reg table
    $select_pending = $conn->prepare("SELECT * FROM `admin_reg` WHERE id = ?");
    $select_pending->execute([$pending_id]);
    $pending_data = $select_pending->fetch(PDO::FETCH_ASSOC);

    if ($pending_data) {
        $profileImageUrl = $pending_data['image'];
        $licenseImageUrl = $pending_data['license_image'];

        // Inserting data into `admin` table with S3 
        $insert_admin = $conn->prepare("INSERT INTO `admin` (name, email, password, user_type, license_no, created_at, profile_image, license_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_admin->execute([
            $pending_data['name'],
            $pending_data['email'],
            $pending_data['password'],
            'admin',
            $pending_data['license_no'],
            $pending_data['created_at'],
            $profileImageUrl,  
            $licenseImageUrl   
        ]);

        // Remove from admin_reg table
        $delete_pending = $conn->prepare("DELETE FROM `admin_reg` WHERE id = ?");
        $delete_pending->execute([$pending_id]);

        $message[] = 'Admin approved successfully!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Approval</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">

    <style>
        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Fixed position */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.4); /* Black background with opacity */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Styling for the admin cards and license image */
        .license-pic {
            width: 100%;
            height: auto;
            max-height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
            cursor: pointer; /* Cursor indicates image is clickable */
        }

        /* Styling for the admin cards */
        .admin-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
        }

        .admin-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 300px;
            padding: 16px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-header .profile-pic {
            width:auto;
            height: fit-content;
            align-items: center;
            max-height: 150px;
            border-radius: 8px;
            object-fit: cover;
        }

        .card-body p {
            margin: 8px 0;
        }

        .license-section {
            margin-top: 12px;
        }

        .card-footer {
            text-align: center;
            margin-top: 12px;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            margin: 0 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 5px;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="form-container">
    <h3>Pending Admin Approvals</h3>
    <?php if (!empty($message)): ?>
        <p><?php echo implode('<br>', $message); ?></p>
    <?php endif; ?>

    <div class="admin-cards">
        <?php if (count($pending_admins) > 0): ?>
            <?php foreach ($pending_admins as $admin): ?>
                <div class="admin-card">
                    <div class="card-header">
                        <img src="<?php echo htmlspecialchars($admin['image']); ?>" 
                             alt="Profile Picture" class="profile-pic" 
                             onerror="this.src='https://grapevault.s3.amazonaws.com/uploaded_img/default_profile.jpg';">
                    </div>
                    <div class="card-body">
                        <p><strong>ID:</strong> <?php echo htmlspecialchars($admin['id']); ?></p>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($admin['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
                        <p><strong>Password:</strong> <?php echo htmlspecialchars($admin['password']); ?></p>
                        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($admin['dob']); ?></p>
                        <p><strong>Role:</strong> <?php echo htmlspecialchars($admin['role']); ?></p>
                        <p><strong>License No:</strong> <?php echo htmlspecialchars($admin['license_no']); ?></p>
                        <p><strong>Created At:</strong> <?php echo htmlspecialchars($admin['created_at']); ?></p>
                        <div class="license-section">
                            <strong>License Image:</strong><br>
                            <img src="<?php echo htmlspecialchars($admin['license_image']); ?>" 
                                 alt="License Image" class="license-pic" 
                                 onclick="openModal('<?php echo htmlspecialchars($admin['license_image']); ?>')">
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="admin_approve.php?approve=<?php echo $admin['id']; ?>" class="btn">Approve</a><br>
                        <a href="admin_approve.php?deny=<?php echo $admin['id']; ?>" class="btn">Deny</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No pending admin approvals.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Modal for license image -->
<div id="licenseModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="modalImage" src="" alt="License Image" style="width: 100%; height: auto;">
    </div>
</div>

<script>
// Open modal and display the image
function openModal(imageUrl) {
    var modal = document.getElementById("licenseModal");
    var modalImage = document.getElementById("modalImage");
    modal.style.display = "block";
    modalImage.src = imageUrl;
}

// Close modal
function closeModal() {
    var modal = document.getElementById("licenseModal");
    modal.style.display = "none";
}

// Close modal if clicked outside of the modal content
window.onclick = function(event) {
    var modal = document.getElementById("licenseModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

<script src="js/script.js"></script>

</body>
</html>
