<?php

        include '../config/config.php';

        // $tmpRoot = dirname(__DIR__)."/tmp/";
        $tmpRoot = "./tmp/";
        $outFile = $tmpRoot . "exports_" . time() . ".csv";
        // conforming to https://git-ainf.aau.at/Konstantin.Schekotihin/ontis/-/tree/main/Machine%20Learning%20Component
        $outHeaders = ['filename', 'class'];

        $_POST = json_decode(file_get_contents('php://input'), true);

        // DB connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        $param = "include_unchecked";

        $res = array("msg" => "");

        // error_log( print_r($_POST, TRUE));
        // exit();

        if (isset($_POST[$param])) {
            $includeUnchecked = number_format($_POST[$param]);

            // default only reviewed
            $sql = "SELECT `image`, `caption` FROM `".$dbtable."` WHERE `reviewed` = 1";
            if ($includeUnchecked == 1) {
                // include all
                $sql = "SELECT `image`, `caption` FROM `".$dbtable."`";
            }

            try {
                $result = mysqli_query($conn, $sql);
                $fp = fopen($outFile, 'w');
                if ( !$fp ) {
                    throw new Exception('unable to create CSV file.');
                }
                fputcsv($fp, $outHeaders);
                while($row = mysqli_fetch_assoc($result)){
                    fputcsv($fp, $row);
                }
                fclose($fp);
            } catch (Exception $e) {
                $error = 'Error: exception: ' . $e->getMessage();
                $res['msg'] = $error;
            }

        } else {
            $res['msg'] = "Error: no input given!";
        }



        if (file_exists($outFile)) {
            $res['msg'] = str_replace("./", "", $outFile);
        } else {

            if ($res['msg'] == "") {
                $res['msg'] = "Error: failed to create CSV file.";
            }
        }

        echo json_encode($res);

        function debug_to_log($data) {
            $output = $data;
            if (is_array($output))
                $output = implode(',', $output);
            error_log($output);
        }


?>