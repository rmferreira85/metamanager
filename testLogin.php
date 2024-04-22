<?php
session_start();

if (isset($_SESSION['login_error'])) {
    // Exibe a mensagem de erro na tela de login
    $login_error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
} else if (isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
    // Acesso ao DB
    include_once('DBconnect.php');
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Procura no DB na tabela Usuario
    $sqlUsuario = "SELECT * FROM Usuario WHERE email = ?";
    $stmtUsuario = $link->prepare($sqlUsuario);
    $stmtUsuario->bind_param("s", $email);
    $stmtUsuario->execute();
    $resultUsuario = $stmtUsuario->get_result();

    if ($resultUsuario->num_rows > 0) {
        $user = $resultUsuario->fetch_assoc();
        // Verificar a senha usando password_verify()
        if (password_verify($senha, $user['senha_hash'])) {
            // Verificar o status do usuário
            if ($user['sit_user'] == 'active') {
                // Definir a chave 'tipo_user' na sessão
                if ($user['adm'] == '1'){
                    $_SESSION['tipo_user'] = 'admin';
                }else{
                    $_SESSION['tipo_user'] = 'user';
                }        
                // ACESSO AO SISTEMA como Usuário
                $_SESSION['email'] = $email;
                $_SESSION['senha'] = $senha;
                $_SESSION['logado'] = 'usuario';
                header('Location: sistema.php');
                exit();
            } else {
                // Usuário não tem stat3us ativo
                $_SESSION['login_error'] = 'Usuário inativo ou aguardando confirmação';
                header('Location: login.php');
                exit();
            }
        } else {
            // Senha incorreta
            $_SESSION['login_error'] = 'Senha incorreta';
            header('Location: login.php');
            exit();
        }
    }
}  
?>