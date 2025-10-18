<?php
$plainPassword = "123456"; // change this to your real password
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);
echo $hash;
?>