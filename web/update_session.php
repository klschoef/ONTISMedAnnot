<?php
session_start();

// allowed settings
$allowedValues = array("image_width", "num_items", "review_mode", "user_info");

// set posted settings
foreach ($allowedValues as &$value) {
    if (isset($_GET[$value])) {
        $_SESSION[$value] = $_GET[$value];
    }
}


?>