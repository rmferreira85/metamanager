<?php
include('DBconnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update']) && isset($_POST['confirm']) && $_POST['confirm'] === 'true') {
    $cpf = $_POST['cpf'];

    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    if ($senha !== $confirmar_senha) {
        echo "<script>alert('As senhas não coincidem. Por favor, tente novamente.'); window.history.back();</script>";
        exit;
    }
    
    $senhahash = password_hash($senha, PASSWORD_DEFAULT);

    $sqlUpdate = "UPDATE Usuario SET senha_hash=? WHERE cpf=?";
    $updateQuery = mysqli_prepare($link, $sqlUpdate);
    mysqli_stmt_bind_param($updateQuery, "ss", $senhahash, $cpf);
    
    
    $result = mysqli_stmt_execute($updateQuery);

    // Upload de imagens
    if ($_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['logo']['name'];
        $logoTmpName = $_FILES['logo']['tmp_name'];
        $targetDirectory = '/Applications/XAMPP/xamppfiles/htdocs/vsxampp/meta/dbImages/perfilPhotos/';
        $targetPath = $targetDirectory . $fileName;
        
        // Valida tamanho e tipo
        $allowedFileTypes = array('jpg', 'jpeg', 'png', 'gif');
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (in_array($fileExtension, $allowedFileTypes) && $_FILES['logo']['size'] <= $maxFileSize) {
            if (move_uploaded_file($logoTmpName, $targetPath)) {
                // Update logo path in the database
                $updateLogoQuery = mysqli_prepare($link, "UPDATE Usuario SET photo_path = ? WHERE cpf = ?");
                mysqli_stmt_bind_param($updateLogoQuery, "ss", $fileName, $cpf);
                mysqli_stmt_execute($updateLogoQuery);
                mysqli_stmt_close($updateLogoQuery);
            }
        }
    }

    // Check if update was successful and handle tempKey and user status update
    if ($result === TRUE) {
        $updatetempKeyQuery = "UPDATE Usuario SET tempKey = NULL, sit_user = 'active' WHERE cpf = '$cpf'";
        $updatetempKeyResult = $link->query($updatetempKeyQuery);

        if ($updatetempKeyResult === TRUE) {
            //header('Location: Login.php');
            //exit();
            echo "Inserido" . $link->error;
        } else {
            echo "Erro ao atualizar a tempKey e situação do usuário: " . $link->error;
        }
    } else {
        echo "Erro de atualização: " . $link->error;
    }

    // Close the prepared statement
    mysqli_stmt_close($updateQuery);
} else {
    echo "Erro ao processar o formulário.";
}
?>
