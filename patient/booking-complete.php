<?php

//learn from w3schools.com

session_start();

if (isset($_SESSION["user"])) {
    if (($_SESSION["user"]) == "" or $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
}


//import database
include("../connection.php");
$sqlmain = "select * from patient where pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];
$username = $userfetch["pname"];


if ($_POST) {
    if (isset($_POST["booknow"])) {
        // Assuming $database is your database connection object

        $apponum = $_POST["apponum"];
        $scheduleid = $_POST["scheduleid"];
        $date = $_POST["date"];
        $app = $_POST["appointment_date"];
        // Replace with your actual user ID or fetch from session
        // $useremail = 'user@example.com'; // Define $useremail

        // Prepare and execute the SQL query using prepared statements
        $stmt = $database->prepare("INSERT INTO appointment (pid, apponum, scheduleid, appodate,appointment) VALUES (?, ?, ?, ?,?)");
        $stmt->bind_param("iiiss", $userid, $apponum, $scheduleid, $date, $app);
        $stmt->execute();
        $stmt->close();

        // Query to get the doctor's name
        $sql_doc = "SELECT docname FROM schedule, doctor WHERE schedule.docid = doctor.docid AND scheduleid = $scheduleid";
        $result_doc = $database->query($sql_doc);
        if ($result_doc && $result_doc->num_rows > 0) {
            $row = $result_doc->fetch_assoc();
            $doc = $row['docname'];

            // Include the email sending logic
            require 'send_mail.php'; // Adjust the path if necessary

            $to = $useremail;
            $subject = 'Session Confirmation';
            $body = '<h1>Your session with ' . $doc . ' has been scheduled</h1><p>Details of the session</p><p>Appointment no ' . $apponum . '</p>';
            $altBody = 'Your session has been scheduled. Details of the session';

            $result = send_mail($to, $subject, $body, $altBody);

            if ($result === true) {
                echo "Email sent successfully";
            } else {
                echo $result;
            }

            // Redirect after successful booking
            header("location: appointment.php?action=booking-added&id=" . $apponum . "&titleget=none");
            exit; // Ensure no further output interferes with the redirect
        } else {
            echo "Error: Unable to retrieve doctor's information";
        }
    }
}
