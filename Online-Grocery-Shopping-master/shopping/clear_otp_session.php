<?php
session_start();
unset($_SESSION['otp']);
unset($_SESSION['otp_expiry']);
unset($_SESSION['otp_sent']);
unset($_SESSION['temp_user']);
echo "Session cleared";
?>