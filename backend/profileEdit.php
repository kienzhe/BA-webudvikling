<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');


include 'conn/dbCon.php';

$data = json_decode(file_get_contents("php://input"));
error_log(print_r($data, true));

// Brug de korrekte navne baseret på frontendens forventninger
$userEmail = $data->userEmail; // Antag, at du sender brugerens e-mail fra frontend
$resultUser = $conn->query("SELECT * FROM User WHERE email = '$userEmail'");


if ($resultUser && $resultUser->num_rows > 0) {
    $userData = $resultUser->fetch_assoc();
    $userId = $userData['id'];

    if (isset($data->first_name)) {
        $FirstName = $data->first_name;
        $LastName = $data->last_name;
        $updateQuery = "UPDATE User SET first_name = '$FirstName', last_name = '$LastName' WHERE id = '$userId'";
        $conn->query($updateQuery);

        if ($conn->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'First name updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update first name']);
        }
    }
    
    if (isset($data->currentPassword) && isset($data->newPassword)) {
        $currentPassword = $data->currentPassword;
        $newPassword = $data->newPassword;

        // Check if the current password matches the stored password
        if (password_verify($currentPassword, $userData['password'])) {
            // Hash the new password before updating
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updatePasswordQuery = "UPDATE User SET password = '$hashedPassword' WHERE id = '$userId'";
            $conn->query($updatePasswordQuery);

            if ($conn->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Password updated']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update password']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        }
    }
    
    if (isset($data->newEmail) && isset($data->newPhone)) {
        // Update email and phone number
        $newEmail = $data->newEmail;
        $newPhone = $data->newPhone;
        $updateContactQuery = "UPDATE User SET email = '$newEmail', phone = '$newPhone' WHERE id = '$userId'";
        $conn->query($updateContactQuery);

        if ($conn->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Contact information updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update contact information']);
        }
    }
}

$conn->close();
?>