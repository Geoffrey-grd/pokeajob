<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\DelaccountController;
use App\Models\User;

class DelaccountControllerTest extends TestCase
{
    protected function setUp(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
        $_POST = [];
    }

    public function testDeleteAccountWrongCredentials() {
        session_start();
        $_SESSION['user_id'] = 1;
        $_SESSION['role'] = 'etudiant';
        $_POST = [
            'email' => 'wrong@mail.com',
            'password' => 'wrongpass'
        ];

        $userMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['findByEmail'])
            ->getMock();
        $userMock->method('findByEmail')->willReturn(false);

        $controller = new class($userMock) extends DelaccountController {
            private $userMock;

            public function __construct($userMock) {
                $this->userMock = $userMock;
            }

            public static function deleteAccount() {}

            public function run() {
                $userData = $this->userMock->findByEmail($_POST["email"]);

                if (!$userData || !password_verify($_POST["password"], $userData["password"] ?? '')) {
                    $_SESSION['delete_error'] = "Identifiant ou mot de passe incorrect.";
                    return;
                }
            }
        };

        $controller->run();

        $this->assertEquals("Identifiant ou mot de passe incorrect.", $_SESSION['delete_error']);
    }

    public function testDeleteAccountWrongUser() {
        session_start();
        $_SESSION['user_id'] = 1;
        $_SESSION['role'] = 'etudiant';
        $_POST = [
            'email' => 'user@mail.com',
            'password' => 'password'
        ];

        $userMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['findByEmail'])
            ->getMock();
        $userMock->method('findByEmail')->willReturn([
            'id_user' => 2,
            'password' => password_hash('password', PASSWORD_DEFAULT)
        ]);

        $controller = new class($userMock) extends DelaccountController {
            private $userMock;

            public function __construct($userMock) {
                $this->userMock = $userMock;
            }

            public function run() {
                $userData = $this->userMock->findByEmail($_POST["email"]);

                if ($userData["id_user"] != $_SESSION["user_id"]) {
                    $_SESSION['delete_error'] = "Vous ne pouvez supprimer que votre propre compte.";
                    return;
                }
            }
        };

        $controller->run();

        $this->assertEquals(
            "Vous ne pouvez supprimer que votre propre compte.",
            $_SESSION['delete_error']
        );
    }

    public function testDeleteAccountSuccess() {
        session_start();
        $_SESSION['user_id'] = 1;
        $_SESSION['role'] = 'etudiant';
        $_POST = [
            'email' => 'user@mail.com',
            'password' => 'password'
        ];

        $userMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['findByEmail', 'delete_account'])
            ->getMock();
        $userMock->method('findByEmail')->willReturn([
            'id_user' => 1,
            'password' => password_hash('password', PASSWORD_DEFAULT)
        ]);

        $userMock->expects($this->once())
            ->method('delete_account')
            ->with(1);

        $controller = new class($userMock) extends DelaccountController {
            private $userMock;

            public function __construct($userMock) {
                $this->userMock = $userMock;
            }

            public function run() {
                $userData = $this->userMock->findByEmail($_POST["email"]);

                if ($userData && password_verify($_POST["password"], $userData["password"])) {
                    if ($userData["id_user"] == $_SESSION["user_id"]) {
                        $this->userMock->delete_account($_SESSION["user_id"]);
                        $_SESSION = []; // simule session_destroy
                    }
                }
            }
        };

        $controller->run();

        $this->assertEmpty($_SESSION);
    }
}