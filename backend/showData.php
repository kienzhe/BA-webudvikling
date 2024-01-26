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

$userEmail = $data->userEmail;

$resultUserId = $conn->query("SELECT id FROM User WHERE email = '$userEmail'");

if ($resultUserId && $resultUserId->num_rows > 0) {
    $userData = $resultUserId->fetch_assoc();
    $userId = $userData['id'];

    $combinedDataArray = [];
    $resultRegistration = $conn->query("SELECT serial_num FROM registration WHERE User_id = '$userId'");

    while ($rowRegistration = $resultRegistration->fetch_assoc()) {
        $serialNum = $rowRegistration['serial_num'];
        $droneData = [];

        $resultDrone = $conn->query("SELECT format, ua, uas FROM Drone WHERE serial_number = '$serialNum'");

        if ($resultDrone->num_rows > 0) {
            $droneData = $resultDrone->fetch_assoc();
        }

        $combinedDataArray[] = array_merge(['serial_number' => $serialNum], $droneData);
    }

    echo json_encode(['success' => true, 'combinedDataArray' => $combinedDataArray]);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}

$conn->close();
?>