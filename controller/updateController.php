<?php 

if(!empty($_GET['id'])){
    include_once('../config/conexao.php');

    $id = $_GET['id'];

    $sqlSelect = "SELECT * FROM tickets WHERE id=$id";

    $result = $conexao->query($sqlSelect);

    if($result->num_rows > 0){
    while($user_data = mysqli_fetch_assoc($result)){
    $titulo = $user_data['titulo'];
    $descricao = $user_data['descricao'];
    $estado = $user_data['estado'];
    $prioridade = $user_data['prioridade'];
    $data_entrega = isset($_POST['data_entrega']) ? $_POST['data_entrega'] : '';
    } 
}else{
    header('Location: ../relatorio.php');
}
}else{
    header('Location: ../relatorio.php');
}

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="../src/css/estilo.css">
</head>
<body>
    <div class="box">
        <form action="saveUpdate.php" method="POST">
        <fieldset>
                <legend><b>Atualizar chamada</b></legend>
            
            <br>
            <div class="inputBox">
                <input placeholder="Titulo" type="text" name="titulo" id="titulo" class="inputUser" value="<?php echo $titulo ?>" required>
            </div>
            <br><br>
            <div class="inputBox">
                <input placeholder="descricao" type="text" name="descricao" id="descricao" class="inputUser" value="<?php echo $descricao ?>" required>
            </div>
            <br><br>
            <p>estado:</p>
            <input type="radio" id="aberto" name="estado" value="aberto" <?php echo ($estado == 'aberto') ? 'checked' : '' ?> required>
            <label for="aberto">Aberto</label>
            <br>
            <input type="radio" id="fechado" name="estado" value="fechado" <?php echo ($estado == 'fechado') ? 'checked' : '' ?> required>
            <label for="fechado">Fechado</label>
            <br><br>
            <p>Prioridade:</p>
            <input type="radio" id="baixa" name="prioridade" value="baixa" <?php echo ($prioridade == 'baixa') ? 'checked' : '' ?> required>
            <label for="baixa">Baixa</label>
            <br>
            <input type="radio" id="media" name="prioridade" value="media" <?php echo ($prioridade == 'media') ? 'checked' : '' ?> required>
            <label for="media">Media</label>
            <br>
            <input type="radio" id="alta" name="prioridade" value="alta" <?php echo ($prioridade == 'alta') ? 'checked' : '' ?> required>
            <label for="alta">Alta</label>
            <br><br>
            <br><br>
            <label for="data_entrega"><b>Data e hora de entrega:</b></label>
            <input type="date" name="data_entrega" id="data_entrega" class="inputUser" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $data_entrega;?>" required>
            <br><br><br>
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <input type="submit" name="update" id="update">
            </fieldset>
        </form>
    </div>
    
</body>
</html>