<?php $version = (ASTERION_DEBUG) ? rand() : date('mY');?>
<!DOCTYPE html>
<html lang="<?php echo Language::active(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="<?php echo ASTERION_APP_URL; ?>visual/img/favicon.ico" />

    <title><?php echo $title; ?></title>

    <link href="<?php echo ASTERION_APP_URL; ?>visual/css/stylesheets/admin.css?v=<?php echo $version; ?>" rel="stylesheet">
    <link href="<?php echo ASTERION_APP_URL; ?>helpers/fontawesome/css/solid.min.css?v=<?php echo $version; ?>" rel="stylesheet">
    <link href="<?php echo ASTERION_APP_URL; ?>helpers/fontawesome/css/fontawesome.min.css?v=<?php echo $version; ?>" rel="stylesheet">

    <script type="text/javascript" src="<?php echo url('navigation_admin/js', true); ?>"></script>
    <script type="text/javascript" src="<?php echo ASTERION_APP_URL; ?>helpers/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="<?php echo ASTERION_APP_URL; ?>helpers/jquery/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="<?php echo ASTERION_APP_URL; ?>helpers/jquery/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo ASTERION_APP_URL; ?>libjs/admin.js?v=<?php echo $version; ?>"></script>
    <script type="text/javascript" src="<?php echo ASTERION_BASE_URL; ?>libjs/public_admin.js?v=<?php echo $version; ?>"></script>

    <?php echo $head; ?>

</head>
<body>

    <div id="wrapper">
        <?php echo $content; ?>
    </div>

</body>
</html>