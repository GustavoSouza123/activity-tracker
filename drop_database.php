<?php
    require "connection.php";
    $sql = "DROP DATABASE `".DBNAME."`";
    $pdo->exec($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css" />
    <title>Drop database</title>
</head>
<body>
    <h2>Drop database</h2>
    <button><a href="index.php">Back to home page</a></button>
</body>
</html>