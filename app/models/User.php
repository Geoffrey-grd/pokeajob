<?php
 
namespace App\Models;

use PDO;
 
class User extends BDDlink {

    public function __construct() {
        parent::__construct();
    }
 
    public function findByemail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public function delete_account($user_id) {
        $stmt = $this->conn->prepare("DELETE FROM user WHERE id_user = ?");
        return $stmt->execute([$user_id]);
    }
}   