<?php
require_once __DIR__ . '/config.php';
unset($_SESSION['staff_id']);
unset($_SESSION['staff_name']);
header('Location: staff-login.php');
exit;
