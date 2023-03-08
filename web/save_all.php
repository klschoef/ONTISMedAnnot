<?php

        include '../config/config.php';

        function debug_to_log($data) {
            $output = $data;
            if (is_array($output))
                $output = implode(',', $output);

            error_log($output);
        }


        $_POST = json_decode(file_get_contents('php://input'), true);

        // DB connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        $param = "save_ids";

        $res = array("msg" => "ok");

        // error_log( print_r($_POST, TRUE));
        // exit();


        if (isset($_POST[$param])) {
            $arr = $_POST[$param];

            foreach ($arr as $values) {
                // debug_to_log($values);
                // exit();
                $id = $values["id"];
                $caption = $values["caption"];
                $reviewed = $values["reviewed"];
                // Strip any html characters
                $caption = htmlspecialchars($caption);
                // Clean input using the database
                $caption = mysqli_real_escape_string($conn, $caption);
                $sql = "UPDATE " . $dbtable . " SET caption='" . $caption . "', reviewed=" . $reviewed . " WHERE id=" . $id;
                if ($conn->query($sql) === TRUE) {
                    // no error - proceed
                    // echo "Updated " . $id . " =>" . $value . "<br>";
                    // echo $sql;
                } else {
                    $error = "Error: " . $sql . "\n" . $conn->error;
                    $res['msg'] = $error;
                    break;
                }
            }
        } else {
            $res['msg'] = "Error: no input given!";
        }

        echo json_encode($res);

?>