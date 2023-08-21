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
        if(isset($_POST["see-data"]) || isset($_POST["add-activity"])) {
            $activityName = $_POST["data-name"];
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
                    </tr>";
                    // <th colspan='3'>action</th>
                foreach($data as $key => $value) {
                    echo "<tr>
                            <td>".$value['id']."</td>
                            <td>".$value['time_spent']."</td>
                            <td>".$value['day']."</td>
                            <td>".$value['activity_id']."</td>
                        </tr>";
                }
                echo "</table>";
            }
        } catch(PDOException $e) {
            $sql . "<br>" . $e->getMessage();
        }


        // add a value to the activity table
        $message = "";
        $time_spent = isset($_POST["time_spent"]) ? $_POST["time_spent"] : "";
        $day = isset($_POST["day"]) ? $_POST["day"] : "";
        $activity_id = isset($_POST["activity_id"]) ? $_POST["activity_id"] : "";

        if(isset($_POST["add-activity"])) {
            if(empty($time_spent) || empty($day) || empty($activity_id)) { // *** verify if the int fields are of the int type ***
                $message = "fill in the required fields";
            } else {
                try {
                    $sql = $pdo->prepare("INSERT INTO `".$activityName."` (time_spent, day, activity_id) VALUES (?, ?, ?)");
                    $sql->execute(array($time_spent, $day, $activity_id));

                    $message = "value added successfully, refresh the page to see the changes";
                } catch(PDOException $e) {
                    echo $sql . "<br>" . $e->getMessage();
                }
            }
        }
    ?>

    <br>
    <form class="add-activity" action="" method="post">
        <b>add a value</b><br>
        <span>time spent:</span><input type="text" name="time_spent" /><br>
        <span>day:</span><input type="date" name="day" value="<?php echo date("Y-m-d"); ?>" /><br>
        <span>activity id:</span><input type="text" name="activity_id" /><br>
        <input type="hidden" name="data-name" value="<?php echo $activityName; ?>" />
        <input type="submit" name="add-activity" value="Submit" />
        <p class="error"><?php echo $message; ?></p><br>
    </form>
    
    <br>
    <button><a href="activities.php">Back to activities page</a></button>
    <button><a href="index.php">Back to home page</a></button>
</body>
</html>