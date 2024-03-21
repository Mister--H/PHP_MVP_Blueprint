<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Wiz</title>
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('bootstrap-icons/font/bootstrap-icons.min.css') ?>">

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light ps-3 align-middle">
    <a class="navbar-brand text-primary d-flex fw-light" style="align-items:center;" href="#">
        <i class="bi bi-infinity fs-1"></i>
        <span class="">
        Data Wiz
</span>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="/dashboard"><i class="bi bi-app"></i> Home</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-box"></i> Product Entry
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkProductEntry">
                    <a class="dropdown-item" href="https://data.mr-h.net/dashboard/add-content"><i class="bi bi-plus-circle"></i> Add Content</a>
                    <a class="dropdown-item" href="https://data.mr-h.net/dashboard/woo/add-product"><i class="bi bi-bag-plus"></i> Add Product</a>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://data.mr-h.net/dashboard/woo/add-product"><i class="bi bi-shop"></i> Add Store</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://data.mr-h.net/dashboard/settings"><i class="bi bi-gear"></i> Settings</a>
            </li>
             <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i> Account
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkAccount">
                    <a class="dropdown-item" href="https://data.mr-h.net/dashboard/profile"><i class="bi bi-person"></i> Profile</a>
                    <a class="dropdown-item" href="https://data.mr-h.net/dashboard/users"><i class="bi bi-person-plus"></i> Add User</a>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://data.mr-h.net/dashboard/logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </li>
        </ul>
    </div>
</nav>


    <!-- Main content area -->
    <div class="container mt-4">
        <?php echo $content; // This will be replaced with the view content ?>
    </div>

    <script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
