<?php
require_once __DIR__ . '/../../config.php';
requireLogin();

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — LifeCare Nursing & Medical Services</title>
    <!-- Google Fonts & Bootstrap 5 & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <style>
        :root {
            --teal-900: #0E3B36;
            --teal-700: #1B6B63;
            --teal-500: #2E8C82;
            --brand-navy: #03357A;
            --brand-teal: #029491;
            --amber-500: #D9A441;
            --bg-light: #F4F7F6;
        }
        body { font-family: 'Manrope', sans-serif; background-color: var(--bg-light); color: #2B3230; margin: 0; min-height: 100vh; }
        h1, h2, h3, h4, h5 { font-family: 'Fraunces', serif; }
        
        .admin-sidebar {
            width: 260px; background: var(--teal-900); color: #fff; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000;
            display: flex; flex-direction: column; transition: 0.3s ease;
        }
        .admin-brand { padding: 1.5rem 1.25rem; background: rgba(0,0,0,0.15); display: flex; align-items: center; gap: 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .admin-brand img { height: 42px; background: #fff; padding: 4px; border-radius: 6px; }
        .admin-brand-text { font-family: 'Fraunces', serif; font-size: 1.1rem; font-weight: 700; color: #fff; line-height: 1.2; }
        .admin-brand-text span { font-size: 0.75rem; font-family: 'Manrope', sans-serif; color: var(--amber-500); font-weight: 600; display: block; }
        
        .admin-menu { padding: 1.25rem 0.75rem; flex: 1; list-style: none; margin: 0; }
        .admin-menu-label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.1em; color: #8BA8A3; padding: 0.75rem 0.75rem 0.4rem; font-weight: 700; }
        .admin-menu-item a {
            display: flex; align-items: center; gap: 0.85rem; padding: 0.75rem 1rem; color: #DCEAE7; text-decoration: none; border-radius: 8px; font-size: 0.92rem; font-weight: 600; transition: 0.2s;
        }
        .admin-menu-item a:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .admin-menu-item.active a { background: var(--brand-teal); color: #fff; font-weight: 700; box-shadow: 0 4px 12px rgba(2,148,145,0.3); }
        .admin-menu-item i { font-size: 1.15rem; }

        .admin-wrapper { margin-left: 260px; min-height: 100vh; display: flex; flex-direction: column; }
        .admin-topbar { background: #fff; height: 68px; border-bottom: 1px solid #E3EEEC; padding: 0 2rem; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 990; }
        .admin-content { padding: 2rem; flex: 1; }

        .card-custom { border: none; border-radius: 12px; box-shadow: 0 8px 24px rgba(14,59,54,0.06); background: #fff; overflow: hidden; }
        .card-custom-header { background: #fff; border-bottom: 1px solid #E8F0EE; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; }
        
        .btn-care { background: var(--brand-teal); color: #fff; font-weight: 700; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; transition: 0.25s; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; }
        .btn-care:hover { background: var(--teal-900); color: #fff; }
        
        .thumb-preview { width: 54px; height: 54px; border-radius: 8px; object-fit: cover; border: 1px solid #ddd; }
        
        @media (max-width: 991px) {
            .admin-sidebar { margin-left: -260px; }
            .admin-sidebar.show { margin-left: 0; }
            .admin-wrapper { margin-left: 0; }
        }
    </style>
</head>
<body>

<div class="admin-sidebar" id="adminSidebar">
    <div class="admin-brand">
        <img src="../assets/logo.png" alt="LifeCare Logo">
        <div class="admin-brand-text">
            LifeCare Panel
            <span>ADMIN PORTAL</span>
        </div>
    </div>
    
    <ul class="admin-menu">
        <li class="admin-menu-label">Main Navigation</li>
        <li class="admin-menu-item <?= $currentPage === 'index.php' ? 'active' : '' ?>">
            <a href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        </li>
        
        <li class="admin-menu-label mt-2">Card Management</li>
        <li class="admin-menu-item <?= $currentPage === 'doctors.php' ? 'active' : '' ?>">
            <a href="doctors.php"><i class="bi bi-person-badge-fill"></i> Doctor Panel Cards</a>
        </li>
        <li class="admin-menu-item <?= $currentPage === 'staff.php' ? 'active' : '' ?>">
            <a href="staff.php"><i class="bi bi-people-fill"></i> Staff Panel Cards</a>
        </li>
        <li class="admin-menu-item <?= $currentPage === 'team.php' ? 'active' : '' ?>">
            <a href="team.php"><i class="bi bi-diagram-3-fill"></i> Our Team Cards</a>
        </li>
        <li class="admin-menu-item <?= $currentPage === 'blogs.php' ? 'active' : '' ?>">
            <a href="blogs.php"><i class="bi bi-journal-richtext"></i> Blog Posts & Articles</a>
        </li>
        
        <li class="admin-menu-label mt-2">Quick Site Links</li>
        <li class="admin-menu-item">
            <a href="../index.html" target="_blank"><i class="bi bi-globe"></i> View Website</a>
        </li>
    </ul>

    <div class="p-3 border-top border-secondary">
        <a href="logout.php" class="btn btn-outline-danger w-100 btn-sm font-weight-bold d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-box-arrow-right"></i> Logout Admin
        </a>
    </div>
</div>

<div class="admin-wrapper">
    <div class="admin-topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none" type="button" onclick="document.getElementById('adminSidebar').classList.toggle('show')">
                <i class="bi bi-list fs-4"></i>
            </button>
            <h5 class="m-0 text-secondary d-none d-sm-block">LifeCare Management System</h5>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-success-subtle text-success px-3 py-2 border border-success-subtle rounded-pill">
                <i class="bi bi-person-circle me-1"></i> Admin User
            </span>
        </div>
    </div>
    <div class="admin-content">
