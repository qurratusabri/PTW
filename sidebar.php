<?php
// sidebar.php
if (!isset($_SESSION)) {
    session_start();
}
?>

<div class="sidebar" id="sidebar">
    <div class="top">
        <div class="logo">
            <i class="bx bx-hard-hat"></i>
            <span> PermitToWork</span>
        </div>
        <i class="bx bx-menu" id="btn"></i>
    </div>

    <ul class="nav-list">
        <li class="nav-item user-info" title="Logged in as: <?= htmlspecialchars($_SESSION['username']) ?>">
            <a href="#">
                <i class="bx bx-user"></i>
                <span>Logged in as:<br><?= htmlspecialchars($_SESSION['username']) ?></span>
            </a>
        </li>

        <?php if ($_SESSION['user_type'] === 'applicant'): ?>
            <li class="nav-item">
                <a href="appdb.php" title="Dashboard">
                    <i class="bx bxs-grid-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="form1.php" title="Form">
                    <i class="bx bx-file-blank"></i>
                    <span>Form</span>
                </a>
            </li>

        <?php elseif ($_SESSION['user_type'] === 'admin'): ?>
            <li class="nav-item">
                <a href="dashboard.php" title="Dashboard">
                    <i class="bx bxs-grid-alt"></i>
                    <span class="nav-item">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="form.php" title="Form">
                    <i class="bx bx-file-blank"></i>
                    <span class="nav-item">Form</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="services.php" title="Services">
                    <i class="bx bx-add-to-queue"></i>
                    <span class="nav-item">Services</span>
                </a>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <a href="logout.php" onclick="return confirmLogout();" title="Logout">
                <i class="bx bx-log-out"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</div>
