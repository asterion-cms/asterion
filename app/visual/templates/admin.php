<!DOCTYPE html>
<html lang="<?php echo Lang::active();?>">
<?php $version = (DEBUG) ? rand() : date('mY');?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="<?php echo APP_URL;?>visual/img/favicon.ico" />

	<title><?php echo $title;?></title>

    <link href="<?php echo APP_URL;?>visual/css/stylesheets/admin.css?v=<?php echo $version;?>" rel="stylesheet">

    <script type="text/javascript" src="<?php echo url('NavigationAdmin/js', true); ?>"></script>
    <script type="text/javascript" src="<?php echo APP_URL; ?>helpers/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="<?php echo APP_URL; ?>libjs/dist.js?v=<?php echo $version;?>"></script>

    <?php echo $header;?>

</head>
<body>

    <div id="wrapper">
        <?php echo $content;?>
    </div>

</body>
</html>