<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order'])) {
    if (isset($_POST['produk']) && isset($_POST['id_produk']) && isset($_POST['harga']) && isset($_POST['jumlah_order'])) {
        $produk = $_POST['produk'];
        $id_produk = $_POST['id_produk'];
        $harga = floatval($_POST['harga']);
        $jumlah_order = intval($_POST['jumlah_order']);
        $total_harga = $harga * $jumlah_order; // Hitung total harga

        // Tambahkan kolom id_produk pada query INSERT INTO
        $sql = "INSERT INTO `order` (id_produk, nama_produk, harga_produk, jumlah_order, total_harga, tgl_order) VALUES ('$id_produk', '$produk', '$harga', '$jumlah_order', '$total_harga', NOW())";

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

        $sql = "DELETE FROM `order` WHERE id_transaksi='$order_id'";
        if ($conn->query($sql) === TRUE) {
            $success = "Order cancelled successfully.";
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $error = "Order ID is missing.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    $_SESSION['checkout'] = true; // Set checkout status to true
    $sql = "SELECT SUM(total_harga) AS total_checkout FROM `order`";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_checkout = $row['total_checkout'];
        $checkout_message = "Total Harga Semua Order: Rp" . number_format($total_checkout, 2, ',', '.');
    } else {
        $checkout_message = "Tidak ada order untuk dihitung.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaksi/Order Pesanan</title>
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

        .custom-select {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .select-selected {
            background-color: white;
        }

        .select-selected:after {
            content: "";
            position: absolute;
            top: 14px;
            right: 10px;
            width: 0;
            height: 0;
            border: 6px solid transparent;
            border-color: #007BFF transparent transparent transparent;
        }

        .select-selected.select-arrow-active:after {
            border-color: transparent transparent #007BFF transparent;
            top: 7px;
        }

        .select-items div, .select-selected {
            color: #007BFF;
            padding: 10px;
            border: 1px solid transparent;
            border-color: transparent transparent rgba(0, 0, 0, 0.1) transparent;
            cursor: pointer;
            user-select: none;
        }

        .select-items {
            position: absolute;
            background-color: white;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 99;
        }

        .select-hide {
            display: none;
        }

        .select-items div:hover, .same-as-selected {
            background-color: rgba(0, 123, 255, 0.1);
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
            <h2>ORDER PRODUK</h2>
        </header>
        <?php if (isset($success)) { echo "<div class='success'>$success</div>"; } ?>
        <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>
        <?php if (isset($checkout_message)) { echo "<div class='success'>$checkout_message</div>"; } ?>
        <form method="post" action="transaksi.php">
            <label for="produk">PILIH PRODUK</label>
            <div class="custom-select">
                <div class="select-selected">Pilih Produk</div>
                <div class="select-items select-hide">
                    <?php
                    // Fetch product data from the database
                    $sql_produk = "SELECT * FROM produk";
                    $result_produk = $conn->query($sql_produk);

                    if ($result_produk->num_rows > 0) {
                        while ($row_produk = $result_produk->fetch_assoc()) {
                            echo "<div data-id='" . $row_produk['id_produk'] . "' data-name='" . $row_produk['nama_produk'] . "' data-harga='" . $row_produk['harga_produk'] . "' class='product-option'>";
                            echo "<img src='" . $row_produk['gambar_produk'] . "' alt='" . $row_produk['nama_produk'] . "'>";
                            echo "<span>" . $row_produk['nama_produk'] . "</span>";
                            echo "</div>";
                        }
                    } else {
                        echo "<div>Tidak ada produk tersedia</div>";
                    }
                    ?>
                </div>
            </div>
            <input type="hidden" name="id_produk" id="id_produk">
            <input type="hidden" name="produk" id="produk">
            <label for="harga">HARGA SATUAN</label>
            <input type="text" name="harga" id="harga" readonly>
            <label for="jumlah_order">JUMLAH ORDER</label>
            <input type="number" name="jumlah_order" id="jumlah_order" min="1">
            <button type="submit" name="order"><i class="fas fa-plus"></i> Tambah Order</button>
        </form>
        <h3>DAFTAR ORDER</h3>
        <table>
            <thead>
                <tr>
                    <th>ID Transaksi</th>
                    <th>Nama Produk</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah Order</th>
                    <th>Total Harga</th>
                    <th>Tanggal Order</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM `order` ORDER BY tgl_order DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id_transaksi'] . "</td>";
                        echo "<td>" . $row['nama_produk'] . "</td>";
                        echo "<td>Rp" . number_format($row['harga_produk'], 2, ',', '.') . "</td>";
                        echo "<td>" . $row['jumlah_order'] . "</td>";
                        echo "<td>Rp" . number_format($row['total_harga'], 2, ',', '.') . "</td>";
                        echo "<td>" . $row['tgl_order'] . "</td>";
                        echo "<td>
                                <form method='post' action='transaksi.php' style='display:inline;'>
                                    <input type='hidden' name='order_id' value='" . $row['id_transaksi'] . "'>
                                    <button type='submit' name='cancel' class='cancel-button'><i class='fas fa-times'></i> Batal</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada order.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <form method="post" action="transaksi.php">
            <button type="submit" name="checkout"><i class="fas fa-check"></i> Checkout</button>
        </form>
        <a href="beranda.php" class="back-button"><i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var selected = document.querySelector('.select-selected');
            var items = document.querySelector('.select-items');

            selected.addEventListener('click', function () {
                this.classList.toggle('select-arrow-active');
                items.classList.toggle('select-hide');
            });

            var options = document.querySelectorAll('.product-option');
            options.forEach(function (option) {
                option.addEventListener('click', function () {
                    var id = this.getAttribute('data-id');
                    var name = this.getAttribute('data-name');
                    var harga = this.getAttribute('data-harga');

                    document.getElementById('id_produk').value = id;
                    document.getElementById('produk').value = name;
                    document.getElementById('harga').value = harga;

                    selected.textContent = name;
                    items.classList.add('select-hide');
                    selected.classList.remove('select-arrow-active');
                });
            });

            document.addEventListener('click', function (e) {
                if (!e.target.matches('.select-selected, .select-items *')) {
                    items.classList.add('select-hide');
                    selected.classList.remove('select-arrow-active');
                }
            });
        });
    </script>
</body>
</html>
