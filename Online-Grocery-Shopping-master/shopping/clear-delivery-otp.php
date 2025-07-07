<?php
session_start();
unset($_SESSION['delivery_otp']);
unset($_SESSION['delivery_otp_order']);
unset($_SESSION['delivery_otp_time']);
echo json_encode(['success' => true]);
?>