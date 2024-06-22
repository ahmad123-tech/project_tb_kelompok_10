<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order'])) {
    if (isset($_POST['produk']) && isset($_POST['harga']) && isset($_POST['jumlah_order'])) {
        $produk = $_POST['produk'];
        $harga = floatval($_POST['harga']);
        $jumlah_order = intval($_POST['jumlah_order']);

        $total_harga = $harga * $jumlah_order; // Calculate total price

        // Insert order into nota table
        $sql = "INSERT INTO `nota` (nama_produk, harga_produk, jumlah_order, total_harga, tgl_order) VALUES ('$produk', '$harga', '$jumlah_order', '$total_harga', NOW())";

        if ($conn->query($sql) === TRUE) {
            $success = "Order placed successfully.";
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $error = "Please fill in all fields.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel'])) {
    if (isset($_POST['order_id'])) {
        $order_id = intval($_POST['order_id']);

        $sql = "DELETE FROM `pesanan` WHERE id_produk='$order_id'";
        if ($conn->query($sql) === TRUE) {
            $success = "Order cancelled successfully.";
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $error = "Order ID is missing.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nota Pembayaran</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
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
            background: #FFF8DC;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        h2 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 15px rgba(64, 64, 64, 0.15);
            background: white;
            border-radius: 12px;
            overflow: hidden;
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
            font-weight: 700;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .total-harga {
            text-align: right;
            font-weight: 700;
            margin-top: 20px;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-weight: 700;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Nota Pembayaran</h2>
        <table>
            <tr>
                <th>ID Transaksi</th>
                <th>ID Produk</th>
                <th>Produk</th>
                <th>Harga Satuan</th>
                <th>Jumlah Order</th>
                <th>Total Harga</th>
                <th>Tanggal Order</th>
            </tr>
            <?php
            $sql = "SELECT 
                        id_transaksi, 
                        id_produk, 
                        nama_produk, 
                        harga_produk, 
                        jumlah_order, 
                        total_harga, 
                        tgl_order, 
                        id 
                    FROM 
                        `order` 
                    JOIN 
                        `profiles` 
                    ON 
                        id_produk = id_produk";

            $result = $conn->query($sql);
            $grand_total = 0;

            while ($order = $result->fetch_assoc()) {
                $id = $order['id'];
                $sql_profiles = "SELECT id, nama_customer, email, alamat, no_hp FROM `profiles` WHERE id='$id'";
                $result_profiles = $conn->query($sql_profiles);
                $profile = $result_profiles->fetch_assoc();

                echo "<tr>";
                echo "<td>{$order['id_transaksi']}</td>";
                echo "<td>{$order['id_produk']}</td>";
                echo "<td>{$order['nama_produk']}</td>";
                echo "<td>Rp" . number_format($order['harga_produk'], 0, ',', '.') . "</td>";
                echo "<td>{$order['jumlah_order']}</td>";
                echo "<td>Rp" . number_format($order['total_harga'], 0, ',', '.') . "</td>";
                echo "<td>{$order['tgl_order']}</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td colspan='7'>";
                echo "<ul>";
                echo "<li>ID: {$profile['id']}</li>";
                echo "<li>Nama Customer: {$profile['nama_customer']}</li>";
                echo "<li>Email: {$profile['email']}</li>";
                echo "<li>Alamat: {$profile['alamat']}</li>";
                echo "<li>No. HP: {$profile['no_hp']}</li>";
                echo "</ul>";
                echo "</td>";
                echo "</tr>";

                // Insert into nota table
                $sql_nota = "INSERT INTO `nota` 
                             (id_transaksi, id_produk, nama_produk, harga_produk, jumlah_order, total_harga, tgl_order, id, nama_customer, email, alamat, no_hp) 
                             VALUES 
                             ('{$order['id_transaksi']}', '{$order['id_produk']}', '{$order['nama_produk']}', '{$order['harga_produk']}', '{$order['jumlah_order']}', '{$order['total_harga']}', '{$order['tgl_order']}', '{$profile['id']}', '{$profile['nama_customer']}', '{$profile['email']}', '{$profile['alamat']}', '{$profile['no_hp']}')";
                
                $conn->query($sql_nota);

                $grand_total += $order['total_harga'];
            }
            ?>
        </table>
        <div class="total-harga">
            Total Harga : Rp<?php echo number_format($grand_total, 0, ',', '.'); ?>
        </div>
        <a href="beranda.php" class="back-button">Kembali ke Beranda</a>
    </div>
</body>
</html>
