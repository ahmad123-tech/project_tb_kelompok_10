<?php
session_start();
include('db_connect.php');

$show_form = true;
$edit_mode = false; // Menandakan apakah sedang dalam mode edit

// Check if a customer profile already exists and we are not in edit mode
if (!isset($_GET['edit_id'])) {
    $sql_check = "SELECT * FROM profiles LIMIT 1";
    $result_check = $conn->query($sql_check);
    if ($result_check->num_rows > 0) {
        $show_form = false;
    }
}

// Fetch the profile data for editing if in edit mode
if (isset($_GET['edit_id'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit_id'];
    $sql_edit = "SELECT * FROM profiles WHERE id = ?";
    $stmt_edit = $conn->prepare($sql_edit);
    $stmt_edit->bind_param("i", $edit_id);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();
    $profile_edit = $result_edit->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
        // Update the profile
        $id = $_POST['edit_id'];
        $nama_customer = $_POST['nama_customer'];
        $email = $_POST['email'];
        $alamat = $_POST['alamat'];
        $no_hp = $_POST['no_hp'];

        $sql = "UPDATE profiles SET nama_customer=?, email=?, alamat=?, no_hp=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nama_customer, $email, $alamat, $no_hp, $id);

        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
        } else {
            $error = "Error updating profile: " . $stmt->error;
        }
    } else {
        // Insert a new profile
        $nama_customer = $_POST['nama_customer'];
        $email = $_POST['email'];
        $alamat = $_POST['alamat'];
        $no_hp = $_POST['no_hp'];

        $sql = "INSERT INTO profiles (nama_customer, email, alamat, no_hp) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nama_customer, $email, $alamat, $no_hp);

        if ($stmt->execute()) {
            $success = "Profile added successfully.";
            $show_form = false; // Hide the form after successful insertion
        } else {
            $error = "Error adding profile: " . $stmt->error;
        }
    }
}

// Fetch all profiles
$sql = "SELECT * FROM profiles";
$result = $conn->query($sql);
$profiles = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Customer</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: wheat;
            margin: 0;
            color: black;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .content {
            width: 90%;
            max-width: 1200px;
            background-color: cornsilk;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            position: relative;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: black;
        }

        input[type="text"],
        input[type="email"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: yellow;
        }

        input[type="text"]:focus,
        input[type="email"]:focus {
            border-color: #007BFF;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: yellow;
        }

        button[type="submit"]:hover {
            background-color: coral;
        }

        .profile-item {
            background-color: cornflowerblue;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 10px;
        }

        .profile-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .edit-button {
            background-color: coral;
            color: black;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: wheat;
        }

        .edit-button:hover {
            background-color: yellow;
        }

        .back-button-container {
            text-align: center;
            margin-top: 20px;
        }

        .back-button {
            color: blue;
            text-decoration: none;
            font-size: 16px;
            transition: black;
        }

        .back-button:hover {
            color: black;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <header>
                <h1>PROFIL CUSTOMER</h1>
            </header>
            <?php if (isset($success)) { echo "<div class='success'>$success</div>"; } ?>
            <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>
            
            <?php if ($show_form || $edit_mode) : ?>
            <div id="profile-form">
                <form method="post" action="profil.php" onsubmit="return submitForm()">
                    <fieldset>
                        <legend>ISI IDENTITAS</legend>
                        <p><br><br></p>
                        <input type="hidden" name="edit_id" id="edit_id" value="<?php echo $edit_mode ? $profile_edit['id'] : ''; ?>">
                        <label for="nama_customer">Nama Customer:</label><br>
                        <input type="text" name="nama_customer" id="nama_customer" value="<?php echo $edit_mode ? $profile_edit['nama_customer'] : ''; ?>" required><br>
                        <label for="email">Email:</label><br>
                        <input type="email" name="email" id="email" value="<?php echo $edit_mode ? $profile_edit['email'] : ''; ?>" required><br>
                        <label for="alamat">Alamat Lengkap:</label><br>
                        <input type="text" name="alamat" id="alamat" value="<?php echo $edit_mode ? $profile_edit['alamat'] : ''; ?>" required><br>
                        <label for="no_hp">No HP:</label><br>
                        <input type="text" name="no_hp" id="no_hp" value="<?php echo $edit_mode ? $profile_edit['no_hp'] : ''; ?>" required><br>
                    </fieldset>
                    <button type="submit" name="save" id="save-button"><?php echo $edit_mode ? 'Update' : 'Simpan'; ?></button>
                </form>
            </div>
            <?php endif; ?>
            
            <div id="profile-list" class="profile-list">
                <?php foreach ($profiles as $profile) : ?>
                <div class="profile-item">
                    <h2>ID: <?php echo $profile['id']; ?> 
                    <p></p>
                    <?php echo $profile['nama_customer']; ?></h2>
                    <p><strong>Email:</strong> <?php echo $profile['email']; ?></p>
                    <p><strong>Alamat:</strong> <?php echo $profile['alamat']; ?></p>
                    <p><strong>No HP:</strong> <?php echo $profile['no_hp']; ?></p>
                    <p><br><br></p>
                    <p><br><br></p>
                    <p><br><br></p>
                    <p><br><br></p>
                    <button class="edit-button" onclick="editProfile(<?php echo $profile['id']; ?>)">Edit</button>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="back-button-container">
                <a href="beranda.php" class="back-button">Kembali</a>
            </div>
        </div>
    </div>
    
    <script>
        function editProfile(id) {
            window.location.href = "profil.php?edit_id=" + id;
        }

        function submitForm() {
            var form = document.getElementById('profile-form');
            form.reset();
            return true;
        }
    </script>
</body>
</html>
