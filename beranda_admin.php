<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Beranda</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: wheat; 
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            background: cornsilk;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            display: flex;
            gap: 20px;
            position: relative;
        }

        header {
            text-align: center;
            width: 100%;
        }

        header h1 {
            font-size: 1.7em;
            color: black;
            margin: 0 0 20px 0;
        }

        nav {
            width: 250px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s ease;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
            position: relative;
        }

        nav ul li {
            margin: 10px 0;
            position: relative;
        }

        nav ul li a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #555;
            font-size: 1em;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background-color 0.3s, color 0.3s;
        }

        nav ul li a i {
            margin-right: 10px;
        }

        nav ul li a:hover {
            background-color: #007BFF;
            color: #fff;
        }

        .logout button {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        .logout button:hover {
            background-color: #c82333;
        }

        .content {
            flex-grow: 1;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .menu-toggle {
            cursor: pointer;
            font-size: 1.5em;
            color: #555;
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1000;
        }

        .hidden {
            display: none;
        }

        @media (max-width: 800px) {
            .container {
                flex-direction: column;
                align-items: center;
            }

            nav {
                width: 100%;
                margin-bottom: 20px;
                transform: translateX(-100%);
            }

            nav ul {
                flex-direction: column;
            }

            nav ul li a {
                justify-content: center;
            }

            .logout {
                margin-top: 20px;
            }

            nav.active {
                transform: translateX(0);
            }
        }

        @media (max-width: 600px) {
            header h1 {
                font-size: 1em;
            }

            nav ul li a {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <div class="menu-toggle" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
    <div class="container">
        <nav id="menu" class="hidden">
            <ul>
                <li><a href="kelola_produk.php"><i class="fas fa-box"></i> Kelola Produk </a></li>
            </ul>
            <div class="logout">
                <form method="post" action="">
                    <button type="submit" name="logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
        </nav>
        <div class="content">
            <header>
                <h1>Admin</h1>
                <img src="image/back.jpg" width="990" height="500">
            </header>
            <!-- Konten utama dapat ditempatkan di sini -->
        </div>
    </div>
    <script>
        function toggleMenu() {
            const menu = document.getElementById('menu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>
