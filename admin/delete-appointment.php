<?php

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
            header("location: ../login.php");
        }

    }else{
        header("location: ../login.php");
    }
    
    
    if($_GET){
        //import database
        include("../connection.php");
        $id=$_GET["id"];
        $sql1 = "SELECT pemail,docname FROM `patient`,appointment,doctor,schedule where appointment.pid=patient.pid and appointment.scheduleid = schedule.scheduleid and schedule.docid=doctor.docid and appointment.appoid = '$id'";

    $result1 = mysqli_query($database, $sql1);

    if ($result1 && mysqli_num_rows($result1) > 0) {
        $row = mysqli_fetch_assoc($result1);
        $useremail = $row['pemail'];
        $doc=$row['docname'];
        //$result001= $database->query("select * from schedule where scheduleid=$id;");
        //$email=($result001->fetch_assoc())["docemail"];
        $sql= $database->query("delete from appointment where appoid='$id';");
        //$sql= $database->query("delete from doctor where docemail='$email';");
        //print_r($email);
        require '../patient/send_mail.php';
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
        header("location: appointment.php");
    }


?>