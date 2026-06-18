<?php
session_start();
session_destroy();
// B10: Redirect disabled — page stays blank
// header("Location: login.php");
exit();
