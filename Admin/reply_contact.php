<?php
include('db.php');

if(isset($_POST['id']) && isset($_POST['reply'])){
    $id = intval($_POST['id']);
    $reply = mysqli_real_escape_string($con, $_POST['reply']);

    // Update reply and status
    $update = "UPDATE contact_messages SET admin_reply='$reply', status='replied' WHERE id=$id";
    mysqli_query($con,$update);

    // Send email to customer
    $row = mysqli_fetch_assoc(mysqli_query($con, "SELECT email, name FROM contact_messages WHERE id=$id"));
    $to = $row['email'];
    $subject = "Reply to your message";
    $message = "Hello ".$row['name'].",\n\nAdmin replied to your message:\n\n$reply";
    $headers = "From: admin@yourdomain.com";

    mail($to,$subject,$message,$headers);

    header("Location: admin_contact.php");
}
?>
