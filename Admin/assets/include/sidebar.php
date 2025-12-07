<?php
// --- Session Logout Handling ---
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f5f6f8;
        margin: 0;
        display: flex;
        overflow-x: hidden;
    }

    .sidebar {
        width: 260px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background: #1f1f1fec;
        color: #fff;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
        overflow-y: auto;
        z-index: 1000;
    }

    .sidebar.collapsed {
        width: 80px;
    }

    .sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-header h4 {
        font-size: 1.2rem;
        margin: 0;
        white-space: nowrap;
    }

    #sidebar-toggle {
        background: none;
        border: none;
        color: #fff;
        font-size: 1.4rem;
        cursor: pointer;
    }

    .sidebar nav .nav-link {
        color: #cbd5e1;
        display: flex;
        align-items: center;
        padding: 12px 18px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s;
        position: relative;
        cursor: pointer;
    }

    .sidebar nav .nav-link:hover {
        background: #ffffffff;
        color: #000000ff;
    }

    .sidebar nav .nav-link.active {
        background: #ffffffff;
        color: #000000ff;
    }

    .sidebar nav .nav-link i {
        font-size: 1.2rem;
        margin-right: 10px;
    }

    .submenu {
        display: none;
        flex-direction: column;
        margin-left: 15px;
        padding-left: 10px;
        border-left: 2px solid rgba(255, 255, 255, 0.1);
    }

    .submenu.show {
        display: flex;
    }

    .submenu .nav-link {
        font-size: 0.95rem;
        padding: 8px 20px;
    }

    .sidebar.collapsed h4,
    .sidebar.collapsed .nav-link span {
        display: none;
    }

    .sidebar.collapsed .submenu {
        display: none !important;
    }

    .sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }

    .main-content {
        margin-left: 260px;
        padding: 20px;
        flex-grow: 1;
        transition: margin-left 0.3s ease;
    }

    .main-content.expanded {
        margin-left: 80px;
    }

    @media (max-width: 768px) {
        .sidebar {
            left: -260px;
        }

        .sidebar.show {
            left: 0;
        }

        .main-content {
            margin-left: 0;
        }
    }
    </style>
</head>

<body>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4>Admin Panel</h4>
            <button id="sidebar-toggle"><i class="bi bi-list"></i></button>
        </div>

        <nav class="nav flex-column mt-3">

            <a href="admin_dashboard.php" class="nav-link <?= $currentPage=='admin_dashboard.php'?'active':'' ?>"
                data-bs-title="Dashboard">
                <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
            </a>

            <!-- Doctors -->
            <div class="nav-link dropdown-toggle" data-bs-title="Doctors">
                <i class="bi bi-person-badge"></i> <span>Doctors</span>
            </div>
            <div class="submenu">
                <a href="add_specialization.php"
                    class="nav-link <?= $currentPage=='add_specialization.php'?'active':'' ?>">Specialization</a>
                <a href="add_doctor.php" class="nav-link <?= $currentPage=='add_doctor.php'?'active':'' ?>">Add
                    Doctor</a>
                <a href="manage_doctor.php" class="nav-link <?= $currentPage=='manage_doctor.php'?'active':'' ?>">Manage
                    Doctors</a>
            </div>

            <!-- Patients -->
            <div class="nav-link dropdown-toggle" data-bs-title="Patients">
                <i class="bi bi-person-vcard"></i> <span>Patients</span>
            </div>
            <div class="submenu">
                <a href="manage_patient.php"
                    class="nav-link <?= $currentPage=='manage_patient.php'?'active':'' ?>">Manage Patients</a>
            </div>

            <!-- Appointments -->
            <a href="appointment_details.php"
                class="nav-link <?= $currentPage=='appointment_details.php'?'active':'' ?>"
                data-bs-title="Appointments">
                <i class="bi bi-calendar-check"></i> <span>Appointments</span>
            </a>

            <!-- Pages -->
            <div class="nav-link dropdown-toggle" data-bs-title="Pages">
                <i class="bi bi-layout-text-sidebar-reverse"></i> <span>Pages</span>
            </div>
            <div class="submenu">
                <a href="Gallery.php" class="nav-link <?= $currentPage=='Gallery.php'?'active':'' ?>">Gallery</a>
                <a href="banner.php" class="nav-link <?= $currentPage=='banner.php'?'active':'' ?>">Banners</a>
                <a href="services.php" class="nav-link <?= $currentPage=='services.php'?'active':'' ?>">Services</a>
            </div>

            <!-- Reports -->
            <div class="nav-link dropdown-toggle" data-bs-title="Reports">
                <i class="bi bi-file-earmark-text"></i> <span>Reports</span>
            </div>
            <div class="submenu">
                <a href="report_create.php" class="nav-link <?= $currentPage=='report_create.php'?'active':'' ?>">Add
                    Report</a>
                <a href="report_search.php" class="nav-link <?= $currentPage=='report_search.php"'?'active':'' ?>">Manage
                    Reports</a>
            </div>

            <!-- Messages -->
            <a href="admin_contact.php" class="nav-link <?= $currentPage=='admin_contact.php'?'active':'' ?>"
                data-bs-title="Messages">
                <i class="bi bi-envelope"></i> <span>Messages</span>
            </a>

            <!-- Logout -->
            <a href="?action=logout" class="nav-link text-danger mt-3" data-bs-title="Logout">
                <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
            </a>

        </nav>
    </div>

    <script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const mainContent = document.getElementById('mainContent');

    // Sidebar collapse
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent?.classList.toggle('expanded');
    });

    // Dropdown open/close
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const submenu = toggle.nextElementSibling;
            submenu.classList.toggle('show');
        });
    });
    </script>

</body>

</html>
