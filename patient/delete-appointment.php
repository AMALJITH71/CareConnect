<?php
session_start();
if (isset($_SESSION["user"]) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'p') {
    $useremail = $_SESSION["user"];
} else {
    header("location: ../login.php");
    exit; // Ensure script stops execution after redirection
}

if ($_GET && isset($_GET["id"])) {
    // Include database connection
    require_once("../connection.php");

    // Sanitize input to prevent SQL injection
    $id = $_GET["id"];
    $id = mysqli_real_escape_string($database, $id);

    // Query to fetch doctor's name associated with the appointment
    $sql1 = "SELECT doctor.docname 
             FROM appointment 
             INNER JOIN schedule ON appointment.scheduleid = schedule.scheduleid
             INNER JOIN doctor ON schedule.docid = doctor.docid
             WHERE appointment.appoid = '$id'";

    $result1 = mysqli_query($database, $sql1);

    if ($result1 && mysqli_num_rows($result1) > 0) {
        $row = mysqli_fetch_assoc($result1);
        $doc = $row['docname'];

        // Perform deletion from the 'appointment' table
        $sql = "DELETE FROM appointment WHERE appoid='$id'";
        $result2 = mysqli_query($database, $sql);

        if ($result2) {
            // Prepare and execute email sending logic
            require 'send_mail.php'; // Adjust path if necessary

            $to = $useremail;
            $subject = 'Session Cancelled';
            $body = '<h1>Your session with ' . $doc . ' has been cancelled</h1><p>Details of the session</p><p>Appointment number: ' . $id . '</p>';
            $altBody = 'Your session has been cancelled. Details of the session: Appointment number ' . $id;

            // Assuming send_mail() function is correctly implemented in send_mail.php
            $mail_result = send_mail($to, $subject, $body, $altBody);

            if ($mail_result === true) {
                echo "Email sent successfully";
            } else {
                echo $mail_result; // Output any errors from send_mail function
            }
        } else {
            echo "Error: " . $database->error; // Output database error if deletion fails
        }
    } else {
        echo "Error: Doctor information not found for this appointment";
    }
} else {
    echo "Error: Appointment ID not provided";
}
header("location: appointment.php");
exit; // Ensure script stops execution after redirection
?>
