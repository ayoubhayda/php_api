<?php

// Require the database connection file
require "connect.php";

// Set the HTTP headers
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

// Get the endpoint
$endpoint = '/api/v1/users';

// Get the request method
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
    case 'POST':
        // Add a new user
        $user = json_decode(file_get_contents('php://input'), true);

        // Validate the user data
        if (!validate($user)) {
            // Return a 400 Bad Request error with a specific error message
            header('HTTP/1.1 400 Bad Request');
            echo json_encode('Invalid user data. Please make sure that all of the required fields are present and that the data is valid.');
            exit();
        }

        // Add the new user to the database
        add_user($db, $user);

        // Get all users
        $users = get_users($db);

        // Respond with the users
        header('HTTP/1.1 200 OK');
        echo json_encode($users);
        exit();
        break;

    case 'PUT' || 'PATCH':
        // Update the existing user
        $id = $_GET['id'];
        $user = json_decode(file_get_contents('php://input'), true);

        // Check if the user exists
        if (!check_id($db, $id)) {
            // Return a 400 Bad Request error with a specific error message
            header('HTTP/1.1 400 Bad Request');
            echo json_encode('This user does not exist.');
            exit();
        }

        // Validate the user data
        if (!validate($user)) {
            // Return a 400 Bad Request error with a specific error message
            header('HTTP/1.1 400 Bad Request');
            echo json_encode('Invalid user data. Please make sure that all of the required fields are present and that the data is valid.');
            exit();
        }

        // Update the user in the database
        update_user($db, $user, $id);

        // Get all users
        $users = get_users($db);

        // Respond with the users
        header('HTTP/1.1 200 OK');
        echo json_encode($users);
        exit();
        break;
    case 'DELETE':
        $id = $_GET['id'];

        // Check if the user exists
        if (!check_id($db, $id)) {
            // Return a 400 Bad Request error with a specific error message
            header('HTTP/1.1 400 Bad Request');
            echo json_encode('This user does not exist.');
            exit();
        }

        // Update the user in the database
        delete_user($db, $id);

        // Get all users
        $users = get_users($db);

        // Respond with the users
        header('HTTP/1.1 200 OK');
        echo json_encode($users);
        exit();
        break;
    default:
        // Respond with a 400 Bad Request error with a specific error message
        header('HTTP/1.1 400 Bad Request');
        echo json_encode('Invalid request method.');
        exit();
        break;
}

// --------------------------- Data Check Functions -------------------------------

// Function to validate the user data
function validate($user) {
    
    // Check if all of the required fields are present
    if (!isset($user['name']) || !isset($user['age']) || !isset($user['email'])) {
        return false;
    }

    // Check if the user data is valid
    if (empty($user['name']) || empty($user['age']) || empty($user['email'])) {
        return false;
    }

    return true;
}


// Function to check if the user exists
function check_id($db, $id) {

    $stm = $db->prepare("SELECT EXISTS(SELECT 1 FROM users WHERE id = :id)");
    $stm->bindParam(':id', $id);
    $stm->execute();
    $exists = $stm->fetchColumn();

    return $exists;
}



// --------------------------- CRUD Functions -------------------------------


// Function to get all users
function get_users($db) {

    $stm = $db->prepare("SELECT * FROM users");
    $stm->execute();
    $users = $stm->fetchAll(PDO::FETCH_ASSOC);

    // Return the users
    return $users;
}

// Function to add a new user
function add_user($db, $user) {
    
    $stm = $db->prepare("INSERT INTO `users`(`name`, `age`, `email`) VALUES (:name, :age, :email)");

    $stm->bindParam(':name', $user['name']);
    $stm->bindParam(':age', $user['age']);
    $stm->bindParam(':email', $user['email']);

    $stm->execute();
}

// Function to update an existing user in the database
function update_user($db, $user, $id) {
    
    $stm = $db->prepare("UPDATE users SET name = :name, age = :age, email = :email WHERE id = $id");

    $stm->bindParam(':name', $user['name']);
    $stm->bindParam(':age', $user['age']);
    $stm->bindParam(':email', $user['email']);

    $stm->execute();
}

// Function to delete an existing user from the database
function delete_user($db, $id) {
    $stm = $db->prepare("DELETE FROM users WHERE id = :id");
    $stm->bindParam(':id', $id);
    $stm->execute();
}

?>