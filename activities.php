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
    <?php
        try {
            $sql = $pdo->prepare("SELECT * FROM `activities`");
            $sql->execute();
            $data = $sql->fetchAll();

            if($data == null) {
                echo "no values found";
            } else {
                echo "<table>
                    <tr>
                        <th>id</th>
                        <th>name</th>
                    </tr>";
                foreach($data as $key => $value) {
                    echo "<tr>
                            <td>".$value['id']."</td>
                            <td>".$value['name']."</td>
                        </tr>";
                }
                echo "</table>";
            }
        } catch(PDOException $e) {
            echo $sql . "<br>" . $e->getMessage();
        }
    ?>
    <br><br>

    <button><a href="index.php">Back to home page</a></button>
</body>
</html>