<?php
    require 'config/config.php';
    require 'config/connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>css/style.css" />
    <title>Activity Tracker</title>
</head>
<body>
    <div class="container">
        <h1>Activity Tracker</h1>
        <p>This system keeps track of how much time you spend daily on some activity, such as listening to content in another language</p>
        <button class="see-activities"><a href="<?php echo INCLUDE_PATH; ?>activities">See activities</a></button>
    </div>

    <br><br><br><br><br>
    <button class="drop-database"><a href="<?php echo INCLUDE_PATH; ?>drop_database">Drop database</a></button>

    <!-- <script src="<?php //echo INCLUDE_PATH; ?>js/jquery.js"></script> -->
    <!-- <script src="<?php //echo INCLUDE_PATH; ?>js/script.js"></script> -->
</body>
</html>