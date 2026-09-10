<?php
require_once __DIR__ . '/config.php';
unset($_SESSION['doctor_id']);
unset($_SESSION['doctor_name']);
header('Location: doctor-login.php');
exit;
