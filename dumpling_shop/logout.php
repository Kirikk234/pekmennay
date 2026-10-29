<?php
require_once __DIR__ . '/inc/functions.php';
session_destroy();
header('Location: /pekmenay/dumpling_shop/index.php');
exit;