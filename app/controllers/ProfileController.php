<?php
namespace App\Controllers;

use App\Models\User;
use Core\Csrf;
use Core\Auth;
use Core\View;
require_once __DIR__ . "/../../config/database.php";

class ProfileController {

    public function renderProfile($editmode = false) {

        if (session_status() === PHP_SESSION_NONE) session_start();
        global $conn;

        if ($_SESSION["role"] == "etudiant") {
            $userData = User::getStudentById($conn, $_SESSION["user_id"]);
        } 
        else if ($_SESSION["role"] == "entreprise") {
            $userData = User::getCompanyById($conn, $_SESSION["user_id"]);
        }
        else if ($_SESSION["role"] == "pilote") {
            $userData = User::getPilotById($conn, $_SESSION["user_id"]);
        }
        View::render("profile.twig", ["role" => $_SESSION["role"], "userData" => $userData, "editmode" => $editmode]);

        
    }


    public function modifyProfile() {
        $this->renderProfile(true);
    }


    // public function modifyProfile(){
    //     if (session_status() === PHP_SESSION_NONE) session_start();
    //     global $conn;

    //     Csrf::verifyToken($_POST["csrf_token"]);

    //     if ($_SESSION["role"] == "etudiant") {
    //         User::updateStudent($conn, $_SESSION["user_id"], $_POST["name"], $_POST["email"], $_POST["description"]);
    //     } 
    //     else if ($_SESSION["role"] == "entreprise") {
    //         User::updateCompany($conn, $_SESSION["user_id"], $_POST["name"], $_POST["email"], $_POST["description"]);
    //     }
    //     else if ($_SESSION["role"] == "pilote") {
    //         User::updatePilot($conn, $_SESSION["user_id"], $_POST["name"], $_POST["email"], $_POST["description"]);
    //     } 
    // }
}