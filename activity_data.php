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
        // set default timezone
        date_default_timezone_set("America/Sao_Paulo");

        // print name of the table as the page's title
        $activityName = "";
        if(isset($_COOKIE["tbname"])) {
            $activityName = $_COOKIE["tbname"];
        } else if(isset($_POST["see-data"])) {
            $activityName = $_POST["data-name"];
            setcookie("tbname", $activityName, time()+86400, "/");
        }
    ?>

    <h2>`<?php echo $activityName; ?>` data</h2>

    <?php
        // print all the data in the activity table
        try {
            $sql = $pdo->prepare("SELECT * FROM `".$activityName."`");
            $sql->execute();
            $data = $sql->fetchAll();

            if($data == null) {
                echo "no values found<br>";
            } else {
                echo "<table>
                    <tr>
                        <th>id</th>
                        <th>time_spent</th>
                        <th>day</th>
                        <th>activity_id</th>
                        <th colspan='2'>action</th>
                    </tr>";
                foreach($data as $key => $value) {
                    echo "<tr>
                            <td>".$value['id']."</td>
                            <td>".$value['time_spent']."</td>
                            <td>".$value['day']."</td>
                            <td>".$value['activity_id']."</td>
                            <td class='action edit' row_id='".$value['id']."' row_time='".$value['time_spent']."' row_day='".$value['day']."' row_activity='".$value['activity_id']."'>edit</td>
                            <td class='action delete' row_id='".$value['id']."'>delete</td>
                        </tr>";
                }
                echo "</table>";
            }
        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        // add a value to the activity table
        $addMessage = "";
        $time_spent = isset($_POST["time_spent"]) ? $_POST["time_spent"] : "";
        $day = isset($_POST["day"]) ? $_POST["day"] : "";
        $activity_id = isset($_POST["activity_id"]) ? $_POST["activity_id"] : "";

        if(isset($_POST["add-activity"])) {
            if(empty($time_spent) || empty($day) || empty($activity_id)) { // *** verify if the int fields are of the int type ***
                $addMessage = "fill in the required fields";
            } else {
                try {
                    $sql = $pdo->prepare("INSERT INTO `".$activityName."` (time_spent, day, activity_id) VALUES (?, ?, ?)");
                    $sql->execute(array($time_spent, $day, $activity_id));

                    header("Location: " . $_SERVER['PHP_SELF']); // refresh the page after sending the form
                } catch(PDOException $e) {
                    $addMessage = $sql . "<br>" . $e->getMessage();
                }
            }
        }

        // edit row
        $actionMessage = "";
        if(isset($_POST["edit"])) {
            if(empty($_POST["time"])) {
                $actionMessage = "fill in the required fields";
            } else {
                try {
                    $id = $_POST["data-id"];
                    $sql = $pdo->prepare("UPDATE ".$activityName." SET time_spent=?, day=?, activity_id=? WHERE id=?");
                    $sql->execute(array($_POST["time"], $_POST["day"], $_POST["activity_id"], $id));

                    header("Location: " . $_SERVER['PHP_SELF']); // refresh the page after sending the form
                } catch(PDOException $e) {
                    if($e->getCode() == "42S02") {
                        // $actionMessage = "the table `".$_POST["time"]."` has already been renamed"; ***TEMPORARY
                        $actionMessage = "error code 42S02, see the pdo exception on the 'edit row' code";
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
                $sql = $pdo->prepare("DELETE FROM ".$activityName." WHERE id=?");
                $sql->execute(array($id));

                header("Location: " . $_SERVER['PHP_SELF']); // refresh the page after sending the form
            } catch(PDOException $e) {
                $actionMessage = $sql . "<br>" . $e->getMessage();
            }
        }
    ?>

    <br>
    <form class="add-activity" action="" method="post">
        <b>add a value</b><br>
        <span>time spent:</span><input type="text" name="time_spent" /><br>
        <span>day:</span><input type="date" name="day" value="<?php echo date("Y-m-d"); ?>" /><br>
        <span>activity id:</span><input type="text" name="activity_id" /><br>
        <input type="submit" name="add-activity" value="ok" />
        <p class="error"><?php echo $addMessage; ?></p><br>
    </form>

    <br>
    <div class="action-window">
        <b>action window</b>
        <div class="edit">
            <form action="" method="post">
                <span>time spent:</span><input type="text" name="time" /><br>
                <span>day:</span><input type="date" name="day" /><br>
                <span>activity id:</span><input type="text" name="activity_id" /><br>
                <input type="hidden" name="data-id" />
                <input type="hidden" name="data-time" />
                <input type="hidden" name="data-day" />
                <input type="hidden" name="data-activity" />
                <input type="submit" name="edit" value="ok" />
                <input type="reset" value="cancel" /><br>
            </form>
        </div>
        <div class="delete">
            <p>are you sure you want to delete row <span></span> from `<?php echo $activityName; ?>` table with all its data?</p>
            <form action="" method="post">
                <input type="hidden" name="data-id" />
                <input type="submit" name="delete" value="ok" />
                <input type="reset" value="cancel" />
            </form>
        </div>
        <p class="error"><?php echo $actionMessage; ?></p>
    </div>
    
    <br>
    <button><a href="activities.php">Back to activities page</a></button>
    <button><a href="index.php">Back to home page</a></button>

    <script src="js/jquery.js"></script>
    <script src="js/activity.js"></script>
</body>
</html>