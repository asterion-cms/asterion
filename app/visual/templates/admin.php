<!DOCTYPE html>
<html lang="<?php echo Lang::active();?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="<?php echo APP_URL;?>visual/img/favicon.ico" />

	<title><?php echo $title;?></title>

    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
    <link href="<?php echo APP_URL;?>visual/css/stylesheets/admin.css?v=<?php echo rand();?>" rel="stylesheet">

    <script type="text/javascript" src="<?php echo url('NavigationAdmin/base-info', true); ?>"></script>
    <script type="text/javascript" src="<?php echo url('NavigationAdmin/js-translations', true); ?>"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo APP_URL; ?>helpers/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="<?php echo APP_URL; ?>libjs/admin.js?v=<?php echo rand();?>"></script>

    <?php echo $header;?>

</head>
<body>

    <div id="wrapper">
        <?php echo $content;?>
    </div>

</body>
</html>