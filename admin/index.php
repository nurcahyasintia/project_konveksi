<?php if ($_SESSION['role'] === 'Admin'): ?>

    <a href="admin/index.php" class="btn btn-primary">
        Halaman Admin
    </a>

<?php endif; ?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Halaman Admin</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body>

<div class="container mt-5">

    <div class="alert alert-success">

        <h4>Halaman Admin</h4>

        <p class="mb-0">
            Selamat datang,
            <?= htmlspecialchars($_SESSION['nama']); ?>.
        </p>

    </div>

    <a href="../index.php" class="btn btn-primary">
        Kembali ke Dashboard
    </a>

    <a href="../auth/logout.php" class="btn btn-danger">
        Logout
    </a>

</div>

</body>

</html>