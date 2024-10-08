<?php
    session_start();
    include_once('./config/conexao.php');

    if ((!isset($_SESSION['email'])) && (!isset($_SESSION['senha']))) {
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
    $logged_in_user_id = $user['id'];

    $pagina = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT);
    if (!$pagina) {
        $pagina = 1;
    }
    $limite = 10;
    $inicio = ($pagina - 1) * $limite;

    if (!empty($_GET['search'])) {
        $data = '%' . filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING) . '%';
        $sql = "
        SELECT u.id, u.nome, u.email, u.setor, u.nivel_acesso, COUNT(t.id) AS chamados
        FROM usuario u
        LEFT JOIN tickets t ON u.email = t.email_usuario
        WHERE u.id LIKE ? OR u.nome LIKE ? OR u.email LIKE ? OR u.setor LIKE ? OR u.nivel_acesso LIKE ?
        GROUP BY u.id, u.nome, u.email, u.setor, u.nivel_acesso
        ORDER BY u.id DESC
        LIMIT ? OFFSET ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param('ssssssi', $data, $data, $data, $data, $data, $limite, $inicio);
    } else {
        $sql = "
        SELECT u.id, u.nome, u.email, u.setor, u.nivel_acesso, COUNT(t.id) AS chamados
        FROM usuario u
        LEFT JOIN tickets t ON u.email = t.email_usuario
        GROUP BY u.id, u.nome, u.email, u.setor, u.nivel_acesso
        ORDER BY u.id DESC
        LIMIT ? OFFSET ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param('ii', $limite, $inicio);
    }

    if (!$stmt) {
        die("Erro na preparação da consulta: " . $conexao->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        die("Erro na execução da consulta: " . $conexao->error);
    }

    $sqlCount = "SELECT COUNT(*) as total FROM usuario";
    $stmtCount = $conexao->prepare($sqlCount);
    $stmtCount->execute();
    $resultRegistros = $stmtCount->get_result();
    if (!$resultRegistros) {
        die("Erro na contagem de registros: " . $conexao->error);
    }
    $totalRegistros = $resultRegistros->fetch_assoc()['total'];
    $paginas = ceil($totalRegistros / $limite);
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
        <div class="box-search">
            <input type="search" id="search" placeholder="Pesquisar" class="search">
            <button onclick="searchData()">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
        <br>
        <div class="container">
            <h1>Usuários</h1>
            <br>
            <table class="content-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Setor</th>
                        <th>Nível de Acesso</th>
                        <th>Qtd. Chamados</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user_data = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user_data['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($user_data['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($user_data['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($user_data['setor'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($user_data['nivel_acesso'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($user_data['chamados'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php if ($nivel_acesso == 'administrador'): ?>
                                    <?php if ($user_data['id'] != $logged_in_user_id): ?>
                                        <a href='./controller/userController.php?id=<?= $user_data['id'] ?>'>atualizar</a>
                                        <a href='./controller/userDelete.php?id=<?= $user_data['id'] ?>'>deletar</a>
                                        <?php else:?>
                                            <a href="#">atualizar</a>
                                            <a href="#">deletar</a>
                                    <?php endif;?>
                                    <?php else:?>
                                        <a href="#">atualizar</a>
                                        <a href="#">deletar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="pagination">
                <a href="?pagina=1">Primeira</a>
                <?php if ($pagina > 1): ?>
                    <a href="?pagina=<?php echo $pagina - 1; ?>">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                <?php echo $pagina; ?>
                <?php if ($pagina < $paginas): ?>
                    <a href="?pagina=<?php echo $pagina + 1; ?>">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
                <a href="?pagina=<?php echo $paginas; ?>">Última</a>
            </div>
        </div>
    </main>
    <script src="./src/js/script.js"></script>
    <script>
       document.addEventListener('DOMContentLoaded', function() {
            let search = document.getElementById('search');

            if (search) {
                search.addEventListener("keydown", function(event) {
                    if (event.key === "Enter") {
                        searchData();
                    }
                });
            }

            function searchData() {
                window.location = 'users.php?search=' + encodeURIComponent(search.value);
            }
    });

    </script>
</body>
</html>
