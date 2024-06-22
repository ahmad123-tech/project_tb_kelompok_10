<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    if (isset($_POST['id_produk']) && isset($_POST['nama_produk']) && isset($_POST['harga_produk']) && isset($_POST['stok_produk']) && isset($_FILES['gambar_produk'])) {
        $id_produk = $_POST['id_produk'];
        $nama_produk = $_POST['nama_produk'];
        $harga_produk = floatval($_POST['harga_produk']);
        $stok_produk = intval($_POST['stok_produk']);
        
        // Handle file upload
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["gambar_produk"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["gambar_produk"]["tmp_name"]);

        if ($check !== false && ($_FILES["gambar_produk"]["size"] <= 500000) && in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            if (move_uploaded_file($_FILES["gambar_produk"]["tmp_name"], $target_file)) {
                $gambar_produk = $target_file;

                // Insert product into database
                $sql = "INSERT INTO produk (id_produk, nama_produk, harga_produk, stok_produk, gambar_produk) VALUES ('$id_produk', '$nama_produk', '$harga_produk', '$stok_produk', '$gambar_produk')";

                if ($conn->query($sql) === TRUE) {
                    $success = "Product added successfully.";
                } else {
                    $error = "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        } else {
            $error = "Invalid file or file is too large.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Query to delete product from database
    $delete_sql = "DELETE FROM produk WHERE id_produk = '$delete_id'";

    if ($conn->query($delete_sql) === TRUE) {
        $success = "Product deleted successfully.";
    } else {
        $error = "Error deleting product: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kelola Produk</title>
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
            background: whitesmoke;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        header {
            text-align: center;
            width: 100%;
        }

        header h2 {
            font-size: 1.8em;
            color: #007BFF;
            margin: 0 0 20px 0;
        }

        .success, .error {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        form {
            margin: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        label {
            font-weight: 600;
        }

        input, select, button {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        @media (max-width: 800px) {
            .container {
                padding: 20px;
            }

            table, th, td {
                font-size: 0.9em;
            }
        }

        @media (max-width: 600px) {
            header h2 {
                font-size: 1.5em;
            }

            table, th, td {
                font-size: 0.8em;
            }
        }

        .back-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .product-option {
            display: flex;
            align-items: center;
        }

        .product-option img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }
        </style>
</head>
<body>
    <div class="container">
        <header>
            <h2>Kelola Produk</h2>
        </header>
        <?php if (isset($success)) { echo "<div class='success'>$success</div>"; } ?>
        <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>
        <!-- Form for adding product -->
        <form method="post" action="kelola_produk.php" enctype="multipart/form-data">
            <label for="id_produk">ID Produk</label>
            <input type="text" id="id_produk" name="id_produk" required>
            <label for="nama_produk">Nama Produk</label>
            <input type="text" id="nama_produk" name="nama_produk" required>
            <label for="harga_produk">Harga Produk</label>
            <input type="number" id="harga_produk" name="harga_produk" required>
            <label for="stok_produk">Stok Produk</label>
            <input type="number" id="stok_produk" name="stok_produk" required>
            <label for="gambar_produk">Gambar Produk</label>
            <input type="file" id="gambar_produk" name="gambar_produk" required>
            <button type="submit" name="add_product">TAMBAH PRODUK</button>
        </form>
        <!-- Table to display products -->
        <table>
            <!-- Table headers -->
            <thead>
                <tr>
                    <th>ID Produk</th>
                    <th>Nama Produk</th>
                    <th>Harga Produk</th>
                    <th>Stok Produk</th>
                    <th>Gambar Produk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch products from database and display in table rows
                $sql = "SELECT * FROM produk";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id_produk'] . "</td>";
                        echo "<td>" . $row['nama_produk'] . "</td>";
                        echo "<td>" . $row['harga_produk'] . "</td>";
                        echo "<td>" . $row['stok_produk'] . "</td>";
                        echo "<td><img src='" . $row['gambar_produk'] . "' alt='" . $row['nama_produk'] . "' width='50' height='50'></td>";
                        echo "<td><button onclick='confirmDelete(" . $row['id_produk'] . ")'>Hapus</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Belum ada produk yang ditambahkan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <!-- Back button to admin homepage -->
        <a href="beranda_admin.php" class="back-button">Kembali ke Beranda</a>
    </div>
    <!-- JavaScript function for confirm delete -->
    <script>
        function confirmDelete(id) {
            if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
                window.location.href = 'kelola_produk.php?delete_id=' + id;
            }
        }
    </script>
</body>
</html>
