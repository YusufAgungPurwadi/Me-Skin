<?php
// admin.php
include 'config.php';

// Proteksi halaman, hanya untuk admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: auth.php?action=login");
    exit();
}

$action = $_GET['action'] ?? 'list'; // Default action is 'list'
$order_id = $_GET['id'] ?? null;

// PROSES FORM (POST REQUESTS)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();
    try {
        if ($action == 'save_new') { // Simpan pesanan baru dari form 'add'
            $customer_name = $_POST['customer_name'];
            $medicine_id = $_POST['medicine_id'];
            $quantity = $_POST['quantity'];
            $order_date = $_POST['order_date'];
            $status = 'Completed';

            $stmt_price = $conn->prepare("SELECT price FROM medicines WHERE id = ?");
            $stmt_price->bind_param("i", $medicine_id);
            $stmt_price->execute();
            $price = $stmt_price->get_result()->fetch_assoc()['price'] ?? 0;
            $total_price = $price * $quantity;

            $stmt_order = $conn->prepare("INSERT INTO orders (customer_name, total_price, order_date, status) VALUES (?, ?, ?, ?)");
            $stmt_order->bind_param("sdss", $customer_name, $total_price, $order_date, $status);
            $stmt_order->execute();
            $new_order_id = $conn->insert_id;

            $stmt_details = $conn->prepare("INSERT INTO order_details (order_id, medicine_id, quantity, price_per_item) VALUES (?, ?, ?, ?)");
            $stmt_details->bind_param("iiid", $new_order_id, $medicine_id, $quantity, $price);
            $stmt_details->execute();
        } 
        elseif ($action == 'update' && $order_id) { // Update pesanan dari form 'edit'
            // 1. Update data utama
            $customer_name = $_POST['customer_name'];
            $order_date = $_POST['order_date'];
            $status = $_POST['status'];
            $stmt_update_order = $conn->prepare("UPDATE orders SET customer_name = ?, order_date = ?, status = ? WHERE id = ?");
            $stmt_update_order->bind_param("sssi", $customer_name, $order_date, $status, $order_id);
            $stmt_update_order->execute();

            // 2. Hapus item yang dicentang
            if (!empty($_POST['remove'])) {
                $remove_ids = $_POST['remove'];
                $placeholders = implode(',', array_fill(0, count($remove_ids), '?'));
                $stmt_delete = $conn->prepare("DELETE FROM order_details WHERE id IN ($placeholders)");
                $stmt_delete->bind_param(str_repeat('i', count($remove_ids)), ...$remove_ids);
                $stmt_delete->execute();
            }

            // 3. Update jumlah item
            if (!empty($_POST['quantities'])) {
                $stmt_update_qty = $conn->prepare("UPDATE order_details SET quantity = ? WHERE id = ?");
                foreach ($_POST['quantities'] as $detail_id => $quantity) {
                    if (is_numeric($quantity) && $quantity > 0) {
                        $stmt_update_qty->bind_param("ii", $quantity, $detail_id);
                        $stmt_update_qty->execute();
                    }
                }
            }
            
            // 4. Tambah item baru
            if (!empty($_POST['new_medicine_id']) && !empty($_POST['new_quantity']) && $_POST['new_quantity'] > 0) {
                $new_medicine_id = $_POST['new_medicine_id'];
                $new_quantity = $_POST['new_quantity'];
                $stmt_price = $conn->prepare("SELECT price FROM medicines WHERE id = ?");
                $stmt_price->bind_param("i", $new_medicine_id);
                $stmt_price->execute();
                $price_per_item = $stmt_price->get_result()->fetch_assoc()['price'];
                $stmt_add_item = $conn->prepare("INSERT INTO order_details (order_id, medicine_id, quantity, price_per_item) VALUES (?, ?, ?, ?)");
                $stmt_add_item->bind_param("iiid", $order_id, $new_medicine_id, $new_quantity, $price_per_item);
                $stmt_add_item->execute();
            }

            // 5. Hitung ulang total harga
            $stmt_recalculate = $conn->prepare("SELECT SUM(quantity * price_per_item) as new_total FROM order_details WHERE order_id = ?");
            $stmt_recalculate->bind_param("i", $order_id);
            $stmt_recalculate->execute();
            $new_total = $stmt_recalculate->get_result()->fetch_assoc()['new_total'] ?? 0;
            $stmt_update_total = $conn->prepare("UPDATE orders SET total_price = ? WHERE id = ?");
            $stmt_update_total->bind_param("di", $new_total, $order_id);
            $stmt_update_total->execute();
        }
        
        $conn->commit();
        header("Location: admin.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Error: Gagal memproses data. " . $e->getMessage());
    }
}

// PROSES DELETE (GET REQUEST)
if ($action == 'delete' && $order_id) {
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Error saat menghapus data: " . $conn->error;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Daftar Pesanan</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php // BAGIAN VIEW (HTML) ?>

<?php if ($action == 'list'): ?>
<section class="book">
    <div class="row">
        <div style="width:100%">
            <h3 class="heading">Daftar <span>Pesanan</span></h3>
            <div style="margin-bottom: 2rem;">
                <a href="admin.php?action=add" class="btn">Tambah Pesanan <span class="fas fa-plus"></span></a>
                <a href="index.php" class="btn" style="margin-left: 1rem;">Kembali ke Home <span class="fas fa-home"></span></a>
            </div>

            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse: collapse; font-size: 1.4rem;">
                    <thead style="background-color: var(--green); color: white;">
                        <tr>
                            <th style="padding: 1rem; border: var(--border);">ID</th>
                            <th style="padding: 1rem; border: var(--border);">Pelanggan</th>
                            <th style="padding: 1rem; border: var(--border);">Produk</th>
                            <th style="padding: 1rem; border: var(--border);">Total</th>
                            <th style="padding: 1rem; border: var(--border);">Tanggal</th>
                            <th style="padding: 1rem; border: var(--border);">Status</th>
                            <th style="padding: 1rem; border: var(--border);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT o.*, GROUP_CONCAT(m.name SEPARATOR ', ') as item_list FROM orders o LEFT JOIN order_details od ON o.id = od.order_id LEFT JOIN medicines m ON od.medicine_id = m.id GROUP BY o.id ORDER BY o.order_date DESC";
                        $result = $conn->query($sql);
                        if ($result && $result->num_rows > 0):
                            while($row = $result->fetch_assoc()): ?>
                            <tr style="text-align: center;">
                                <td style="padding: 1rem; border: var(--border);"><?= $row['id'] ?></td>
                                <td style="padding: 1rem; border: var(--border);"><?= htmlspecialchars($row['customer_name']) ?></td>
                                <td style="padding: 1rem; border: var(--border); text-align: left;"><?= htmlspecialchars($row['item_list'] ?? 'Tidak ada detail') ?></td>
                                <td style="padding: 1rem; border: var(--border);">Rp<?= number_format($row['total_price'], 0, ',', '.') ?></td>
                                <td style="padding: 1rem; border: var(--border);"><?= htmlspecialchars($row['order_date'] ?? 'N/A') ?></td>
                                <td style="padding: 1rem; border: var(--border);"><?= htmlspecialchars($row['status'] ?? 'N/A') ?></td>
                                <td style="padding: 1rem; border: var(--border);">
                                    <a href="admin.php?action=edit&id=<?= $row['id'] ?>" class="btn"><i class="fas fa-edit"></i></a>
                                    <a href="admin.php?action=delete&id=<?= $row['id'] ?>" class="btn" onclick="return confirm('Yakin ingin hapus?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endwhile;
                        else: ?>
                            <tr><td colspan="7" style="text-align:center; padding: 2rem;">Belum ada pesanan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php elseif ($action == 'add'): ?>
<section class="book">
    <div class="row">
        <form action="admin.php?action=save_new" method="post">
            <h3 class="heading">Tambah <span>Pesanan</span></h3>
            <input type="text" name="customer_name" placeholder="Nama Pelanggan" class="box" required>
            <select name="medicine_id" class="box" required>
                <option value="">-- Pilih Obat --</option>
                <?php
                $medicines_result = $conn->query("SELECT id, name, price FROM medicines");
                while ($med = $medicines_result->fetch_assoc()): ?>
                    <option value="<?= $med['id'] ?>"><?= htmlspecialchars($med['name']) ?> (Rp<?= number_format($med['price']) ?>)</option>
                <?php endwhile; ?>
            </select>
            <input type="number" name="quantity" placeholder="Jumlah" class="box" min="1" required>
            <input type="date" name="order_date" class="box" required value="<?= date('Y-m-d') ?>">
            <input type="submit" value="Tambah Pesanan" class="btn">
            <a href="admin.php" class="btn" style="background-color: #777; color: white;">Batal</a>
        </form>
    </div>
</section>

<?php elseif ($action == 'edit' && $order_id): 
    // Ambil data untuk form edit
    $order = $conn->query("SELECT * FROM orders WHERE id = $order_id")->fetch_assoc();
    if (!$order) die("Pesanan tidak ditemukan.");
    
    $result_details = $conn->query("SELECT od.id, od.quantity, m.name as medicine_name FROM order_details od JOIN medicines m ON od.medicine_id = m.id WHERE od.order_id = $order_id");
    $order_details = [];
    while ($row = $result_details->fetch_assoc()) { $order_details[] = $row; }
    
    $all_medicines = $conn->query("SELECT id, name FROM medicines ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
?>
<section class="book">
    <div class="row">
        <form action="admin.php?action=update&id=<?= $order_id ?>" method="post">
            <h3 class="heading">Edit <span>Pesanan #<?= $order_id ?></span></h3>
            
            <input type="text" name="customer_name" class="box" value="<?= htmlspecialchars($order['customer_name']) ?>" required>
            <input type="date" name="order_date" class="box" value="<?= htmlspecialchars($order['order_date']) ?>" required>
            <select name="status" class="box">
                <option value="Completed" <?= $order['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Cancelled" <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>

            <div style="margin-top: 3rem; border-top: 1px solid #ccc; padding-top: 2rem;">
                <h3 class="heading">Detail <span>Barang</span></h3>
                <?php foreach ($order_details as $item): ?>
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <span style="flex-grow: 1; font-size: 1.6rem;"><?= htmlspecialchars($item['medicine_name']) ?></span>
                        <label style="font-size: 1.6rem;">Jumlah:</label>
                        <input type="number" name="quantities[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="1" class="box" style="width: 80px; margin: 0;">
                        <label style="font-size: 1.6rem;"><input type="checkbox" name="remove[]" value="<?= $item['id'] ?>"> Hapus</label>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top: 3rem; border-top: 1px solid #ccc; padding-top: 2rem;">
                <h3 class="heading">Tambah <span>Item Baru</span></h3>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <select name="new_medicine_id" class="box" style="flex-grow: 1; margin: 0;">
                        <option value="">-- Pilih Obat --</option>
                        <?php foreach ($all_medicines as $med): ?>
                            <option value="<?= $med['id'] ?>"><?= htmlspecialchars($med['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="new_quantity" placeholder="Jumlah" min="1" class="box" style="width: 120px; margin: 0;">
                </div>
            </div>

            <input type="submit" value="Simpan Perubahan" class="btn" style="width: 100%; margin-top: 3rem;">
            <a href="admin.php" class="btn" style="width: 100%; margin-top: 1rem; background-color: #777; color: white;">Batal</a>
        </form>
    </div>
</section>
<?php endif; ?>

</body>
</html>
<?php $conn->close(); ?>