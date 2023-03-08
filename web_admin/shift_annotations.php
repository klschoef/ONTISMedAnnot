<?php

        // static variables
        $servername = "localhost";
        $dbname = "clip_annot_tool";
        $dbtable = "annotations";
        $username = "clip_annot_tool";
        $password = "3UMNL3Ht3PAvWWmZowNy";

        echo "Reading DB...<br>";

        // DB connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        $sql = "SELECT * FROM `".$dbtable."`";

        $result = mysqli_query(
            $conn,
            $sql
        );
        $prevId = -1;
        $prevCap = "";
        $mapping = array();
        while($row = mysqli_fetch_array($result)){
            $curId = $row['id'];
            $curCap = $row['caption'];
            if ($prevId != -1) {
                $mapping[$prevId] = $curCap;
            }
            $prevId = $curId;
            $prevCap = $curCap;
        }

        echo "Updating DB...<br>";
        foreach ($mapping as $id => $value) {
            $sql = "UPDATE " . $dbtable . " SET caption='" . $value . "' WHERE id=" . $id;
            if ($conn->query($sql) === TRUE) {
                echo "Updated " . $id . " =>" . $value . "<br>";
                // echo $sql;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
            }
        }


        echo "...Done!";

?>