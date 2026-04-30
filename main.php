<?php
header("Content-Type: application/json");

$host = "localhost";
$db = "notes_db";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(["error" => "Connessione fallita"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];


if ($method == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['title']) || !isset($data['content'])) {
        echo json_encode(["error" => "Dati mancanti"]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO notes (title, content) VALUES (?, ?)");
    $stmt->execute([$data['title'], $data['content']]);

    echo json_encode(["message" => "Nota creata"]);
}


elseif ($method == "GET") {

    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $note = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode($note);
    } else {
        $stmt = $pdo->query("SELECT * FROM notes");
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($notes);
    }
}


elseif ($method == "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        echo json_encode(["error" => "ID mancante"]);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ?");
    $stmt->execute([$data['title'], $data['content'], $data['id']]);

    echo json_encode(["message" => "Nota aggiornata"]);
}


elseif ($method == "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        echo json_encode(["error" => "ID mancante"]);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
    $stmt->execute([$data['id']]);

    echo json_encode(["message" => "Nota eliminata"]);
}

else {
    echo json_encode(["error" => "Il metodo non e supportato"]);
}
?>