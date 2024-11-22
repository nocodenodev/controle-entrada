<?php
$host = 'localhost';
$dbname = 'controle_escolar';
$user = 'root';
$password = '';

session_start();

try {
    // Conexão inicial sem banco de dados para criar o banco, caso não exista
    $pdo = new PDO("mysql:host=$host", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cria o banco de dados se não existir
    $queryCreateDB = "CREATE DATABASE IF NOT EXISTS $dbname";
    $pdo->exec($queryCreateDB);

    // Conecta ao banco recém-criado
    $pdo->exec("USE $dbname");

    // Query para criar a tabela 'alunos' se não existir
    $queryAlunos = "
        CREATE TABLE IF NOT EXISTS alunos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            RM INT NOT NULL,
            email VARCHAR(100) NOT NULL,
            senha VARCHAR(255) NOT NULL,
            ultima_entrada DATETIME DEFAULT NULL
        )
    ";
    $pdo->exec($queryAlunos);

    // Query para criar a tabela 'entrada' se não existir
    $queryEntrada = "
        CREATE TABLE IF NOT EXISTS entrada (
            id INT AUTO_INCREMENT PRIMARY KEY,
            aluno_id INT,
            data_entrada DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE
        )
    ";
    $pdo->exec($queryEntrada);

    // echo "Banco de dados e tabelas verificadas/criadas com sucesso.";
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>