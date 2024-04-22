<?php
session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['senha']) || !isset($_SESSION['tipo_user'])) {
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    unset($_SESSION['tipo_user']);
    $logado = false;
    $tipoUsuario = null;
    $idUsuario = null;
    header('Location: login.php');
    exit();
} else {
    $logado = true;
    $tipoUsuario = $_SESSION['tipo_user'];
}

if ($logado) {
    //echo "Usuário logado: " . $_SESSION['email'];
} else {
    //echo "Usuário não logado.";
}

?>
