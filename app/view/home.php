<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="<?php absPath("public/css/test.css"); ?>">
</head>

<body>
    <h2>I am in Home</h2>
    <form action="<?php url('post'); ?>" method="post">
        <?php Csrf::createToken(); ?>
        <input type="text" name="name">
        <input type="submit" value="Submit">
    </form>
</body>

</html>