<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>ONTIS - Image Annotation Tool</title>
  <meta name="description" content="A tool for captioning images.">
  <meta name="author" content="SitePoint">

  <meta property="og:title" content="Caption Annotator 1.0">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://www.sitepoint.com/a-basic-html5-template/">
  <meta property="og:description" content="A tool for captioning images.">
  <meta property="og:image" content="image.png">

  <link rel="icon" href="/favicon.ico">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">

  <link rel="stylesheet" href="css/styles.css?v=1.0">
  <link rel="stylesheet" href="css/common.css?v=1.0">

  <!-- 3rd party -->
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
<div class='content'>

<div class='left-header'>
    <a href="index.php" class="highlight" title="Annotator"><i class="fa-solid fa-pen-to-square"></i></a>
    <a href="visualize.php" title="Viewer"><i class="fa-solid fa-eye"></i></a>
</div>
<div class='right-header'>
    <a class="custom-button" title="refresh" href="index.php"><i class="fa-solid fa-arrow-rotate-right"></i></a>
</div>
<h1>ONTIS Image Annotation Tool - Annotator</h1>

<?php

include '../config/config.php';

// Connect to MySQL
// $servername = "localhost";
// $dbname = "clip_annot_tool";
// $dbtable = "annotations";
// $username = "clip_annot_tool";
// $password = "3UMNL3Ht3PAvWWmZowNy";
// $imagesDir = 'images/';

$images = glob($imagesDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

$randomImage = $images[array_rand($images)]; // See comments
$randomImageRelative = str_replace("images/", "./", $randomImage);
?>

<div class='image-wrapper'>
    <img src="<?php echo $randomImage; ?>" class="image" />
</div>


<form method="post" action="index.php">
    <div class="input-form-elements">
        <div>
            <input type="text" size="80" maxlength="180" name="caption" autofocus>
            <input type="hidden" name="imgUrl" value="<?php echo $GLOBALS['randomImageRelative']; ?>"/>
        </div>
        <div>
            <input type="submit" id="save" value="save" name="submit"> <!-- assign a name for the button -->
        </div>
    </div>
</form>


<?php
function postUserInput($userinput, $imgUrl) {

    $output = "<div class='userinfo'>";

     // check if input is empty
    if (empty($userinput)) {
        $output .= "Image skipped...";
        $skip = true;
    }

    if (!$skip) {
        // Create connection
        $conn = new mysqli($GLOBALS['servername'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['dbname']);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
            $output .= "Could not connect to database";
        }
        // echo "Connected successfully";

        $skip = false;



        // Strip any html characters
        $userinput = htmlspecialchars($userinput);

        // Clean input using the database
        $userinput = mysqli_real_escape_string($conn, $userinput);


        $sql = "INSERT INTO " . $GLOBALS['dbtable'] . " (image, caption) VALUES ('" . $imgUrl . "', '" . $userinput . "')";
        // echo $sql;
        // echo '<br>';

        if ($conn->query($sql) === TRUE) {
            $output .= "Caption saved!";
        } else {
            $output .= "Error: " . $sql . "<br>" . $conn->error;
        }


        // disconnect from db
        $conn->close();
    }

    // Return a cleaned string
    // return $userinput;

    $output .= "</div>";
    echo $output;

}

if(isset($_POST['submit']))
{
    postUserInput($_POST["caption"], $_POST['imgUrl']);

}

?>


  <!-- <script src="js/scripts.js"></script> -->
</div> <!-- content -->
</body>
<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>
</html>

