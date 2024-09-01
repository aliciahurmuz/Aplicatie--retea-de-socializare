<?php
    session_start();
    include '../core/init.php';
    $getFromU->logout();
    header('Location: '.BASE_URL.'index.php');
    exit();
?>
