<?php
    require "connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/jquery.js"></script>
    <title>Activity Tracker</title>
</head>
<body>
    <div class="container">
        <h1>Activity Tracker</h1>
        <p>This system keeps track of how much time you spend daily on some activity, such as listening to content in another language</p>
        <button class="add-activity"><a href="add_activity.php">Add activity</a></button>
        <button class="see-activities"><a href="activities.php">See activities</a></button>
    </div>

    <br><br><br>
    <button class="drop-database"><a href="drop_database.php">Drop database</a></button>

    <script src="js/script.js"></script>
</body>
</html>