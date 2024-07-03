<?php
    session_start();
    include_once('./config/conexao.php');

    if((!isset($_SESSION['email']) == true) and (!isset($_SESSION['senha']) == true)){
        unset($_SESSION['email']);
        unset($_SESSION['senha']);
        header('Location: login.php');
    }
    $logado = $_SESSION['email'];

    // Obter o nível de acesso do usuário logado
    $stmt = $conexao->prepare("SELECT id, nivel_acesso FROM usuario WHERE email = ?");
    $stmt->bind_param('s', $_SESSION['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $nivel_acesso = $user['nivel_acesso'];

    // Gera e verifica o token CSRF
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    if (isset($_POST['submit'])) {
        // Verifica se todos os campos obrigatórios estão preenchidos e se o token CSRF é válido
        if (empty($_POST['titulo']) || empty($_POST['descricao']) || empty($_POST['prioridade']) || empty($_POST['local']) || empty($_POST['data_entrega']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['message'] = "Todos os campos são obrigatórios ou o token CSRF é inválido.";
        } else {
            // Obtém e sanitiza os dados do formulário
            $titulo = htmlspecialchars($_POST['titulo'], ENT_QUOTES, 'UTF-8');
            $descricao = htmlspecialchars($_POST['descricao'], ENT_QUOTES, 'UTF-8');
            $prioridade = htmlspecialchars($_POST['prioridade'], ENT_QUOTES, 'UTF-8');
            $local = htmlspecialchars($_POST['local'], ENT_QUOTES, 'UTF-8');
            $email_usuario = $_SESSION['email'];
            $data_entrega = $_POST['data_entrega'];

            // Prepara e executa a inserção dos dados no banco de dados
            $stmt = $conexao->prepare("INSERT INTO tickets (titulo, descricao, prioridade, local, email_usuario, data_entrega) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $titulo, $descricao, $prioridade, $local, $email_usuario, $data_entrega);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Operação concluida!";
            } else {
                $_SESSION['message'] = "Erro ao inserir dados.";
            }

            $stmt->close();
            header('Location: chamados.php');
            exit;
        }
    }

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./src/css/sidebar.css">
    <title>Sistema</title>
</head>
<body>
    
    <?php include('./view/navbar.php'); ?>

    <main>
        <?php
        if (isset($_SESSION['message'])) {
            echo "<p>" . htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') . "</p>";
            unset($_SESSION['message']);
        }
        ?>
        <div class="box">
        <h3>Cadastro de chamados</h3>
            <form action="chamados.php" method="POST" id="taskForm">
                <div class="inputBox">
                    <label for="titulo">Nome do chamado:</label>
                    <input placeholder="Título" type="text" name="titulo" id="titulo" class="inputUser" required>
                </div>
                <div class="inputBox">
                    <label for="descricao">Descrição do chamado:</label>
                    <textarea placeholder="Descrição" name="descricao" id="descricao" class="inputUser" required></textarea>
                </div>
                <div class="priority">
                    <p>Prioridade:</p>
                    <label>
                        <input type="radio" id="baixa" name="prioridade" value="baixa" required> Baixa
                    </label>
                    <label>
                        <input type="radio" id="media" name="prioridade" value="media" required> Média
                    </label>
                    <label>
                        <input type="radio" id="alta" name="prioridade" value="alta" required> Alta
                    </label>
                </div>
                <br>
                <div class="inputBox">
                    <label for="local">Local:</label>
                    <select name="local" id="local" class="inputUser" required>
                        <option value="Central patrimonio">Central patrimônio</option>
                        <option value="Central estacionamento">Central estacionamento</option>
                        <option value="Refeitório limpeza">Refeitório limpeza</option>
                        <option value="Refeitório Patrimônio">Refeitório Patrimônio</option>
                        <option value="Guarita estacionamento funcionários">Guarita estacionamento funcionários</option>
                        <option value="Cancelas de estacionamento geral">Cancelas de estacionamento geral</option>
                        <option value="Estacionamento Uber">Estacionamento Uber</option>
                    </select>
                </div>
                <div class="inputBox">
                    <label for="data_entrega"><b>Data de entrega:</b></label>
                    <input type="date" name="data_entrega" id="data_entrega" class="inputUser" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="submit" name="submit" id="submit" value="Enviar">
            </form>
        </div>
    </main>
    <script src="./src/js/script.js"></script>
    
</body>
</html>
