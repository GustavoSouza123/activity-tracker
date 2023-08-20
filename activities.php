<?php
    require "connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css" />
    <title>Activities</title>
</head>
<body>
    <h2>Activities</h2>

    <?php
        try {
            // print all the data in the activities table
            $sql = $pdo->prepare("SELECT * FROM `activities`");
            $sql->execute();
            $data = $sql->fetchAll();

            if($data == null) {
                echo "no values found<br>";
            } else {
                echo "<table>
                    <tr>
                        <th>id</th>
                        <th>name</th>
                        <th colspan='3'>action</th>
                    </tr>";
                foreach($data as $key => $value) {
                    echo "<tr>
                            <td>".$value['id']."</td>
                            <td>".$value['name']."</td>
                            <td class='action edit' row_id='".$value['id']."'>edit</td>
                            <td class='action delete' row_id='".$value['id']."'>delete</td>
                            <td class='action see'><a href='activity_data.php'>see data</a></td>
                        </tr>";
                }
                echo "</table>";
            }
        } catch(PDOException $e) {
            echo $sql . "<br>" . $e->getMessage();
        }

        // edit row
        $message = "";
        if(isset($_POST["edit"])) {
            if(empty($_POST["name"])) {
                $message = "fill in the required fields";
            } else {
                try {
                    $id = $_POST["data-id"];
                    $sql = $pdo->prepare("UPDATE `activities` SET name=? WHERE id=?");
                    $sql->execute(array($_POST["name"], $id));

                    $message = "activity edited successfully, refresh the page to see the changes";
                } catch(PDOException $e) {
                    echo $sql . "<br>" . $e->getMessage();
                }
            }
        }

        // delete row
        if(isset($_POST["delete"])) {
            try {
                // delete table with the name of the activity
                // make a confirmation screen
                $id = $_POST["data-id"];
                $sql = $pdo->prepare("DELETE FROM `activities` WHERE id=?");
                $sql->execute(array($id));

                $message = "activity deleted successfully, refresh the page to see the changes";
            } catch(PDOException $e) {
                echo $sql . "<br>" . $e->getMessage();
            }
        }
    ?>

    <br>
    <div class="action-window">
        <b>action window</b>
        <div class="edit">
            <form action="" method="post">
                <span>name:</span><input type="text" name="name" /><br>
                <input type="hidden" name="data-id" />
                <input type="submit" name="edit" value="ok" />
                <input type="reset" value="cancel" /><br>
            </form>
        </div>
        <div class="delete">
            <p>are you sure you want to delete row <span></span>?</p>
            <form action="" method="post">
                <input type="hidden" name="data-id" />
                <input type="submit" name="delete" value="ok" />
                <input type="reset" value="cancel" />
            </form>
        </div>
        <p class="error"><?php echo $message; ?></p>
    </div>

    <br>
    <button><a href="index.php">Back to home page</a></button>

    <script src="js/jquery.js"></script>
    <script src="js/script.js"></script>
</body>
</html>