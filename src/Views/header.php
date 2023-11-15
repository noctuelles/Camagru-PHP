<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?= $title ?? 'Camagru'?></title>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link type="text/css" href="css/normalize.css" rel="stylesheet"/>
        <link type="text/css" href="css/main.css" rel="stylesheet"/>
        <link type="text/css" href="css/custom-field.css" rel="stylesheet"/>
        <link type="text/css" href="css/custom-button.css" rel="stylesheet"/>
        <link type="text/css" href="css/login.css" rel="stylesheet"/>

        <?php if ($script ?? ''): ?>
            <script defer type="application/javascript" src=<?= $script?>></script>
        <?php endif; ?>
    </head>
    <body>
        <main>