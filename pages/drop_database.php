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
    <title>Drop database</title>
</head>
<body>
    <h2>Drop database</h2>

    <?php
        $message = "";
        if(isset($_POST["delete"])) {
            try {
                $sql = "DROP DATABASE `".DBNAME."`";
                $pdo->exec($sql);
                $message = "database dropped succesfully";
            } catch(PDOException $e) {
                echo $sql . "<br>" . $e->getMessage();
            }
        }

        if(isset($_POST["cancel"])) {
            header("Location: index.php"); /* ***** */
            exit();
        }
    ?>

    <p>are you sure you want to drop the dabase `<?php echo DBNAME; ?>`?</p>
    <form action="" method="post">
        <input type="submit" name="delete" value="ok" />
        <input type="submit" name="cancel" value="cancel" />
    </form>

    <br>
    <p style="margin: 0;"><?php echo $message; ?></p>

    <br>
    <button><a href="<?php echo INCLUDE_PATH; ?>home">Back to home page</a></button>
</body>
</html>