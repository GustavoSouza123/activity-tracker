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

        // data period
        $period = "alltime";
        if(isset($_COOKIE["period"])) {
            $period = $_COOKIE["period"];
        }
        if(isset($_GET["period"])) {
            $period = $_GET["period"];
            setcookie("period", $period, time()+86400*30, "/");
        }

        if($period == "alltime") {
            $dayPattern = "day LIKE '%'";
        } else if($period == "last7days") {
            $dayPattern = "day >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        } else if($period == "last14days") {
            $dayPattern = "day >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)";
        } else if($period == "last30days") {
            $dayPattern = "day >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } else if($period == "thismonth") {
            $dayPattern = "MONTH(day) = MONTH(CURDATE())";
        } else if($period == "lastmonth") {
            $dayPattern = "MONTH(day) = MONTH(CURDATE())-1";
        } else if($period == "last3months") {
            $dayPattern = "day >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)";
        } else if($period == "last6months") {
            $dayPattern = "day >= DATE_SUB(CURDATE(), INTERVAL 180 DAY)";
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
        <input type="submit" value="order">
    </form>

    <form class="show-form" action="" method="post">
        <span>show:</span>
        <input type="checkbox" name="show_id" id="id" <?php if($columnsToShow[0] == 1) echo "value='1'; checked"; else echo "value='0';" ?> />id
        <input type="checkbox" name="show_time" id="time" <?php if($columnsToShow[1] == 1) echo "value='1'; checked"; else echo "value='0';" ?> />time
        <input type="checkbox" name="show_day" id="day" <?php if($columnsToShow[2] == 1) echo "value='1'; checked"; else echo "value='0';" ?> />day
        <input type="submit" name="columns_to_show" value="show" />
    </form>

    <form action="" method="get">
        <span>period:</span>
        <select name="period" id="period">
            <option value="alltime" <?php if($period == "alltime") echo "selected"; ?>>all time</option>
            <option value="last7days" <?php if($period == "last7days") echo "selected"; ?>>last 7 days</option>
            <option value="last14days" <?php if($period == "last14days") echo "selected"; ?>>last 14 days</option>
            <option value="last30days" <?php if($period == "last30days") echo "selected"; ?>>last 30 days</option>
            <option value="thismonth" <?php if($period == "thismonth") echo "selected"; ?>>this month</option>
            <option value="lastmonth" <?php if($period == "lastmonth") echo "selected"; ?>>last month</option>
            <option value="last3months" <?php if($period == "last3months") echo "selected"; ?>>last 3 months</option>
            <option value="last6months" <?php if($period == "last6months") echo "selected"; ?>>last 6 months</option>
        </select>
        <input type="submit" value="show" />
    </form><br>

    <?php
        // print all the data in the activity table
        try {
            $sql = $pdo->prepare("SELECT * FROM `".$activityName."` WHERE ".$dayPattern." ORDER BY ".$field." ".$order);
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
                    $dayArr = explode("-", $value['day']);
                    $value['day'] = $dayArr[2] . "/" . $dayArr[1] . "/" . $dayArr[0];

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
        $sql = $pdo->prepare("SELECT SUM(time_spent) AS time_spent_sum FROM `".$activityName."` WHERE ".$dayPattern);
        $sql->execute();
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        $totalTimeSpent = $row['time_spent_sum'];

        // get the average of the time spent
        $sql = $pdo->prepare("SELECT AVG(time_spent) AS time_spent_avg FROM `".$activityName."` WHERE ".$dayPattern);
        $sql->execute();
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        $avgTimeSpent = $row['time_spent_avg'];

        // get the total days
        $sql = $pdo->prepare("SELECT COUNT(*) AS total_days FROM `".$activityName."` WHERE ".$dayPattern);
        $sql->execute();
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        $totalDays = $row['total_days'];
    ?>

    <br>
    <div class="stats">
        <div class="stats-header">
            <b>stats</b>
            <form action="" method="get">
                <select name="period" id="period">
                    <option value="alltime" <?php if($period == "alltime") echo "selected"; ?>>all time</option>
                    <option value="last7days" <?php if($period == "last7days") echo "selected"; ?>>last 7 days</option>
                    <option value="last14days" <?php if($period == "last14days") echo "selected"; ?>>last 14 days</option>
                    <option value="last30days" <?php if($period == "last30days") echo "selected"; ?>>last 30 days</option>
                    <option value="thismonth" <?php if($period == "thismonth") echo "selected"; ?>>this month</option>
                    <option value="lastmonth" <?php if($period == "lastmonth") echo "selected"; ?>>last month</option>
                    <option value="last3months" <?php if($period == "last3months") echo "selected"; ?>>last 3 months</option>
                    <option value="last6months" <?php if($period == "last6months") echo "selected"; ?>>last 6 months</option>
                </select>
                <input type="submit" value="show" />
            </form>
        </div>
        <span class="total">total: <?php echo round($totalTimeSpent/60, 2); ?> hrs</span><br>
        <span class="avg">average/day: <?php echo round($avgTimeSpent/60, 2); ?> hrs (<?php echo round($avgTimeSpent, 2); ?> mins)</span><br>
        <span class="days">total days: <?php echo $totalDays; ?></span>
    </div>

    <br>
    <form class="add-activity" action="success" method="post">
        <b>add a value</b><br>
        <input type="hidden" name="data-name" value="<?php echo $activityName; ?>" />
        <span>time spent:</span><input type="text" name="time_spent" /><br>
        <span>day:</span><input type="date" name="day" value="<?php echo date("Y-m-d"); ?>" /><br>
        <input type="submit" name="add-activity" value="ok" />
    </form>

    <br>
    <div class="action-window">
        <b>action window</b>
        <div class="edit">
            <form action="success" method="post">
                <span>time spent:</span><input type="text" name="time" /><br>
                <span>day:</span><input type="date" name="day" /><br>
                <input type="hidden" name="data-name" value="<?php echo $activityName; ?>" />
                <input type="hidden" name="data-id" />
                <input type="hidden" name="data-time" />
                <input type="hidden" name="data-day" />
                <input type="submit" name="edit" value="ok" />
                <input type="reset" value="cancel" /><br>
            </form>
        </div>
        <div class="delete">
            <p>are you sure you want to delete row <span></span> from `<?php echo $activityName; ?>` table with all its data?</p>
            <form action="success" method="post">
                <input type="hidden" name="data-name" value="<?php echo $activityName; ?>" />
                <input type="hidden" name="data-id" />
                <input type="submit" name="delete" value="ok" />
                <input type="reset" value="cancel" />
            </form>
        </div>
    </div>
    
    <br>
    <button><a href="<?php echo INCLUDE_PATH; ?>activities">Back to activities page</a></button>
    <button><a href="<?php echo INCLUDE_PATH; ?>home">Back to home page</a></button>

    <script src="<?php echo INCLUDE_PATH; ?>js/jquery.js"></script>
    <script src="<?php echo INCLUDE_PATH; ?>js/activity.js"></script>
</body>
</html>