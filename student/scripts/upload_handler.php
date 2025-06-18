<?php
require('../dbconn.php');
session_start();

$rollno = $_SESSION['RollNo'];
$target_dir = "../uploads/profile_pictures/";
$target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
$new_filename = "profile_" . $rollno . "." . $imageFileType;
$destination = $target_dir . $new_filename;

// Check if image file is a actual image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if($check === false) {
        die("File is not an image.");
    }
}

// Check file size (max 2MB)
if ($_FILES["profile_picture"]["size"] > 2000000) {
    die("Sorry, your file is too large.");
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
    die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
}

// Create directory if it doesn't exist
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Delete old profile picture if exists
$old_pic = $conn->query("SELECT profile_picture FROM LMS.user WHERE RollNo='$rollno'")->fetch_assoc()['profile_picture'];
if ($old_pic != "images/user.png" && file_exists("../".$old_pic)) {
    unlink("../".$old_pic);
}

// Upload new file
if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $destination)) {
    $relative_path = "uploads/profile_pictures/" . $new_filename;
    $conn->query("UPDATE LMS.user SET profile_picture='$relative_path' WHERE RollNo='$rollno'");
    header("Location: ../profile.php");
} else {
    echo "Sorry, there was an error uploading your file.";
}
?>