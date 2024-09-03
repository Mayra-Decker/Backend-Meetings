<?php

class meetingsModel {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli('localhost', 'root', 'mayra2005', 'api');
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getMeetings($id = null) {
        $where = ($id === null) ? "" : " WHERE id=?";
        $meetings = [];
        $sql = "SELECT * FROM meetings" . $where;
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die('Error al preparar la consulta: ' . $this->conn->error);
        }
        if ($id !== null) {
            $stmt->bind_param("i", $id);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($meetings, $row);
            }
        } else {
            die('Error en la consulta: ' . $this->conn->error);
        }
        return $meetings;
    }

    public function saveMeetings($reunion, $descripcion, $fecha, $hora) {
        $sql = "INSERT INTO meetings (reunion, descripcion, fecha, hora) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die('Error al preparar la consulta: ' . $this->conn->error);
        }
        $stmt->bind_param("ssss", $reunion, $descripcion, $fecha, $hora);
        if ($stmt->execute()) {
            return ['status' => 'success'];
        } else {
            return ['error' => $stmt->error];
        }
    }

    public function updateMeetings($id, $reunion, $descripcion, $fecha, $hora) {
        $exists = $this->getMeetings($id);
        if (count($exists) > 0) {
            $valida = $this->validateMeetings($reunion, $descripcion, $fecha, $hora);
            if (count($valida) == 0) {
                $sql = "UPDATE meetings SET reunion=?, descripcion=?, fecha=?, hora=? WHERE id=?";
                $stmt = $this->conn->prepare($sql);
                if ($stmt === false) {
                    die('Error al preparar la consulta: ' . $this->conn->error);
                }
                $stmt->bind_param("ssssi", $reunion, $descripcion, $fecha, $hora, $id);
                if ($stmt->execute()) {
                    return ['success' => 'Reunión actualizada'];
                } else {
                    return ['error' => $stmt->error];
                }
            } else {
                return ['error' => 'Ya existe una reunión con las mismas características'];
            }
        } else {
            return ['error' => 'No existe la reunión con ID ' . $id];
        }
    }

    public function deleteMeetings($id) {
        $valida = $this->getMeetings($id);
        if (count($valida) > 0) {
            $sql = "DELETE FROM meetings WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                die('Error al preparar la consulta: ' . $this->conn->error);
            }
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                return ['success' => 'Reunión eliminada'];
            } else {
                return ['error' => $stmt->error];
            }
        } else {
            return ['error' => 'No existe la reunión con ID ' . $id];
        }
    }

    public function validateMeetings($reunion, $descripcion, $fecha, $hora) {
        $meetings = [];
        $sql = "SELECT * FROM meetings WHERE reunion=? AND descripcion=? AND fecha=? AND hora=?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die('Error al preparar la consulta: ' . $this->conn->error);
        }
        $stmt->bind_param("ssss", $reunion, $descripcion, $fecha, $hora);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            array_push($meetings, $row);
        }
        return $meetings;
    }
}
?>
