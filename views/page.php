<html lang="en">
<head>
<title><?=$head_title?></title>
<link
    rel="icon"
    type="image/svg+xml"
    href="<?=$this->url("static/images/icon.svg")?>"
/>
<link
    rel="icon"
    type="image/png"
    href="<?=$this->url("static/images/icon.png")?>"
    sizes="128x128"
/>
<?=$styles?>
<?=$styles_code?>
<?=$scripts?>
<?=$scripts_code?>
<link
    href="<?=$this->url("static/css/style.css")?>"
    rel="stylesheet"
    type="text/css"
    media="all"
/>
</head>

<body>
    <div id="main-container">
        <div id="menu">
            <div id="logo">
                <img src="<?=$this->url("static/images/logo.svg")?>" />
            </div>

            <?=$this->menus["primary"]?>
        </div>
        <div id="content">
            <!--<h1><?=$title?></h1>-->

            <div class="container">
                <?=$content?>
            </div>
        </div>
    </div>
</body>

</html>