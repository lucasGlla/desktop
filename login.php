<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="./src/css/estilo.css">
</head>
<body>
    <div class="box">
    <form action="./controller/testeLogin.php" method="POST">
        <fieldset>
        <h1>Login</h1>
        <div class="inputBox">
                <input placeholder="Email" type="text" name="email" id="email" class="inputUser" required>
            </div>
        <br><br>
        <div class="inputBox">
                <input placeholder="Senha" type="password" name="senha" id="senha" class="inputUser" required>
            </div>
        <br><br>
        <input type="submit" name="submit" id="submit">
        </fieldset>
    </form>
  </div>
</body>
</html>