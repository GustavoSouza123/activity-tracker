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
        }
        if(isset($_POST["see-data"])) {
            $activityName = $_POST["data-name"];
            setcookie("tbname", $activityName, time()+86400*30, "/");
        }
    ?>

    <h2>`<?php echo $activityName; ?>` data</h2>

    <?php
        // data order
        $field = "id";
        if(isset($_COOKIE["field"])) {
            $field = $_COOKIE["field"];
        }
        if(isset($_GET["field"])) {
            $field = $_GET["field"];
            setcookie("field", $field, time()+86400*30, "/");
        }
        $order = "ASC";
        if(isset($_COOKIE["order"])) {
            $order = $_COOKIE["order"];
        }
        if(isset($_GET["order"])) {
            $order = strtoupper($_GET["order"]);
            setcookie("order", $order, time()+86400*30, "/");
        }

        // columns to show
        $columnsToShow = array(1, 1, 1);

        if(isset($_COOKIE["columnsToShow"])) {
            $columnsToShow = explode(" ", $_COOKIE["columnsToShow"]);
        }
        if(isset($_POST["columns_to_show"])) {
            $columnsToShow[0] = (isset($_POST["show_id"])) ? 1 : 0;
            $columnsToShow[1] = (isset($_POST["show_time"])) ? 1 : 0;
            $columnsToShow[2] = (isset($_POST["show_day"])) ? 1 : 0;
            setcookie("columnsToShow", implode(" ", $columnsToShow), time()+86400*30, "/");
        }
    ?>

    <form action="" method="get">
        <span>order by:</span>
        <select name="field" id="field">
            <option value="id" <?php if($field == "id") echo "selected"; ?>>id</option>
            <option value="time_spent" <?php if($field == "time_spent") echo "selected"; ?> >time_spent</option>
            <option value="day" <?php if($field == "day") echo "selected"; ?>>day</option>
        </select>
        <select name="order" id="order">
            <option value="asc" <?php if($order == "ASC") echo "selected"; ?>>asc</option>
            <option value="desc" <?php if($order == "DESC") echo "selected"; ?>>desc</option>
        </select>
        <input type="submit" name="orderby" value="order">
    </form>

    <form action="" method="post">
        <span>show:</span>
        <input type="checkbox" name="show_id" id="id" <?php if($columnsToShow[0] == 1) echo "value='1'; checked"; else echo "value='0';" ?> />id
        <input type="checkbox" name="show_time" id="time" <?php if($columnsToShow[1] == 1) echo "value='1'; checked"; else echo "value='0';" ?> />time
        <input type="checkbox" name="show_day" id="day" <?php if($columnsToShow[2] == 1) echo "value='1'; checked"; else echo "value='0';" ?> />day
        <input type="submit" name="columns_to_show" value="show" />
    </form><br>

    <?php
        // print all the data in the activity table
        try {
            $sql = $pdo->prepare("SELECT * FROM `".$activityName."` ORDER BY ".$field." ".$order);
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
                        <th class='action' colspan='2'>action</th>
                    </tr>";
                foreach($data as $key => $value) {
                    echo "<tr>
                            <td>".$value['id']."</td>
                            <td>".$value['time_spent']."</td>
                            <td>".$value['day']."</td>
                            <td class='action edit' row_id='".$value['id']."' row_time='".$value['time_spent']."' row_day='".$value['day']."'>edit</td>
                            <td class='action delete' row_id='".$value['id']."'>delete</td>
                        </tr>";
                }
                echo "</table>";
            }
        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        // get the total time spent
        $sql = $pdo->prepare("SELECT SUM(time_spent) AS time_spent_sum FROM `".$activityName."`");
        $sql->execute();
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        $totalTimeSpent = $row['time_spent_sum'];

        // get the average of the time spent
        $sql = $pdo->prepare("SELECT AVG(time_spent) AS time_spent_avg FROM `".$activityName."`");
        $sql->execute();
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        $avgTimeSpent = $row['time_spent_avg'];

        // get the total days
        $sql = $pdo->prepare("SELECT COUNT(*) AS total_days FROM `".$activityName."`");
        $sql->execute();
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        $totalDays = $row['total_days'];
    ?>

    <?php
        // add a value to the activity table
        $addMessage = "";
        $time_spent = isset($_POST["time_spent"]) ? $_POST["time_spent"] : "";
        $day = isset($_POST["day"]) ? $_POST["day"] : "";

        if(isset($_POST["add-activity"])) {
            if(!isset($time_spent) || !isset($day)) { // *** verify if the int fields are of the int type ***
                $addMessage = "fill in the required fields";
            } else {
                try {
                    $sql = $pdo->prepare("INSERT INTO `".$activityName."` (time_spent, day) VALUES (?, ?)");
                    $sql->execute(array($time_spent, $day));

                    header("Location: " . $_SERVER['PHP_SELF']); // refresh the page after sending the form
                } catch(PDOException $e) {
                    $addMessage = $sql . "<br>" . $e->getMessage();
                }
            }
        }

        // edit row
        $actionMessage = "";
        if(isset($_POST["edit"])) {
            if(!isset($_POST["time"])) {
                $actionMessage = "fill in the required fields";
            } else {
                try {
                    $id = $_POST["data-id"];
                    $sql = $pdo->prepare("UPDATE ".$activityName." SET time_spent=?, day=? WHERE id=?");
                    $sql->execute(array($_POST["time"], $_POST["day"], $id));

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
    ?><br>

    <div class="stats">
        <b>stats</b><br>
        <span class="total">total: <?php echo round($totalTimeSpent/60, 2); ?> hrs</span><br>
        <span class="avg">average/day: <?php echo round($avgTimeSpent/60, 2); ?> hrs (<?php echo round($avgTimeSpent, 2); ?> mins)</span><br>
        <span class="days">total days: <?php echo $totalDays; ?></span>
    </div>

    <br>
    <form class="add-activity" action="" method="post">
        <b>add a value</b><br>
        <span>time spent:</span><input type="text" name="time_spent" /><br>
        <span>day:</span><input type="date" name="day" value="<?php echo date("Y-m-d"); ?>" /><br>
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