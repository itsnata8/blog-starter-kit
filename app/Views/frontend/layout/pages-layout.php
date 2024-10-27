<!DOCTYPE html>

<!--
 // WEBSITE: https://themefisher.com
 // TWITTER: https://twitter.com/themefisher
 // FACEBOOK: https://www.facebook.com/themefisher
 // GITHUB: https://github.com/themefisher/
-->

<html lang="en-us">

<head>
    <meta charset="utf-8">
    <title><?= isset($pageTitle) ? $pageTitle : 'New Page Title'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>">
    <?= $this->renderSection('page_meta'); ?>
    <link
        rel="icon"
        type="image/png"
        sizes="16x16"
        href="<?= get_settings()->blog_favicon ? '/images/blog/' . get_settings()->blog_favicon : '/images/blog/default-favicon.png' ?>" />

    <!-- theme meta -->
    <meta name="theme-name" content="reporter" />

    <!-- # Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Neuton:wght@700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- # CSS Plugins -->
    <link rel="stylesheet" href="/frontend/plugins/bootstrap/bootstrap.min.css">

    <!-- # Main Style Sheet -->
    <link rel="stylesheet" href="/frontend/css/style.css">
    <?= $this->renderSection('stylesheets'); ?>
</head>

<body>

    <?php include('inc/header.php') ?>

    <main>
        <section class="section">
            <div class="container">
                <?= $this->renderSection('content'); ?>
            </div>
        </section>
    </main>

    <?php include('inc/footer.php') ?>


    <!-- # JS Plugins -->
    <script src="/frontend/plugins/jquery/jquery.min.js"></script>
    <script src="/frontend/plugins/bootstrap/bootstrap.min.js"></script>

    <!-- Main Script -->
    <script src="/frontend/js/script.js"></script>
    <?= $this->renderSection('scripts'); ?>

</body>

</html>