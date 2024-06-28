<?php 
if (isset($_POST['submit'])) {
    include_once('./config/conexao.php');

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    $setor = filter_input(INPUT_POST, 'setor', FILTER_SANITIZE_STRING);
    $nivel_acesso = filter_input(INPUT_POST, 'nivel_acesso', FILTER_SANITIZE_STRING);

    $stmt = $conexao->prepare("SELECT id FROM usuario WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        $conexao->close();
        header("Location: cadastro.php?error=id_exists");
        exit();
    }
    $stmt->close();

    $stmt = $conexao->prepare("SELECT email FROM usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        $conexao->close();
        header("Location: cadastro.php?error=email_exists");
        exit();
    }
    $stmt->close();

    $stmt = $conexao->prepare("INSERT INTO usuario (id, nome, email, senha, setor, nivel_acesso) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $id, $nome, $email, $senha, $setor, $nivel_acesso);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Dados inseridos com sucesso!";
    } else {
        echo "Erro ao inserir dados.";
    }

    $stmt->close();
    $conexao->close();
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="./src/css/estilo.css">
</head>
<body>
    <div class="box">
        <form action="cadastro.php" method="POST">
            <fieldset>
                <legend><b>Cadastro</b></legend>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="error">
                        <?php
                        if ($_GET['error'] == 'id_exists') {
                            echo "ID já existe. Por favor, escolha outro ID.";
                        } elseif ($_GET['error'] == 'email_exists') {
                            echo "E-mail já existe. Por favor, escolha outro e-mail.";
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <br>
                <div class="inputBox">
                    <input placeholder="Matricula" type="number" name="id" id="id" class="inputUser" required>
                </div>
                <br><br>
                <div class="inputBox">
                    <input placeholder="Nome completo" type="text" name="nome" id="nome" class="inputUser" required>
                </div>
                <br><br>
                <div class="inputBox">
                    <input placeholder="Email" type="text" name="email" id="email" class="inputUser" required>
                </div>
                <br><br>
                <div class="inputBox">
                    <input placeholder="Senha" type="password" name="senha" id="senha" class="inputUser" required>
                </div>
                <br><br>
                <div class="inputBox">
                    <select name="setor" id="setor" class="inputUser" required>
                        <option value="tecnologia">Tecnologia</option>
                        <option value="Eletrica">Eletrica</option>
                        <option value="Climatizacao">Climatização</option>
                        <option value="manutenção predial">Manutenção predial</option>
                        <option value="hidraulica">Hidraulica</option>
                        <option value="DP">DP</option>
                        <option value="Segurança de trabalho">Segurança de trabalho</option>
                        <option value="Financeiro">Financeiro</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Artes">Artes</option>
                        <option value="Almoxerifado">Almoxerifado</option>
                        <option value="Enfermaria">Enfermaria</option>
                        <option value="Bombeiros">Bombeiros</option>
                    </select>
                </div>
                <p>Nivel de acesso:</p>
                <label>
                    <input type="radio" id="administrador" name="nivel_acesso" value="administrador" required> Administrador
                </label>
                <br>
                <label>
                    <input type="radio" id="gestor" name="nivel_acesso" value="gestor" required> Gestor
                </label>
                <br>
                <label>
                    <input type="radio" id="usuario" name="nivel_acesso" value="usuario" required> Usuario
                </label>
                <br><br>
                <input type="submit" name="submit" id="submit">
            </fieldset>
        </form>
    </div>
</body>
</html>
