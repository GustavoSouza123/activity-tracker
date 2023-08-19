<?php
    require "connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css" />
    <title>Add activity</title>
</head>
<body>
    <?php
        $message = "";
        $name = isset($_POST["name"]) ? $_POST["name"] : "";

        if($_SERVER["REQUEST_METHOD"] == "POST") {
            if(empty($name)) {
                $message = "fill in the required fields";
            } else {
                try {
                    $sql = $pdo->prepare("INSERT INTO `activities` (name) VALUES (?)");
                    $sql->execute(array($name));

                    $sql = "CREATE TABLE IF NOT EXISTS `$name` (
                                id INT NOT NULL AUTO_INCREMENT,
                                time_spent INT NOT NULL,
                                day DATE NOT NULL,
                                activity_id INT NOT NULL,
                                PRIMARY KEY(id),
                                FOREIGN KEY(activity_id) REFERENCES activities(id)
                            )";
                    $pdo->exec($sql);

                    $message = "activity added successfully";
                } catch(PDOException $e) {
                    echo $sql . "<br>" . $e->getMessage();
                }
            }
        }
    ?>

    <h1>Add an activity</h1>
    <p>Enter the name of the activity you want to add</p>

    <form action="" method="post">
        <input type="text" name="name" />
        <input type="submit" value="Submit" />
    </form>

    <br>
    <?php echo $message . "<br>"; ?>
    <br>

    <button><a href="index.php">Back to home page</a></button>
</body>
</html>