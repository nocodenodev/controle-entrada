<?php
require 'conexao.php';

// Busca os registros de entrada dos alunos no banco de dados
if (isset($_GET['id'])) {
    $query = $pdo->prepare("
        SELECT alunos.nome, alunos.RM, entrada.data_entrada
        FROM entrada
        JOIN alunos ON entrada.aluno_id = alunos.id
        WHERE alunos.id = :id
        ORDER BY entrada.data_entrada DESC
    ");
    $query->execute(['id' => $_GET['id']]);
} else {
    $query = $pdo->prepare("
        SELECT alunos.nome, alunos.RM, entrada.data_entrada
        FROM entrada
        JOIN alunos ON entrada.aluno_id = alunos.id
        ORDER BY entrada.data_entrada DESC
    ");
    $query->execute();
}

$entradas = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
    <?php echo isset($_GET['id']) ? 'Registro de Entradas' : 'Registro de Entrada dos Alunos'; ?>
  </title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <style>
    /* Adiciona estilo para esconder a coluna de nome quando necessário */
    .hide-column .nome-aluno {
      display: none;
    }
  </style>
</head>
<body>
  <div class="container">
    
    <table class="<?php echo isset($_GET['id']) ? 'hide-column' : ''; ?>">
      <caption>
        <?php echo isset($_GET['id']) ? 'Registro de Entradas' : 'Registro de Entrada dos Alunos'; ?>
      </caption>
      <thead>
        <tr>
          <?php if (!isset($_GET['id'])): ?>
            <th>Nome do Aluno</th>
          <?php endif; ?>
          <th>RM</th>
          <th>Horário de Entrada</th>
          <th>Data de Entrada</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($entradas) > 0): ?>
          <?php foreach ($entradas as $entrada): ?>
            <?php
              // Formata a data e hora para exibição
              $dataHora = new DateTime($entrada['data_entrada']);
              $data = $dataHora->format('d/m/Y');
              $hora = $dataHora->format('H:i');
            ?>
            <tr>
              <?php if (!isset($_GET['id'])): ?>
                <td data-label="Nome do Aluno"><?php echo htmlspecialchars($entrada['nome']); ?></td>
              <?php endif; ?>
              <td data-label="RM"><?php echo htmlspecialchars($entrada['RM']); ?></td>
              <td data-label="Horário de Entrada"><?php echo $hora; ?></td>
              <td data-label="Data de Entrada"><?php echo $data; ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4">Nenhum registro encontrado.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    
  </div>
</body>
</html>
