<?php
// Conectar ao banco de dados
require 'conexao.php';
header('Content-Type: application/json');

// Função para verificar se o RM existe
function rmExists($rm) {
    global $pdo;

    try {
        $query = $pdo->prepare("SELECT * FROM alunos WHERE RM = :rm");
        $query->execute(['rm' => $rm]);

        return $query->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Erro ao verificar se o RM $rm existe: " . $e->getMessage() . "\n", 3, "debug.log");
        return false;
    }
}

// Função para verificar se a entrada é permitida
function canRegisterEntry($rm) {
    global $pdo;

    try {
        $query = $pdo->prepare("SELECT ultima_entrada FROM alunos WHERE RM = :rm");
        $query->execute(['rm' => $rm]);
        $aluno = $query->fetch(PDO::FETCH_ASSOC);

        // Se o aluno não tem uma entrada registrada, permite o registro
        if (!$aluno || !$aluno['ultima_entrada']) {
            return true;
        }

        $ultimaEntrada = new DateTime($aluno['ultima_entrada']);
        $agora = new DateTime(); // Horário atual
        $limiteHoje = new DateTime(); // Define o limite de hoje às 07:30
        $limiteHoje->setTime(7, 30);

        // Caso a última entrada seja no mesmo dia e após o horário limite
        if ($ultimaEntrada->format('Y-m-d') === $agora->format('Y-m-d')) {
            return false; // Já registrou hoje
        }

        // Caso a última entrada seja no dia anterior e antes do horário limite
        if ($ultimaEntrada->format('Y-m-d') < $agora->format('Y-m-d') && $agora < $limiteHoje) {
            return false; // Ainda não pode registrar porque está antes das 7:30 de hoje
        }

        return true; // Caso contrário, permite o registro
    } catch (Exception $e) {
        error_log("Erro ao verificar entrada: " . $e->getMessage() . "\n", 3, "debug.log");
        return false;
    }
}

// Função para registrar a entrada
function registerEntry($rm) {
    global $pdo;

    // Define o fuso horário para -03:00 (Horário Padrão de Brasília)
    date_default_timezone_set('America/Sao_Paulo');
    $currentTime = date('Y-m-d H:i:s');

    error_log("Tentando registrar entrada para RM $rm às $currentTime.\n", 3, "debug.log");

    try {
        // Atualiza a última entrada
        $queryAluno = $pdo->prepare("UPDATE alunos SET ultima_entrada = :currentTime WHERE RM = :rm");
        $queryAluno->execute(['currentTime' => $currentTime, 'rm' => $rm]);

        // Registra na tabela entrada
        $queryEntrada = $pdo->prepare("INSERT INTO entrada (aluno_id, data_entrada) 
                                        SELECT id, :currentTime FROM alunos WHERE RM = :rm");
        $queryEntrada->execute(['currentTime' => $currentTime, 'rm' => $rm]);

        error_log("Entrada registrada com sucesso para RM $rm.\n", 3, "debug.log");
        return true;
    } catch (Exception $e) {
        error_log("Erro ao registrar entrada: " . $e->getMessage() . "\n", 3, "debug.log");
        return false;
    }
}

// Checar se o POST contém o RM
if (isset($_POST['rm'])) {
    $rm = $_POST['rm'];
    error_log("Recebido RM: $rm.\n", 3, "debug.log");

    if(!rmExists($rm)) {
        $message = "Entrada não permitida para RM $rm. Esse RM não está cadastrado.";
        error_log($message . "\n", 3, "debug.log");
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }

    // Verificar se a entrada é permitida
    if (!canRegisterEntry($rm)) {
        $message = "Entrada não permitida para RM $rm. Você já registrou sua entrada hoje.";
        error_log($message . "\n", 3, "debug.log");
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }

    // Registrar a entrada
    if (registerEntry($rm)) {
        echo json_encode(['success' => true, 'message' => 'Entrada registrada com sucesso.']);
    } else {
        $message = "Erro ao registrar entrada para RM $rm.";
        error_log($message . "\n", 3, "debug.log");
        echo json_encode(['success' => false, 'message' => $message]);
    }
} else {
    $message = "RM não informado.";
    error_log($message . "\n", 3, "debug.log");
    echo json_encode(['success' => false, 'message' => $message]);
}
?>
