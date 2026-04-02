<?php
namespace App\Controllers;

use Core\Csrf;
use Core\Auth;
use Core\View;

class GeneralController {
    public function checkprofilepic($user_id) {
    
        if ($_SESSION["role"] == "etudiant" || $_SESSION["role"] == "pilote") {
            $files = glob(__DIR__ . "/../../public/images/uploads/profile_img/" . $user_id . ".*");
            if (!empty($files)) {
                $profile_pic_path = "images/uploads/profile_img/" . $user_id . "." . pathinfo($files[0], PATHINFO_EXTENSION);
            }
            else {
                $profile_pic_path = "images/Profile.png";
            }
        }
        else if ($_SESSION["role"] == "entreprise") {
            $files = glob(__DIR__ . "/../../public/images/uploads/company_logo/" . $user_id . ".*");
            if (!empty($files)) {
                $profile_pic_path = "images/uploads/company_logo/" . $user_id . "." . pathinfo($files[0], PATHINFO_EXTENSION);
            }
            else {
                $profile_pic_path = "images/Profile.png";
            }
        }

        return $profile_pic_path;
    }
}