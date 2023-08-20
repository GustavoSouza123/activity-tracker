<?php
    require "connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css" />
    <title>Activity data</title>
</head>
<body>
    <?php
        $activityName = "?";
        if(isset($_POST["see-data"])) {
            $activityName = $_POST["data-name"];
        }
    ?>

    <h2><?php echo $activityName; ?> data</h2>

    <button><a href="activities.php">Back to activities page</a></button>
    <button><a href="index.php">Back to home page</a></button>
</body>
</html>