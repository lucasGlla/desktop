<?php 

if(!empty($_GET['id'])){
    include_once('../config/conexao.php');

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    $stmt = $conexao->prepare("SELECT * FROM usuario WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        while($user_data = mysqli_fetch_assoc($result)){
         $email = $user_data['email'];
         $setor = $user_data['setor'];
         $nivel_acesso = $user_data['nivel_acesso'];
    } 
}else{
    header('Location: ../users.php');
}
}else{
    header('Location: ../users.php');
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
        <form action="userUpdate.php" method="POST">
        <fieldset>
                <legend><b>Atualizar usuario</b></legend>
            
            <br>
            <div class="inputBox">
                <input placeholder="email" type="text" name="email" id="email" class="inputUser" value="<?php echo $email ?>" required>
            </div>
            <br><br>
            <div class="inputBox">
                <select name="setor" id="setor" class="inputUser" required>
                    <option value="tecnologia" <?php echo ($setor == 'tecnologia') ? 'selected' : '' ?>>Tecnologia</option>
                    <option value="Eletrica" <?php echo ($setor == 'Eletrica') ? 'selected' : '' ?>>Eletrica</option>
                    <option value="Climatizacao" <?php echo ($setor == 'Climatizacao') ? 'selected' : '' ?>>Climatização</option>
                    <option value="manutenção predial" <?php echo ($setor == 'manutenção predial') ? 'selected' : '' ?>>Manutenção predial</option>
                    <option value="hidraulica" <?php echo ($setor == 'hidraulica') ? 'selected' : '' ?>>Hidráulica</option>
                    <option value="DP" <?php echo ($setor == 'DP') ? 'selected' : '' ?>>DP</option>
                    <option value="Segurança de trabalho" <?php echo ($setor == 'Segurança de trabalho') ? 'selected' : '' ?>>Segurança de trabalho</option>
                    <option value="Financeiro" <?php echo ($setor == 'Financeiro') ? 'selected' : '' ?>>Financeiro</option>
                    <option value="Marketing" <?php echo ($setor == 'Marketing') ? 'selected' : '' ?>>Marketing</option>
                    <option value="Artes" <?php echo ($setor == 'Artes') ? 'selected' : '' ?>>Artes</option>
                    <option value="Almoxerifado" <?php echo ($setor == 'Almoxerifado') ? 'selected' : '' ?>>Almoxerifado</option>
                    <option value="Enfermaria" <?php echo ($setor == 'Enfermaria') ? 'selected' : '' ?>>Enfermaria</option>
                    <option value="Bombeiros" <?php echo ($setor == 'Bombeiros') ? 'selected' : '' ?>>Bombeiros</option>
                </select>
            </div>
            <br><br>
            <p>Nivel de acesso:</p>
                    <label>
                        <input type="radio" id="administrador" name="nivel_acesso" value="administrador" <?php echo ($nivel_acesso == 'administrador') ? 'checked' : '' ?>required> Administrador
                    </label>
                    <br>
                    <label>
                        <input type="radio" id="gestor" name="nivel_acesso" value="gestor"<?php echo ($nivel_acesso == 'gestor') ? 'checked' : '' ?> required> Gestor
                    </label>
                    <br>
                    <label>
                        <input type="radio" id="usuario" name="nivel_acesso" value="usuario"<?php echo ($nivel_acesso == 'usuario') ? 'checked' : '' ?> required> Usuario
                    </label>
            
 
            <br><br><br>
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <input type="submit" name="update" id="update">
            </fieldset>
        </form>
    </div>
    
</body>
</html>