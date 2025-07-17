<?php
// auth.php
include 'config.php';

$action = $_GET['action'] ?? 'login';

// Proses Form (jika ada data POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($action == 'submit_login') {
        $username = $_POST['username'];
        $password = md5($_POST['password']);

        $stmt = $conn->prepare("SELECT username, role FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            $error_message = "Username atau password salah!";
        }
        $stmt->close();
    } elseif ($action == 'submit_register') {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $role = 'user';

        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);

        if ($stmt->execute()) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            header("Location: user_dashboard.php");
            exit();
        } else {
            $error_message = "Gagal mendaftar. Username mungkin sudah ada.";
        }
        $stmt->close();
    }
}

// Proses Logout
if ($action == 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($action) ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex; justify-content: center; align-items: center; height: 100vh;
            background: linear-gradient(135deg, #16a085, #1abc9c);
        }
        .form-container {
            background: #fff; padding: 3rem; border-radius: 1rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; text-align: center;
        }
        .form-container h2 { font-size: 2.8rem; color: var(--green); margin-bottom: 2rem; }
        .form-container .input-box { width: 100%; margin-bottom: 1.5rem; border: var(--border); border-radius: .5rem; padding: 1.2rem; font-size: 1.6rem; }
        .form-container .btn { width: 100%; }
        .form-container .footer-note { margin-top: 1.5rem; font-size: 1.4rem; color: var(--light-color); }
        .form-container .footer-note a { color: var(--green); text-decoration: underline; }
        .error { color: red; margin-bottom: 1rem; font-size: 1.4rem; }
    </style>
</head>
<body>

<?php if ($action == 'login'): ?>
    <form class="form-container" action="auth.php?action=submit_login" method="post">
        <h2>Login</h2>
        <?php if(isset($error_message)) { echo "<p class='error'>$error_message</p>"; } ?>
        <input type="text" name="username" class="input-box" placeholder="Username" required>
        <input type="password" name="password" class="input-box" placeholder="Password" required>
        <button type="submit" class="btn">Masuk</button>
        <div class="footer-note">Belum punya akun? <a href="auth.php?action=register">Register di sini</a></div>
    </form>
<?php elseif ($action == 'register'): ?>
    <form class="form-container" action="auth.php?action=submit_register" method="post">
        <h2>Register</h2>
        <?php if(isset($error_message)) { echo "<p class='error'>$error_message</p>"; } ?>
        <input type="text" name="username" class="input-box" placeholder="Username" required>
        <input type="password" name="password" class="input-box" placeholder="Password" required>
        <button type="submit" class="btn">Register</button>
        <div class="footer-note">Sudah punya akun? <a href="auth.php?action=login">Login di sini</a></div>
    </form>
<?php endif; ?>

</body>
</html>