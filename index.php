<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = new SQLite3('requests.db');
$result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='requests'");
if (!$result->fetchArray()) {
    require 'createdb.php';
    $db = new SQLite3('requests.db');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    if (empty($subject) || strlen($subject) > 255) {
        $errors['subject'] = 'Subject is required and must be less than 255 characters.';
    }

    $text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_STRING);
    if (empty($text) || strlen($text) > 4096) {
        $errors['text'] = 'Text is required and must be less than 4096 characters.';
    }

    $priority = filter_input(INPUT_POST, 'priority', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 5]]);
    if ($priority === false) {
        $errors['priority'] = 'Invalid priority selected.';
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (empty($email) || !$email) {
        $errors['email'] = 'Valid email is required.';
    }

    $pin = filter_input(INPUT_POST, 'pin', FILTER_SANITIZE_NUMBER_INT);
    if (strlen($pin) != 4 || !ctype_digit($pin)) {
        $errors['pin'] = 'PIN must be a 4 digit number.';
    }

    if (empty($errors)) {

        $stmt = $db->prepare("INSERT INTO requests (subject, text, priority, email, pin) VALUES (:subject, :text, :priority, :email, :pin)");
        $stmt->bindValue(':subject', $subject, SQLITE3_TEXT);
        $stmt->bindValue(':text', $text, SQLITE3_TEXT);
        $stmt->bindValue(':priority', $priority, SQLITE3_INTEGER);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':pin', $pin, SQLITE3_TEXT);
        $stmt->execute();

        $requestId = $db->lastInsertRowID();

        setcookie("request_$requestId", md5($pin), time() + (86400 * 30));

        header("Location: ?id=$requestId");
        exit();
    } else {
        echo "Errors detected:<br>";
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
        include 'form.php';
    }
} elseif (isset($_GET['id'])) {

    $requestId = intval($_GET['id']);
    $query = $db->query("SELECT * FROM requests WHERE id = $requestId");
    $request = $query->fetchArray(SQLITE3_ASSOC);
    if ($request) {
        echo "<h2>Данные вашей заявки:</h2>";
        echo "<p><strong>Тема:</strong> " . htmlspecialchars($request['subject']) . "</p>";
        echo "<p><strong>Текст:</strong> " . htmlspecialchars($request['text']) . "</p>";
        echo "<p><strong>Приоритет:</strong> " . htmlspecialchars($request['priority']) . "</p>";
        echo "<p><strong>Электронная почта:</strong> " . htmlspecialchars($request['email']) . "</p>";
        exit();
    } else {
        echo "Запрос не найден.";
    }
} else {
    if (!file_exists('form.php')) {
        echo "form.php not found!";
        exit();
    } else {
        include 'form.php';
    }
}
?>
