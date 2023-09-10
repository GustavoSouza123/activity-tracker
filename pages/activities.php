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
    <title>Activities</title>
</head>
<body>
    <h2>Activities</h2>

    <?php
        // print all the data in the activities table
        try {
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
                        <th class='action' colspan='3'>action</th>
                    </tr>";
                foreach($data as $key => $value) {
                    echo "<tr>
                            <td>".$value['id']."</td>
                            <td>".$value['name']."</td>
                            <td class='action edit' row_id='".$value['id']."' row_name='".$value['name']."'>edit</td>
                            <td class='action delete' row_id='".$value['id']."' row_name='".$value['name']."'>delete</td>
                            <td class='action see'>
                                <form action='activity_data' method='post'>
                                    <input type='hidden' name='data-name' value='".$value['name']."' />
                                    <input class='action' type='submit' name='see-data' value='see data' />
                                </form>
                            </td>
                        </tr>";
                }
                echo "</table>";
            }
        } catch(PDOException $e) {
            echo $sql . "<br>" . $e->getMessage();
        }

        // add an activity to the activities table
        $addMessage = "";
        $name = isset($_POST["name"]) ? $_POST["name"] : "";

        if(isset($_POST["add-activity"])) {
            if(!isset($name)) {
                $addMessage = "fill in the required fields";
            } else {
                try {
                    $sql = $pdo->prepare("INSERT INTO `activities` (name) VALUES (?)");
                    $sql->execute(array($name));

                    $sql = "CREATE TABLE IF NOT EXISTS `$name` (
                                id INT NOT NULL AUTO_INCREMENT,
                                time_spent INT NOT NULL,
                                day DATE NOT NULL,
                                PRIMARY KEY(id)
                            )";
                    $pdo->exec($sql);

                    header("Location: activities"); // refresh the page after sending the form
                } catch(PDOException $e) {
                    $addMessage = $sql . "<br>" . $e->getMessage();
                }
            }
        }

        // edit row
        $actionMessage = "";
        if(isset($_POST["edit"])) {
            if(!isset($_POST["name"])) {
                $actionMessage = "fill in the required fields";
            } else {
                try {
                    $id = $_POST["data-id"];
                    $sql = $pdo->prepare("UPDATE `activities` SET name=? WHERE id=?");
                    $sql->execute(array($_POST["name"], $id));

                    $sql = "ALTER TABLE `".$_POST["data-name"]."` RENAME `".$_POST["name"]."`";
                    $pdo->exec($sql);

                    header("Location: activities"); // refresh the page after sending the form
                } catch(PDOException $e) {
                    if($e->getCode() == "42S02") {
                        $actionMessage = "the table `".$_POST["name"]."` has already been renamed";
                    } else {
                        $actionMessage = $sql . "<br>" . $e->getMessage();
                    }
                }
            }
        }

        // delete row
        if(isset($_POST["delete"])) {
            try {
                $id = $_POST["data-id"];
                $sql = $pdo->prepare("DELETE FROM `activities` WHERE id=?");
                $sql->execute(array($id));

                $sql = "DROP TABLE IF EXISTS `".$_POST["data-name"]."`";
                $pdo->exec($sql);

                header("Location: activities"); // refresh the page after sending the form
            } catch(PDOException $e) {
                $actionMessage = $sql . "<br>" . $e->getMessage();
            }
        }
    ?>

    <br>
    <form class="add-activity" action="" method="post">
        <b>add an activity</b>
        <p>enter the name of the activity you want to add</p>
        <input type="text" name="name" />
        <input type="submit" name="add-activity" value="ok" /><br>
        <p class="error"><?php echo $addMessage; ?></p><br>
    </form>

    <br>
    <div class="action-window">
        <b>action window</b>
        <div class="edit">
            <form action="" method="post">
                <span>name:</span><input type="text" name="name" /><br>
                <input type="hidden" name="data-id" />
                <input type="hidden" name="data-name" />
                <input type="submit" name="edit" value="ok" />
                <input type="reset" value="cancel" /><br>
            </form>
        </div>
        <div class="delete">
            <p>are you sure you want to delete row <span></span> from `activities` table with all its data?</p>
            <form action="" method="post">
                <input type="hidden" name="data-id" />
                <input type="hidden" name="data-name" />
                <input type="submit" name="delete" value="ok" />
                <input type="reset" value="cancel" />
            </form>
        </div>
        <p class="error"><?php echo $actionMessage; ?></p>
    </div>

    <br>
    <button><a href="<?php echo INCLUDE_PATH; ?>home">Back to home page</a></button>

    <script src="<?php echo INCLUDE_PATH; ?>js/jquery.js"></script>
    <script src="<?php echo INCLUDE_PATH; ?>js/activities.js"></script>
</body>
</html>