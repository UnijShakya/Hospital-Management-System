<?php
include('db.php');
include('assets/include/sidebar.php');

// Fetch all distinct emails with their latest name
$emails_res = mysqli_query($con, "
    SELECT email, 
           MAX(name) AS name, 
           MAX(created_at) AS latest 
    FROM contact_messages 
    GROUP BY email 
    ORDER BY latest DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Messages</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
body { 
    font-family: 'Poppins', sans-serif; 
    background-color: #f5f6f8; 
    margin: 0; 
}
.container { 
    margin-left: 240px; 
    padding: 40px 20px; 
}
h2 { 
    text-align: center; 
    color: #0d6efd; 
    margin-bottom: 30px; 
}

/* Email list box */
.email-list {
    max-width: 650px;
    margin: 0 auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    padding: 20px;
}

/* Each item */
.email-item {
    padding: 12px 15px;
    border-bottom: 1px solid #e9ecef;
    transition: 0.2s;
}
.email-item:last-child { border-bottom: none; }
.email-item:hover { background: #f8f9fa; }

/* Link style */
.email-item a {
    color: #212529;
    text-decoration: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.email-item i { color: #0d6efd; }

/* Name style */
.email-name {
    font-weight: 600;
    color: #0d6efd;
    font-size: 0.95rem;
}
.email-address {
    color: #495057;
    font-size: 0.9rem;
}
</style>
</head>
<body>

<div class="container">
    <h2>📬 User Email Messages</h2>
    <div class="email-list">
        <?php while ($row = mysqli_fetch_assoc($emails_res)): ?>
            <div class="email-item">
                <a href="view_messages.php?email=<?= urlencode($row['email']) ?>">
                    <div>
                        <div class="email-name"><?= htmlspecialchars($row['name'] ?? 'Unknown User') ?></div>
                        <div class="email-address"><?= htmlspecialchars($row['email']) ?></div>
                    </div>
                    <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
