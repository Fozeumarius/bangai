<?php
$newPassword = "12345";
$hash = password_hash($newPassword, PASSWORD_DEFAULT);
echo $hash;
?>
