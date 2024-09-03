<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/json; charset=utf-8');

require 'meetingsModel.php';
$meetingsModel = new meetingsModel();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $response = (!isset($_GET['id'])) ? $meetingsModel->getMeetings() : $meetingsModel->getMeetings($_GET['id']);
        error_log('GET Response: ' . print_r($response, true)); 
        echo json_encode($response);
        break;

    case 'POST':
        $_POST = json_decode(file_get_contents('php://input'), true);
        error_log('POST Data: ' . print_r($_POST, true));

        if (!isset($_POST['reunion']) || empty(trim($_POST['reunion'])) || strlen($_POST['reunion']) > 80) {
            $response = ['error' => 'El nombre de la reunión no debe estar vacío y no debe tener más de 80 caracteres'];
        } else if (!isset($_POST['descripcion']) || empty(trim($_POST['descripcion'])) || strlen($_POST['descripcion']) > 150) {
            $response = ['error' => 'La descripción de la reunión no debe estar vacía y no debe tener más de 150 caracteres'];
        } else if (!isset($_POST['fecha']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['fecha'])) {
            $response = ['error' => 'La fecha debe estar en formato YYYY-MM-DD'];
        } else if (!isset($_POST['hora']) || !preg_match('/^\d{2}:\d{2}:\d{2}$/', $_POST['hora'])) {
            $response = ['error' => 'La hora debe estar en formato HH:MM:SS'];
        } else {
            $response = $meetingsModel->saveMeetings($_POST['reunion'], $_POST['descripcion'], $_POST['fecha'], $_POST['hora']);
        }
        error_log('POST Response: ' . print_r($response, true));
        echo json_encode($response);
        break;

    case 'PUT':
        $_PUT = json_decode(file_get_contents('php://input'), true);
        error_log('PUT Data: ' . print_r($_PUT, true)); 

        if (!isset($_PUT['id']) || empty(trim($_PUT['id']))) {
            $response = ['error' => 'El ID de la reunión no debe estar vacío'];
        } else if (!isset($_PUT['reunion']) || empty(trim($_PUT['reunion'])) || strlen($_PUT['reunion']) > 80) {
            $response = ['error' => 'El nombre de la reunión no debe estar vacío y no debe tener más de 80 caracteres'];
        } else if (!isset($_PUT['descripcion']) || empty(trim($_PUT['descripcion'])) || strlen($_PUT['descripcion']) > 150) {
            $response = ['error' => 'La descripción de la reunión no debe estar vacía y no debe tener más de 150 caracteres'];
        } else if (!isset($_PUT['fecha']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_PUT['fecha'])) {
            $response = ['error' => 'La fecha debe estar en formato YYYY-MM-DD'];
        } else if (!isset($_PUT['hora']) || !preg_match('/^\d{2}:\d{2}:\d{2}$/', $_PUT['hora'])) {
            $response = ['error' => 'La hora debe estar en formato HH:MM:SS'];
        } else {
            $response = $meetingsModel->updateMeetings($_PUT['id'], $_PUT['reunion'], $_PUT['descripcion'], $_PUT['fecha'], $_PUT['hora']);
        }
        error_log('PUT Response: ' . print_r($response, true)); 
        echo json_encode($response);
        break;

    case 'DELETE':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if (empty($id)) {
            $response = ['error' => 'El ID de la reunión no debe estar vacío'];
        } else {
            $response = $meetingsModel->deleteMeetings($id);
        }
        error_log('DELETE Response: ' . print_r($response, true)); 
        echo json_encode($response);
        break;
}
