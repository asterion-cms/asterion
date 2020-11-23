<!DOCTYPE html>
<html lang="<?php echo Language::active();?>">
<head>

    <meta charset="utf-8">
    <meta name="description" content="<?php echo $metaDescription;?>"/>
    <meta name="keywords" content="<?php echo $metaKeywords;?>"/>
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />

    <meta property="og:title" content="<?php echo $title;?>" />
    <meta property="og:description" content="<?php echo $metaDescription;?>" />
    <meta property="og:image" content="<?php echo $metaImage;?>" />

    <link rel="shortcut icon" href="<?php echo ASTERION_BASE_URL;?>visual/img/favicon.ico"/>
    <link rel="canonical" href="<?php echo $metaUrl;?>" />

    <title><?php echo $title;?></title>

    <link href="<?php echo ASTERION_BASE_URL;?>visual/css/stylesheets/public.css" rel="stylesheet" type="text/css" />

    <script type="text/javascript" src="<?php echo ASTERION_APP_URL; ?>libjs/jquery/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="<?php echo ASTERION_BASE_URL; ?>libjs/public.js"></script>

    <?php echo $head;?>

</head>
<body>
    <div id="bodyFrame">
        <?php echo $content;?>
    </div>
</body>
</html>