<?php
require 'conexao.php';
require 'vendor/autoload.php'; // Carrega o autoload do Composer para a biblioteca QR Code

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// Busca informações do usuário no banco de dados
$query = $pdo->prepare("SELECT nome, RM, email FROM alunos WHERE id = :id");
$query->execute(['id' => $_SESSION['usuario_id']]);
$usuario = $query->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit;
}

// Configurações para o QR Code
$options = new QROptions([
    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
    'eccLevel' => QRCode::ECC_L,
    'imageBase64' => true, // Para facilitar a exibição diretamente no HTML
    'scale' => 128,
]);

// Gera o QR Code com o RM do usuário
$qrcode = (new QRCode($options))->render($usuario['RM']);
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="assets/css/styles.css" />
    <title>Credential - aluno</title>
  </head>
  <body>
    <div class="container">
        <div class="card-content">
         <div class="card-fita">
            <img src="assets/img/faixa.svg" alt="" >
         </div>

          <header class="card-header">
            <span>Credencial</span>

            <div class="card-credential">
               <span>RM: <?php echo htmlspecialchars($usuario['RM']); ?></span>
            </div>
          </header>

          <div class="card-data">
            <div class="card-image">
              <div class="card-mask">
                <img
                  src="assets/img/user.png"
                  alt="profile Picture"
                  class="card-img"
                />
              </div>
            </div>
            
            <h2 class="card-name"><?php echo htmlspecialchars($usuario['nome']); ?></h2>
            <h3 class="card-profession">Aluno</h3>
            
            <div class="card-qrcode">
                <!-- Exibe o QR Code do RM do usuário -->
              <img id="qrcode" src="<?php echo $qrcode; ?>" alt="QR Code do RM">
            </div>

            <a href="entradas.php?id=<?php echo $_SESSION['usuario_id'] ?>" class="card-button">
              <img src="assets/img/see.svg"></img> 
              <span>Ver histórico</span>
            </a>
          </div>
        </div>
    </div>
  </body>
</html>

