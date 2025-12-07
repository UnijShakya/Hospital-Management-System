<?php
include('assets/include/navbar.php'); // Include your navbar
include('db.php');

if(isset($_GET['token'])){
    $token = mysqli_real_escape_string($con, $_GET['token']); // sanitize input
    $res = mysqli_query($con,"SELECT * FROM contact_messages WHERE token='$token'");

    if(mysqli_num_rows($res) > 0){
        $row = mysqli_fetch_assoc($res);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>View Reply</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body {
                    background-color: #f8f9fa;
                    min-height: 100vh;
                }
                .reply-container {
                    max-width: 800px;
                    margin: 60px auto;
                    padding: 30px;
                    background: #fff;
                    border-radius: 15px;
                    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
                }
                h3 {
                    color: #0d6efd;
                    margin-bottom: 20px;
                }
                .message, .admin-reply {
                    padding: 15px;
                    border-left: 5px solid #0d6efd;
                    background: #f1f3f6;
                    border-radius: 8px;
                    margin-bottom: 20px;
                }
                .admin-reply {
                    border-color: #6610f2;
                    background: #e9e6ff;
                }
                .status {
                    font-style: italic;
                    color: #888;
                }
            </style>
        </head>
        <body>
            <div class="container reply-container">
                <h3>Subject: <?= htmlspecialchars($row['subject']) ?></h3>

                <div class="message">
                    <strong>Your Message:</strong>
                    <p><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                </div>

                <?php if($row['status'] == 'replied' && !empty($row['admin_reply'])): ?>
                    <div class="admin-reply">
                        <strong>Admin Reply:</strong>
                        <p><?= nl2br(htmlspecialchars($row['admin_reply'])) ?></p>
                    </div>
                <?php else: ?>
                    <p class="status">Admin has not replied yet.</p>
                <?php endif; ?>

                <a href="index.php" class="btn btn-primary mt-3">Back to Home</a>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    } else {
        echo "<div class='alert alert-danger text-center mt-5'>Invalid link!</div>";
    }
} else {
    echo "<div class='alert alert-warning text-center mt-5'>No token provided!</div>";
}
?>
