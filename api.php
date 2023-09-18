<?php
require "connect.php";

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

$endpoint = '/api/v1/users';

$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
    case 'GET':
        // Get all users
        $users = get_users($db);

        // Respond with the users
        header('HTTP/1.1 200 OK');
        echo json_encode($users);
        exit();
        break;
    default:
        // Respond with an error
        header('HTTP/1.1 400 Bad Request');
        echo json_encode('Invalid request method.');
        exit();
        break;
}

// Function to get all users
function get_users($db) {
    @$stm = $db->prepare("SELECT * FROM users");
    $stm->execute();
    return $stm->fetchAll(PDO::FETCH_ASSOC);
}

?>