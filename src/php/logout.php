<?php
session_start();
session_unset();
session_destroy();
header("Location: /login"); // reindirizza alla root/login
exit;
?>