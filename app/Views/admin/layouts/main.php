<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $this->include('admin/layouts/header') ?>
</head>
<body class="sb-nav-fixed">
    <?= $this->include('admin/layouts/navbar') ?> <div id="layoutSidenav">
        <?= $this->include('admin/layouts/sidebar') ?> <div id="layoutSidenav_content">
            <main class="container-fluid px-4 mt-4">
                <?= $this->renderSection('content') ?>
            </main>
            <?= $this->include('admin/layouts/footer') ?>
        </div>
    </div>
</body>
</html>