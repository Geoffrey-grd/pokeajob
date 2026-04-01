<?php
namespace App\Controllers;

use Core\Csrf;
use Core\Auth;
use Core\View;

use App\Models\Application;

class ApplicationController {

    public function renderApplication() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "etudiant"){
            header("Location: /");
            exit();
        }

        $error = "";
        if (isset($_SESSION["flash_error"])) { $error = $_SESSION["flash_error"]; }
        unset($_SESSION["flash_error"]);

        View::render("application.twig", ["role" => $_SESSION["role"], "csrf_token" => Csrf::generateToken(), "id_offer" => $_GET["id_offer"], "error" => $error]);
    }

    public function apply() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        Csrf::verifyToken($_POST["csrf_token"]);

        $applicationModel = new Application();

        $this->checkfile($_FILES["cv"]);
        $this->checkfile($_FILES["motivation_letter"]);

        $cv_path = $this->moveFile($_FILES["cv"], "cv");
        $motivation_letter_path = $this->moveFile($_FILES["motivation_letter"], "motivation_letter");

        $applicationModel->applyToOffer($_SESSION["user_id"], $_POST["id_offer"], $cv_path, $motivation_letter_path);

        header("Location: /offer_description?id_offer=" . $_POST["id_offer"]);
        exit();
    }

    public function checkfile($file) {
        $error = "";

        if ($file["error"] !== UPLOAD_ERR_OK) {
            $error = "Error uploading file:";
            $_SESSION["flash_error"] = $error;
            header("Location: /apply?id_offer=" . $_POST["id_offer"]);
            exit();
        }

        if ($file["size"] > 2 * 1024 * 1024) {
           $error = "File size exceeds the limit of 2MB.";
           $_SESSION["flash_error"] = $error;
           header("Location: /apply?id_offer=" . $_POST["id_offer"]);
           exit();
        }
    }

    public function moveFile($file, $type) {
        $base_dir = realpath(__DIR__ . "/../../public/images/uploads/application/" . $type) . "/";
        $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
        $target_file = $base_dir . $_SESSION["user_id"] . "." . $extension;
        foreach (glob($base_dir . $_SESSION["user_id"] . ".*") as $existing_file) {
            unlink($existing_file);
        }
        move_uploaded_file($file["tmp_name"], $target_file);
        $bdd_path = str_replace("/var/www/pokeajob/public", "", $target_file);
        return $bdd_path;
    }
}