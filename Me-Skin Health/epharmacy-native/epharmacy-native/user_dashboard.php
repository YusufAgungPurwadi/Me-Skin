<?php
// user_dashboard.php
include 'config.php';

// Proteksi halaman, hanya untuk user
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header("Location: auth.php?action=login");
    exit();
}

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$view = $_GET['view'] ?? 'products'; // products, cart, checkout_confirmation, success
$action = $_POST['action'] ?? null;
$message = '';
$ongkos_kirim = 19000; // Definisikan ongkos kirim di sini

// PROSES SEMUA AKSI POST (KERANJANG & CHECKOUT)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $action) {
    $medicine_id = $_POST['medicine_id'] ?? null;

    switch ($action) {
        case 'add_to_cart':
            $quantity = $_POST['quantity'];
            $name = $_POST['medicine_name'];
            $price = $_POST['price'];
            if (isset($_SESSION['cart'][$medicine_id])) {
                $_SESSION['cart'][$medicine_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$medicine_id] = ['name' => $name, 'price' => $price, 'quantity' => $quantity];
            }
            $message = 'Obat berhasil ditambahkan ke keranjang!';
            $view = 'products'; // Tetap di halaman produk
            break;

        case 'update_cart':
            $quantity = $_POST['quantity'];
            if ($quantity > 0) {
                $_SESSION['cart'][$medicine_id]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$medicine_id]);
            }
            header('Location: user_dashboard.php?view=cart');
            exit();

        case 'remove_from_cart':
            unset($_SESSION['cart'][$medicine_id]);
            header('Location: user_dashboard.php?view=cart');
            exit();

        // ### PERUBAHAN LOGIKA CHECKOUT ###
        // Logika ini sekarang untuk memproses checkout FINAL setelah konfirmasi
        case 'process_final_checkout': 
            if (empty($_SESSION['cart'])) {
                header('Location: user_dashboard.php?view=products');
                exit();
            }
            
            $cart = $_SESSION['cart'];
            $customer_name = $_SESSION['username'];
            $order_date = date('Y-m-d');
            
            // Hitung subtotal dari item di keranjang
            $subtotal = array_reduce($cart, fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);
            // Total harga adalah subtotal + ongkos kirim
            $grand_total = $subtotal + $ongkos_kirim;

            $conn->begin_transaction();
            try {
                // Simpan pesanan dengan total harga yang sudah termasuk ongkir
                $stmt_order = $conn->prepare("INSERT INTO orders (customer_name, total_price, order_date) VALUES (?, ?, ?)");
                $stmt_order->bind_param("sds", $customer_name, $grand_total, $order_date);
                $stmt_order->execute();
                $order_id = $conn->insert_id;

                // Simpan detail pesanan
                $stmt_details = $conn->prepare("INSERT INTO order_details (order_id, medicine_id, quantity, price_per_item) VALUES (?, ?, ?, ?)");
                foreach ($cart as $id => $item) {
                    $stmt_details->bind_param("iiid", $order_id, $id, $item['quantity'], $item['price']);
                    $stmt_details->execute();
                }

                $conn->commit();
                unset($_SESSION['cart']);
                header('Location: user_dashboard.php?view=success');
                exit();

            } catch (Exception $e) {
                $conn->rollback();
                die("Checkout gagal: " . $e->getMessage());
            }
            break;
    }
}

$cart_count = count($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="style.css">
    <style>
        .checkout-summary {
            font-size: 1.6rem;
            color: var(--black);
            margin-top: 2rem;
            border-top: var(--border);
            padding-top: 1.5rem;
        }
        .checkout-summary div {
            display: flex;
            justify-content: space-between;
            padding: .5rem 0;
        }
        .checkout-summary .total {
            font-weight: bold;
            font-size: 1.8rem;
            color: var(--green);
            border-top: 1px solid #ccc;
            padding-top: 1rem;
            margin-top: 1rem;
        }
        .item-list p {
            font-size: 1.5rem;
            padding: .5rem 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>

<header class="header">
    <a href="#" class="logo"><i class="fas fa-heartbeat"> E-Pharmacy</i></a>
    <nav class="navbar">
        <a href="user_dashboard.php?view=products">Produk</a>
        <a href="user_dashboard.php?view=cart" class="btn"><i class="fas fa-shopping-cart"></i> Keranjang (<?= $cart_count ?>)</a>
        <a href="auth.php?action=logout">Logout</a>
    </nav>
</header>

<?php // BAGIAN VIEW (HTML) ?>

<?php if ($view == 'products'): ?>
<section class="services" style="padding-top: 10rem;">
    <h1 class="heading">Daftar <span>Obat</span></h1>
    <?php if ($message): ?>
        <p style="text-align:center; background: #d4edda; color: #155724; padding: 1rem; border-radius: .5rem; font-size: 1.6rem; margin-bottom:2rem;"><?= $message ?></p>
    <?php endif; ?>
    <div class="box-container">
        <?php 
        $result = $conn->query("SELECT * FROM medicines");
        while($row = $result->fetch_assoc()): ?>
        <div class="box">
            <i class="fas fa-capsules"></i>
            <h3><?= htmlspecialchars($row['name']) ?></h3>
            <p><?= htmlspecialchars($row['description']) ?></p>
            <form action="user_dashboard.php" method="post">
                <input type="hidden" name="action" value="add_to_cart">
                <input type="hidden" name="medicine_id" value="<?= $row['id'] ?>">
                <input type="hidden" name="medicine_name" value="<?= htmlspecialchars($row['name']) ?>">
                <input type="hidden" name="price" value="<?= $row['price'] ?>">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1rem;">
                    <input type="number" name="quantity" value="1" min="1" style="width: 60px; padding: .5rem; font-size: 1.6rem;">
                    <button type="submit" class="btn">Tambah</button>
                </div>
                <p style="font-size: 1.6rem; margin-top: 1rem; text-align:right; font-weight:bold;">Rp<?= number_format($row['price'], 0, ',', '.') ?></p>
            </form>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<?php elseif ($view == 'cart'): ?>
<section class="book" style="padding: 10rem 7%;">
    <h1 class="heading">Keranjang <span>Belanja</span></h1>
    <div class="row">
        <?php $cart_items = $_SESSION['cart']; if (empty($cart_items)): ?>
            <p style="text-align:center; font-size: 2rem; width:100%;">Keranjang Anda kosong.</p>
        <?php else: $grand_total = 0; ?>
            <div style="overflow-x:auto; width:100%;">
                <table style="width:100%; border-collapse: collapse; font-size: 1.4rem;">
                    <thead style="background-color: var(--green); color: white;">
                        <tr>
                            <th style="padding: 1rem; border: var(--border);">Produk</th><th style="padding: 1rem; border: var(--border);">Harga</th>
                            <th style="padding: 1rem; border: var(--border);">Jumlah</th><th style="padding: 1rem; border: var(--border);">Subtotal</th>
                            <th style="padding: 1rem; border: var(--border);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $id => $item): 
                            $subtotal = $item['price'] * $item['quantity']; $grand_total += $subtotal; ?>
                            <tr style="text-align: center;">
                                <td style="padding: 1rem; border: var(--border);"><?= htmlspecialchars($item['name']) ?></td>
                                <td style="padding: 1rem; border: var(--border);">Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                                <td style="padding: 1rem; border: var(--border);">
                                    <form action="user_dashboard.php" method="post" style="display:inline-flex; align-items:center;">
                                        <input type="hidden" name="action" value="update_cart"><input type="hidden" name="medicine_id" value="<?= $id ?>">
                                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" style="width: 60px; padding: .5rem; font-size: 1.6rem;">
                                        <button type="submit" class="btn" style="margin-left: 1rem; padding: .5rem 1rem;"><i class="fas fa-sync-alt"></i></button>
                                    </form>
                                </td>
                                <td style="padding: 1rem; border: var(--border);">Rp<?= number_format($subtotal, 0, ',', '.') ?></td>
                                <td style="padding: 1rem; border: var(--border);">
                                    <form action="user_dashboard.php" method="post" style="display:inline;">
                                        <input type="hidden" name="action" value="remove_from_cart"><input type="hidden" name="medicine_id" value="<?= $id ?>">
                                        <button type="submit" class="btn" style="background: #e74c3c;"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right; padding: 1.5rem; font-size: 1.8rem; font-weight: bold;">Total</td>
                            <td style="padding: 1.5rem; font-size: 1.8rem; font-weight: bold; text-align: center;">Rp<?= number_format($grand_total, 0, ',', '.') ?></td><td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div style="text-align: right; margin-top: 2rem; width:100%;">
                <a href="user_dashboard.php?view=checkout_confirmation" class="btn" style="font-size: 1.8rem;">Lanjut ke Pembayaran <i class="fas fa-arrow-right"></i></a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php elseif ($view == 'checkout_confirmation'): ?>
<section class="book" style="padding: 10rem 7%;">
    <div class="row" style="justify-content: center;">
        <div style="flex:1 1 50rem; background: #fff; border:var(--border); box-shadow: var(--box-shadow); padding: 2rem; border-radius: .5rem;">
            <h3 class="heading">Konfirmasi <span>Pesanan</span></h3>
            <?php
            $cart_items = $_SESSION['cart'];
            if (empty($cart_items)) {
                echo "<p>Keranjang Anda kosong.</p>";
            } else {
                $subtotal = 0;
            ?>
                <div class="item-list">
                    <h4>Ringkasan Belanja</h4>
                    <?php foreach ($cart_items as $item): 
                        $item_subtotal = $item['price'] * $item['quantity'];
                        $subtotal += $item_subtotal;
                    ?>
                        <p>
                            <span><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</span>
                            <span>Rp<?= number_format($item_subtotal, 0, ',', '.') ?></span>
                        </p>
                    <?php endforeach; ?>
                </div>

                <div class="checkout-summary">
                    <div>
                        <span>Subtotal</span>
                        <span>Rp<?= number_format($subtotal, 0, ',', '.') ?></span>
                    </div>
                    <div>
                        <span>Ongkos Kirim</span>
                        <span>Rp<?= number_format($ongkos_kirim, 0, ',', '.') ?></span>
                    </div>
                    <div class="total">
                        <span>Total Pembayaran</span>
                        <span>Rp<?= number_format($subtotal + $ongkos_kirim, 0, ',', '.') ?></span>
                    </div>
                </div>

                <form action="user_dashboard.php" method="post" style="margin-top: 2rem; text-align: right;">
                    <input type="hidden" name="action" value="process_final_checkout">
                    <button type="submit" class="btn" style="font-size: 1.7rem;">Konfirmasi & Bayar</button>
                </form>
            <?php } ?>
        </div>
    </div>
</section>

<?php elseif ($view == 'success'): ?>
<div style="text-align: center; padding: 15rem 2rem;">
    <h1 class="heading">Terima Kasih!</h1>
    <p style="font-size: 2rem;">Pesanan Anda telah berhasil diproses.</p>
    <a href="user_dashboard.php?view=products" class="btn" style="margin-top: 2rem;">Kembali Berbelanja</a>
</div>
<?php endif; ?>

</body>
</html>
<?php $conn->close(); ?>