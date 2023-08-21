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
        // print name of the table as the page's title
        $activityName = "?";
        if(isset($_POST["see-data"])) {
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

        if($_SERVER["REQUEST_METHOD"] == "POST") {
            if(empty($time_spent) || empty($day) || empty($activity_id)) {
                $message = "fill in the required fields";
            } else {
                try {

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
        <span>day:</span><input type="text" name="day" /><br>
        <span>activity id:</span><input type="text" name="activity_id" /><br>
        <input type="submit" value="Submit" />
        <?php echo $message . "<br>"; ?>
    </form>
    
    <br>
    <button><a href="activities.php">Back to activities page</a></button>
    <button><a href="index.php">Back to home page</a></button>
</body>
</html>