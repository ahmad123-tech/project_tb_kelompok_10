<?php
session_start();
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == 'admin') {
                $redirect_url = 'beranda_admin.php';
            } else {
                $redirect_url = 'beranda.php';
            }
            ?>
            <script>
                setTimeout(function () {
                    alert('Login Berhasil');
                    window.location.href = '<?php echo $redirect_url; ?>';
                }, 3000);
            </script>
            <?php
        } else {
            ?>
            <script>
                setTimeout(function () {
                    alert('Username Atau Password Salah.');
                }, 3000);
            </script>
            <?php
        }
    } elseif (isset($_POST['register'])) {
        $new_username = $_POST['new_username'];
        $new_password = $_POST['new_password'];
        $email = $_POST['email'];

        // Check if username or email already exists
        $check_sql = "SELECT * FROM users WHERE username='$new_username' OR email='$email'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            ?>
            <script>
                setTimeout(function () {
                    alert('Username Atau Email Sudah Terdaftar');
                }, 3000);
            </script>
            <?php
        } else {
            $sql = "INSERT INTO users (username, password, email, role) VALUES ('$new_username', '$new_password', '$email', 'user')";
            if ($conn->query($sql) === TRUE) {
                ?>
                <script>
                    setTimeout(function () {
                        alert('Akun Berhasil Dibuat');
                    }, 3000);
                </script>
                <?php
            } else {
                ?>
                <script>
                    setTimeout(function () {
                        alert('Error: <?php echo $conn->error; ?>');
                    }, 3000);
                </script>
                <?php
            }
        }
    } elseif (isset($_POST['reset_password'])) {
        $email = $_POST['email'];
        $new_password = $_POST['new_password'];

        // Check if email exists
        $check_sql = "SELECT * FROM users WHERE email='$email'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            $sql = "UPDATE users SET password='$new_password' WHERE email='$email'";
            if ($conn->query($sql) === TRUE) {
                ?>
                <script>
                    setTimeout(function () {
                        alert('Password Berhasil Diubah');
                    }, 3000);
                </script>
                <?php
            } else {
                ?>
                <script>
                    setTimeout(function () {
                        alert('Error: <?php echo $conn->error; ?>');
                    }, 3000);
                </script>
                <?php
            }
        } else {
            ?>
            <script>
                setTimeout(function () {
                    alert('Email Tidak Ditemukan');
                }, 3000);
            </script>
            <?php
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: cornflowerblue;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: navajowhite;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        .input-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }
        label {
            margin-bottom: 5px;
        }
        input[type="text"], input[type="password"], input[type="email"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 10px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
        }
        button:hover {
            background-color: coral;
        }
        .toggle {
            text-align: center;
            margin-top: 10px;
            cursor: pointer;
            color: #007bff;
        }
        .toggle:hover {
            text-decoration: underline;
        }
        .message {
            text-align: center;
            color: red;
        }
        .success {
            text-align: center;
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 id="loginTitle">Login</h2>
        <?php if (isset($error)) { echo "<p class='message'>$error</p>"; } ?>
        <?php if (isset($success)) { echo "<p class='success'>$success</p>"; } ?>
        <form method="post" action="login.php" id="loginForm">
            <div class="input-group">
                <label for="username">Username:</label>
                <input type="text" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login">Login</button>
            <p class="toggle" id="toggleToForgotPassword">Lupa password?</p>
            <p class="toggle" id="toggleToRegister">Belum punya akun? Daftar di sini</p>
        </form>
        
        <h2 style="display: none;" id="registerTitle">Register</h2>
        <form method="post" action="login.php" id="registerForm" style="display: none;">
            <div class="input-group">
                <label for="new_username">Username:</label>
                <input type="text" name="new_username" required>
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="new_password">Password:</label>
                <input type="password" name="new_password" required>
            </div>
            <div class="register-button">
                <button type="submit" name="register">Register</button>
            </div>
            <p class="toggle" id="toggleToLogin">Sudah punya akun? Masuk di sini</p>
        </form>

        <h2 style="display: none;" id="forgotPasswordTitle">Reset Password</h2>
        <form method="post" action="login.php" id="forgotPasswordForm" style="display: none;">
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" required>
            </div>
            <div class="register-button">
                <button type="submit" name="reset_password">Reset Password</button>
            </div>
            <p class="toggle" id="toggleToLoginFromForgot">Sudah punya akun? Masuk di sini</p>
        </form>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');
        const registerTitle = document.getElementById('registerTitle');
        const loginTitle = document.getElementById('loginTitle');
        const forgotPasswordTitle = document.getElementById('forgotPasswordTitle');

        const toggleToRegister = document.getElementById('toggleToRegister');
        const toggleToLogin = document.getElementById('toggleToLogin');
        const toggleToForgotPassword = document.getElementById('toggleToForgotPassword');
        const toggleToLoginFromForgot = document.getElementById('toggleToLoginFromForgot');

        toggleToRegister.addEventListener('click', function() {
            loginForm.style.display = 'none';
            registerForm.style.display = 'block';
            forgotPasswordForm.style.display = 'none';
            registerTitle.style.display = 'block';
            loginTitle.style.display = 'none';
            forgotPasswordTitle.style.display = 'none';
        });

        toggleToLogin.addEventListener('click', function() {
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
            forgotPasswordForm.style.display = 'none';
            registerTitle.style.display = 'none';
            loginTitle.style.display = 'block';
            forgotPasswordTitle.style.display = 'none';
        });

        toggleToForgotPassword.addEventListener('click', function() {
            loginForm.style.display = 'none';
            registerForm.style.display = 'none';
            forgotPasswordForm.style.display = 'block';
            registerTitle.style.display = 'none';
            loginTitle.style.display = 'none';
            forgotPasswordTitle.style.display = 'block';
        });

        toggleToLoginFromForgot.addEventListener('click', function() {
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
            forgotPasswordForm.style.display = 'none';
            registerTitle.style.display = 'none';
            loginTitle.style.display = 'block';
            forgotPasswordTitle.style.display = 'none';
        });
    </script>
</body>
</html>
