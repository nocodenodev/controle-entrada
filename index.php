<?php
require 'conexao.php';
// Função para criar hash SHA-256
function hashSenha($senha) {
    return hash('sha256', $senha);
}

// Cadastro de novo usuário
if (isset($_POST['cadastro'])) {
    $nome = $_POST['nome'];
    $rm = $_POST['rm'];
    $email = $_POST['email'];
    $senha = hashSenha($_POST['senha']);

    // Verificar se o RM ou email já está cadastrado
    $query = $pdo->prepare("SELECT * FROM alunos WHERE RM = :rm OR email = :email");
    $query->execute(['rm' => $rm, 'email' => $email]);

    if ($query->rowCount() > 0) {
        echo "<script>alert('RM ou Email já cadastrado!');</script>";
    } else {
        // Inserir novo usuário
        $stmt = $pdo->prepare("INSERT INTO alunos (nome, RM, email, senha) VALUES (:nome, :rm, :email, :senha)");
        $stmt->execute(['nome' => $nome, 'rm' => $rm, 'email' => $email, 'senha' => $senha]);
        echo "<script>alert('Cadastro realizado com sucesso!');</script>";
    }
}

// Login do usuário
if (isset($_POST['login'])) {
    $email = $_POST['login_email'];
    $senha = hashSenha($_POST['login_senha']);

    $query = $pdo->prepare("SELECT * FROM alunos WHERE email = :email AND senha = :senha");
    $query->execute(['email' => $email, 'senha' => $senha]);

    if ($query->rowCount() > 0) {
        // Obtém os dados do usuário
        $usuario = $query->fetch(PDO::FETCH_ASSOC);

        // Salva as informações do usuário na sessão
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_rm'] = $usuario['RM'];

        echo "<script>alert('Login realizado com sucesso!');</script>";
        // Redireciona para a página inicial ou dashboard
        echo "<script>window.location.href = 'card.php';</script>";
    } else {
        echo "<script>alert('Email ou senha incorretos!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Cadastro e Login</title>
    <link rel="stylesheet" href="assets/css/cadastro.css" />
  </head>
  <body>
   <section class="container">

      <header>Cadastro</header>
      <form method="post" class="form">
        <div class="input-box">
            <label>Nome</label>
            <input type="text" name="nome" placeholder="Digite seu nome" required />
        </div>
        <div class="column">
            <div class="input-box">
                <label>RM</label>
                <input type="number" name="rm" placeholder="Digite seu RM" required />
            </div>
            <div class="input-box">
                <label>Email</label>
                <input type="email" name="email" placeholder="Digite seu email" required />
            </div>
        </div>
        <div class="input-box">
            <label>Senha</label>
            <input type="password" name="senha" placeholder="Digite sua senha" required />
        </div>

        <button name="cadastro">Enviar</button>
        <button onclick="window.location.href='scanner.php'">Ir para o scanner</button>
      </form>

      <button 
        class="login-link" 
        onclick="
          document.querySelectorAll('.container').forEach( (e) => 
          e.classList.toggle('hide')
          );
        ">
        Já possui cadastro?
      </button>
    </section>
    <section class="container hide">
      <header>Login</header>
      <form method="post" class="form">
        <div class="input-box">
          <label>Email</label>
          <input type="text" name="login_email" placeholder="Digite seu email" required />
        </div>
        <div class="input-box">
          <label>Senha</label>
          <input type="password" name="login_senha" placeholder="Digite sua senha" required />
        </div>
        <button name="login">Entrar</button>
      </form>

      <button 
        class="login-link" 
        onclick="
          document.querySelectorAll('.container').forEach( (e) => 
          e.classList.toggle('hide')
          );
        ">
        Não possui cadastro?
      </button>
    </section>
  </body>
</html>