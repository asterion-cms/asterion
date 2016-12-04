<!DOCTYPE html>
<html lang="<?php echo Lang::active();?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="<?php echo APP_URL;?>visual/img/favicon.ico" />

	<title><?php echo $title;?></title>
    
    <link href="<?php echo APP_URL;?>visual/css/stylesheets/admin.css" rel="stylesheet">
    
    <script type="text/javascript" src="<?php echo url('NavigationAdmin/base-info', true); ?>"></script>
    <script type="text/javascript" src="<?php echo url('NavigationAdmin/js-translations', true); ?>"></script>
    <script type="text/javascript" src="<?php echo APP_URL; ?>libjs/jquery/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="<?php echo APP_URL; ?>libjs/jquery/jquery-ui-1.10.3.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo APP_URL; ?>libjs/jquery/jquery.form.js"></script>
    <script type="text/javascript" src="<?php echo APP_URL; ?>helpers/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="<?php echo APP_URL; ?>libjs/admin.js"></script>

    <?php echo $header;?>

</head>
<body>

    <div id="wrapper">
        <?php echo $content;?>
    </div>

</body>
</html>