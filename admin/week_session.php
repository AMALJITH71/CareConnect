<?php
include("../connection.php");
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['days'])) {
    $title = $_POST['title'];
    $docid = $_POST['docid'];
    $nop = $_POST['nop'];
    $time = $_POST['time'];
    $days = $_POST['days'];

    foreach ($days as $day) {
        $sql = "INSERT INTO schedule (title, docid, day_of_week, scheduletime, nop) VALUES ('$title', '$docid', '$day', '$time', '$nop')";
        $database->query($sql);
    }
    header("Location: schedule.php?action=session-added&title=$title");
}
?>
