<?php
// Configura��o do banco de dados
$servername = "localhost";  // Altere caso precise
$username = "fivein68_jv";  // Seu usu�rio do banco de dados
$password = "idioteque225";    // Sua senha do banco de dados
$dbname = "fivein68_Leads";   // O nome do banco de dados

// Cria��o da conex�o com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conex�o
if ($conn->connect_error) {
    die("Conex�o falhou: " . $conn->connect_error);
}

// Definindo o tipo de resposta como JSON
header('Content-Type: application/json');

// Roteamento da API
$request_method = $_SERVER["REQUEST_METHOD"];

// Fun��es para as rotas da API
switch($request_method) {
    case 'GET':
        getDados();
        break;
    case 'POST':
        postDados();
        break;
    default:
        echo json_encode(["message" => "M�todo n�o permitido"]);
        break;
}

// Fun��o para GET (Recupera dados)
function getDados() {
    global $conn;
    $sql = "SELECT * FROM Leads";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $dados = array();
        while($row = $result->fetch_assoc()) {
            $dados[] = $row;
        }
        echo json_encode($dados);
    } else {
        echo json_encode([]);
    }
}

// Fun��o para POST (Adiciona dados)
function postDados() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    $nome = $data['nome'];
    $idade = $data['idade'];

    // Preparar a consulta para evitar SQL Injection
    $stmt = $conn->prepare("INSERT INTO Leads (nome, idade) VALUES (?, ?)");
    $stmt->bind_param("si", $nome, $idade);  // 's' para string, 'i' para inteiro

    if ($stmt->execute()) {
        echo json_encode(["message" => "Dado inserido com sucesso"]);
    } else {
        echo json_encode(["message" => "Erro ao inserir dado: " . $stmt->error]);
    }

    $stmt->close();
}

// Fun��o para PUT (Atualiza dados)
function putDados() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'];
    $nome = $data['nome'];
    $idade = $data['idade'];

    // Preparar a consulta para evitar SQL Injection
    $stmt = $conn->prepare("UPDATE Leads SET nome=?, idade=? WHERE id=?");
    $stmt->bind_param("sii", $nome, $idade, $id);  // 's' para string, 'i' para inteiro

    if ($stmt->execute()) {
        echo json_encode(["message" => "Dado atualizado com sucesso"]);
    } else {
        echo json_encode(["message" => "Erro ao atualizar dado: " . $stmt->error]);
    }

    $stmt->close();
}

// Fechar conex�o
$conn->close();
?>
