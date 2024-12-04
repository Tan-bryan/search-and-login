<?php
class CRUD {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function insertApplication($data) {
        $sql = "INSERT INTO applications (name, birthday, location_of_birth, gender, marital_status, education, description, created_by) 
                VALUES (:name, :birthday, :location_of_birth, :gender, :marital_status, :education, :description, :created_by)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':birthday' => $data['birthday'],
            ':location_of_birth' => $data['location_of_birth'],
            ':gender' => $data['gender'],
            ':marital_status' => $data['marital_status'],
            ':education' => $data['education'],
            ':description' => $data['description'],
            ':created_by' => $_SESSION['user_id'], // assuming user_id is stored in session
        ]);
        return $stmt->rowCount() > 0;
    }

    public function getAllApplications($search = '') {
        $sql = "SELECT * FROM applications WHERE name LIKE :search OR education LIKE :search";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':search' => '%' . $search . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getApplicationById($id) {
        $sql = "SELECT * FROM applications WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateApplication($data) {
        $sql = "UPDATE applications SET name = :name, birthday = :birthday, location_of_birth = :location_of_birth, 
                gender = :gender, marital_status = :marital_status, education = :education, description = :description 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        return $stmt->rowCount() > 0;
    }

    public function deleteApplication($id) {
        $sql = "DELETE FROM applications WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
