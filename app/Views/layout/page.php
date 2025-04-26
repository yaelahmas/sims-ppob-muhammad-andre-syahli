<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo.png'); ?>">
    <title>SIMS PPOB - MUHAMMAD ANDRE SYAHLI</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #fff;
            font-family: 'Poppins', sans-serif;
        }

        .saldo-card {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
            border-radius: 20px;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        .saldo-card::after {
            content: "";
            position: absolute;
            right: 0;
            bottom: 0;
            width: 150px;
            height: 150px;
            background: url('pattern.png') no-repeat;
            background-size: cover;
            opacity: 0.3;
        }

        .navbar-brand img {
            margin-right: 8px;
            font-style: normal;
        }

        .active-link {
            color: #dc3545 !important;
            font-weight: bold;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= base_url('/'); ?>">
                <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo" width="30" height="30">
                <span class="fw-bold">SIMS PPOB</span>
            </a>
            <div>
                <a href="<?= base_url('topup'); ?>"
                    class="text-decoration-none mx-3 <?= (uri_string() == 'topup') ? 'active-link' : 'text-dark' ?>">Top Up</a>

                <a href="<?= base_url('transaction'); ?>"
                    class="text-decoration-none mx-3 <?= (uri_string() == 'transaction') ? 'active-link' : 'text-dark' ?>">Transaksi</a>

                <a href="<?= base_url('profile'); ?>"
                    class="text-decoration-none mx-3 <?= (uri_string() == 'profile') ? 'active-link' : 'text-dark' ?>">Akun</a>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container my-4">
        <?= $this->renderSection('content') ?>
    </div>

    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>

</body>

</html>