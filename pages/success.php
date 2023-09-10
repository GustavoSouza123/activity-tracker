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
    <title>Success page</title>
</head>
<body>
    <?php
        // get the name of the table
        $activityName = "";
        if(isset($_POST["data-name"])) {
            $activityName = $_POST["data-name"];
        }

        // add a value to the activity table
        $actionMessage = "";
        $time_spent = isset($_POST["time_spent"]) ? $_POST["time_spent"] : "";
        $day = isset($_POST["day"]) ? $_POST["day"] : "";

        if(isset($_POST["add-activity"])) {
            if(!isset($time_spent) || !isset($day) || $time_spent == "" || $day == "") { // *** verify if the int fields are of the int type ***
                $actionMessage = "fill in the required fields";
            } else {
                try {
                    $sql = $pdo->prepare("INSERT INTO `".$activityName."` (time_spent, day) VALUES (?, ?)");
                    $sql->execute(array($time_spent, $day));

                    // header("Location: " . $_SERVER['PHP_SELF']); // refresh the page after sending the form
                } catch(PDOException $e) {
                    $actionMessage = $sql . "<br>" . $e->getMessage();
                }
            }
        }

        // edit row
        if(isset($_POST["edit"])) {
            if(!isset($_POST["time"]) || !isset($_POST["day"]) || $_POST["time"] == "" || $_POST["day"] == "") {
                $actionMessage = "fill in the required fields";
            } else {
                try {
                    $id = $_POST["data-id"];
                    $sql = $pdo->prepare("UPDATE ".$activityName." SET time_spent=?, day=? WHERE id=?");
                    $sql->execute(array($_POST["time"], $_POST["day"], $id));

                    // header("Location: " . $_SERVER['PHP_SELF']); // refresh the page after sending the form
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

                // header("Location: " . $_SERVER['PHP_SELF']); // refresh the page after sending the form
            } catch(PDOException $e) {
                $actionMessage = $sql . "<br>" . $e->getMessage();
            }
        }

        if(!empty($actionMessage)) {
            echo "<p>".$actionMessage."</p>";
        } else {
            echo "<h3>Table modified successfully!</h3>";
        }
    ?>

    <button><a href="<?php echo INCLUDE_PATH; ?>activity_data">Back to activity page</a></button>
</body>
</html>