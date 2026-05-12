<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$error = '';

if (!empty($_SESSION['admin_ok'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_verify_csrf($_POST['csrf'] ?? null)) {
        $error = 'Ungültige Anfrage.';
    } else {
        $pw = (string) ($_POST['password'] ?? '');
        if (admin_password_ok($ADMIN_CONFIG, $pw)) {
            $_SESSION['admin_ok'] = true;
            header('Location: index.php');
            exit;
        }
        $error = 'Login fehlgeschlagen.';
    }
}

$csrf = admin_csrf_token();
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin · Events · ina.YOGA</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
  <main class="admin-login">
    <h1>Events verwalten</h1>
    <?php if ($error !== '') : ?>
      <p class="admin-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form method="post" action="login.php" class="admin-form">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
      <label>
        Passwort
        <input type="password" name="password" required autocomplete="current-password">
      </label>
      <button type="submit">Anmelden</button>
    </form>
    <p class="admin-hint"><a href="../index.html">← Zur Website</a></p>
  </main>
</body>
</html>
