<?php
$plainPassword = "12345"; // change this to your real password
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);
echo $hash;
?>