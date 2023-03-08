<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title> ONTIS Image Annotation Tool - Visualizer </title>
  <meta name="description" content="Visualization/Update of annotations made using the Caption Tool.">
  <meta name="author" content="SitePoint">

  <meta property="og:title" content="Caption Annotator Visualization 1.0">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://www.sitepoint.com/a-basic-html5-template/">
  <meta property="og:description" content="Visualization/Update of annotations made using the Caption Tool.">
  <meta property="og:image" content="image.png">

  <link rel="icon" href="/favicon.ico">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">


  <script src="js/visualize.js" defer></script>
  <link rel="stylesheet" href="css/common.css?v=1.0">
  <link rel="stylesheet" href="css/styles_visualize.css?v=1.0">

  <!-- 3rd party -->
  <!-- lightbox -->
  <script src="lib/simple-lightbox/simple-lightbox.min.js" defer></script>
  <link rel="stylesheet" href="lib/simple-lightbox/simple-lightbox.min.css">
  <!-- fontawesome -->
  <link href="lib/fontawesome6/css/fontawesome.min.css" rel="stylesheet">
  <link href="lib/fontawesome6/css/all.min.css" rel="stylesheet">
  <!-- toastify.js - https://github.com/apvarun/toastify-js -->
  <script src="lib/toastifyjs/toastify.js" defer></script>
  <link href="lib/toastifyjs/toastify.css" rel="stylesheet">

  <!-- needs to be loaded last -->
  <script src="js/utils.js" defer></script>

</head>
<body>

    <?php
        /*
            INIT
        */

        include '../config/config.php';

        // session
        session_start();

        // static variables
        // $servername = "localhost";
        // $dbname = "clip_annot_tool";
        // $dbtable = "annotations";
        // $username = "clip_annot_tool";
        // $password = "3UMNL3Ht3PAvWWmZowNy";
        // $imagesDir = 'images/';

        // dynamic variables
        $curPage = isset($_SESSION['cur_page']) ? $_SESSION['cur_page'] : 0;
        if (isset($_POST['page'])) {
            $curPage = $_POST['page'] - 1;
        }
        if ($curPage < 0) {
            $curPage = 0;
        }
        $imageWidth = isset($_SESSION['image_width']) ? $_SESSION['image_width'] : 400;
        $itemsPerPage = isset($_SESSION['num_items']) ? $_SESSION['num_items'] : 12;
        $showReviewed = isset($_SESSION['review_mode']) ? $_SESSION['review_mode'] : "false";
        // echo $showReviewed;
        // echo gettype($showReviewed);

        // var_dump($_SESSION['cur_page']);

        // GLOBAL DB connection
        $conn = new mysqli($servername, $username, $password, $dbname);
    ?>

    <div class='content'>
        <div class='left-header'>
            <a href="index.php" title="Annotator"><i class="fa-solid fa-pen-to-square"></i></a>
            <a href="visualize.php" class="highlight" title="Viewer"><i class="fa-solid fa-eye"></i></a>
        </div>
        <div class="middle-header">

                <div class="middle-element">
                    <div class="icon" title="image size"><i class="fa-solid fa-image fa-sm"></i></div>
                    <div title="image size">
                        <input id="image_width-input" type="number" step="50" min="50" max="1000" name="image-width" value="<?php echo $imageWidth;?>" >
                    </div>
                </div>

                <div class="middle-element">
                    <div class="icon" title="images per page" ><i class="fa-solid fa-hashtag fa-sm"></i></div>
                    <div title="images per page">
                        <input id="num_items-input" type="number" min="1" max="30" name="image-num" value="<?php echo $itemsPerPage;?>">
                    </div>
                </div>

                <div class="middle-element">
                    <div class="icon" title="show reviewed" ><i class="fa-solid fa-file-circle-check fa-sm"></i></div>
                    <div class="icon" title="show reviewed">
                        <?php
                            if ($showReviewed == "true") {
                                echo '<input id="review_mode-input" class="largerCheckbox" type="checkbox" checked>';
                            } else {
                                echo '<input id="review_mode-input" class="largerCheckbox" type="checkbox">';
                            }
                            ?>
                    </div>
                </div>

                <div class="middle-element" style="flex-basis: 47%;">
                    <div class="icon" title="current clipboard (read only)" ><i class="fa-solid fa-clipboard fa-sm"></i></div>
                    <div title="current clipboard (read only)" style="box-sizing: border-box; width: 100%">
                        <input id="input_clipboard-contents" style="box-sizing: border-box; width: 100%" readonly></input>
                    </div>
                    <div class="icon">
                        <a class="custom-button-red" title="clear" href="#" onclick="clearClipboardDisplay()"><i class="fa-solid fa-delete-left fa-sm"></i></a>
                    </div>
                    <div class="icon">
                        <a class="custom-button" title="copy" href="#" onclick="copyToClipboard('clipboard-contents')"><i class="fa-regular fa-copy fa-sm"></i></a>
                    </div>
                </div>

                <div class="middle-element">
                    <div class="icon" >
                        <a class="custom-button-red" title="save all" href="#" onclick="saveAll()"><i class="fa-solid fa-save fa-sm"></i></a>
                    </div>
                    <div class="icon" >
                        <a class="custom-button" title="export csv" href="#" onclick="exportCSV()"><i class="fa-solid fa-file-export fa-sm"></i></a>
                    </div>
                </div>
                <!-- <div class="icon" title="bulk replace captions">
                    <a class="custom-button-red" href="visualize.php"><i class="fa-solid fa-circle-arrow-left fa-sm"></i></a>
                </div> -->

            </div>
        <div class='right-header'>
            <a class="custom-button" title="refresh" href="visualize.php"><i class="fa-solid fa-arrow-rotate-right"></i></a>
        </div>
        <h1>ONTIS Image Annotation Tool - Visualizer</h1>
        <?php

            /*
                OPERATIONS WITH OUTPUT
            */

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
                echo "Could not connect to database";
            }

            // 1 potentially update entry
            $output = "<div id='userInfo'>";
            if(isset($_POST['save']))
            {
                $checked = isset($_POST['checked']) ? 1 : 0;
                $output .= updateEntry($_POST['id'], $_POST["caption"], $checked);
            }
            elseif(isset($_POST['delete'])) {
                $output .= deleteEntry($_POST['id']);
            }
            if(isset($_SESSION['user_info']))
            {
                $output .= $_SESSION['user_info'];
                unset($_SESSION['user_info']);
            }
            $output .= "</div>";
            echo $output;

            // 2. get total entries
            $sql = "SELECT COUNT(*) AS total FROM `" . $dbtable. "`";
            if ($showReviewed == "false") {
                $sql = "SELECT COUNT(case `reviewed` when 0 then 1 else null end) AS total FROM `" . $dbtable. "`";
            }
            $result = $conn->query($sql);
            $data =  $result->fetch_assoc();
            $total_records = $data['total'];
            $total_no_of_pages = ceil($total_records / $itemsPerPage);
            if (($curPage + 1) > $total_no_of_pages) {
                $curPage = $total_no_of_pages - 1;
            }
            $_SESSION['cur_page'] = $curPage;
            $curOffset = $curPage * $itemsPerPage;
            $second_last = $total_no_of_pages - 1; // total pages minus 1
            $output = "<div id='pageChangeWrapper'>";
            // $output .= "<div class='icon' title='bulk replace captions'>";
            // $output .= '<a class="custom-button-red" href="visualize.php"><i class="fa-solid fa-circle-arrow-left fa-sm"></i></a>';
            // $output .= '</div>';
            $output .= '<form id="pageChangeForm" method="post" action="visualize.php">';
            $output .= "<strong>";
            $output .= "Page ";
            $output .= '<input id="pageChangeInput" min="1" max="'.$total_no_of_pages.'" type="number" name="page" value="'.($curPage + 1).'">';
            $output .= " of ".$total_no_of_pages;
            $output .= "</strong>";
            $output .= "</form>";
            $output .= "</div>";
            echo $output;


            // 3. read and display annotations
            // $output = '<div class="annot-gallery-wrapper-outer">';
            $output = '<div class="annot-gallery-wrapper-inner">';


            $sql = "SELECT * FROM `".$dbtable."` LIMIT " . $itemsPerPage . " OFFSET " . $curOffset;
            if ($showReviewed == "false") {
                $sql = "SELECT * FROM `".$dbtable."` WHERE `reviewed` = 0  LIMIT " . $itemsPerPage . " OFFSET " . $curOffset;
            }

            $result = mysqli_query(
                $conn,
                $sql
                );
            while($row = mysqli_fetch_array($result)){
                // onsubmit="event.preventDefault(); validateInputs(event);"
                $imgPath = $imagesDir.'/'.$row['image'];
                $output .= '<form class="annotationForm" method="post" action="visualize.php">';
                $output .= "<div class='entry-wrapper'>";
                $output .= "<div class='image-wrapper' style='max-width:".$imageWidth."px'><a href='".$imgPath."'><img class='image' src='".$imgPath."'></a></div>"
                // .$row['id']
                .'<div style="display: flex;">'
                .'<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width:10%">';

                if ($row['reviewed'] === "1") {
                    $output .= '<input id="check-'.$row['id'].'" class="largerCheckbox" title="reviewed" name="checked" type="checkbox" checked>';
                } else {
                    $output .= '<input id="check-'.$row['id'].'" class="largerCheckbox" title="reviewed" name="checked" type="checkbox">';
                }

                $output .= '<a class="custom-button" title="copy" href="#" onclick="copyToClipboard('.$row['id'].')"><i class="fa-regular fa-copy fa-sm"></i></a>'
                .'<a class="custom-button-red" title="paste" href="#" onclick="replaceContent('.$row['id'].')"><i class="fa-solid fa-paste fa-sm"></i></a>'
                .'</div>'
                // .'<input id="input_'.$row['id'].'" type="text" style="width:80%" size="5" maxlength="180" name="caption" value="'.$row['caption'].'">'
                .'<textarea id="input_'.$row['id'].'" style="width:90%;resize: none;" rows="5" maxlength="180" name="caption" >'.$row['caption'].'</textarea>'
                .'<input type="hidden" name="id" value="'.$row['id'].'">'
                .'</div>'
                .'<div style="display: flex; justify-content: center; align-content: center;">'
                .'<button id="save" title="save" name="save" style="width: 50%;height: 100%;" type="submit"><i class="fa-solid fa-floppy-disk"></i></button>'
                .'<button id="delete" class="deleteEntryButton" title="delete" name="delete" style="width: 50%;height: 100%;color: #e41a1c;" type="submit"><i class="fa-solid fa-trash-can"></i></button>'
                // .'<input style="width: 50%;height: 100%;" type="submit" id="save" value="save" name="save">'
                // .'<input class="deleteEntryButton" style="width: 50%;height: 100%;color: #e41a1c;" type="submit" id="delete" value="delete" name="delete">'
                .'</div>'
                ."</div>";
                $output .= "</form>";
            }
            // inner gallery wrapper
            $output .= "</div>";
            // outer gallery wrapper
            // $output .= "</div>";
            echo $output;

            // Update DB
            function updateEntry($id, $userinput, $isChecked) {

                // get connection
                $conn = $GLOBALS['conn'];

                // Strip any html characters
                $userinput = htmlspecialchars($userinput);

                // Clean input using the database
                $userinput = mysqli_real_escape_string($conn, $userinput);

                $sql = "UPDATE " . $GLOBALS['dbtable'] . " SET caption='" . $userinput . "', reviewed=" . $isChecked . " WHERE id=" . $id;
                // $sql = "UPDATE " . $GLOBALS['dbtable'] . " SET caption='" . $userinput . "' WHERE id=" . $id;

                if ($conn->query($sql) === TRUE) {
                    return "Caption updated!";
                    // echo $sql;
                } else {
                    return "Error: " . $sql . "<br>" . $conn->error;
                }

            }

            function deleteEntry($id) {
                $conn = $GLOBALS['conn'];
                $sql = "DELETE FROM " . $GLOBALS['dbtable'] . " WHERE id=" . $id;


                if ($conn->query($sql) === TRUE) {
                    return "Entry deleted!";
                    // echo $sql;
                } else {
                    return "Error: " . $sql . "<br>" . $conn->error;
                }
            }



        ?>

    </div><!-- content -->
</body>
<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>
</html>