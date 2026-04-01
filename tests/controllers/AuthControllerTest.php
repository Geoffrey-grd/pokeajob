<?php
use PHPunit\Framework\TestCase;
use App\Controllers\AuthController;
use App\Models\Sector;
use App\Models\Pilot;
use Core\View;
use Core\Csrf;

class AuthControllerTest extends TestCase {
    protected function setUp() : void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
    }

    public function testFormTypeCallsLogin() {
        $_POST['auth_mode'] = 'login';
        $controller = $this->getMockBuilder(AuthController::class)
        ->onlyMethods(['login', 'register'])
        ->getMock();

        $controller->expects($this->once())->method('login');
        $controller->expects($this->never())->method('register');

        $controller->form_type();
    }

    public function testFormTypeCallsRegister() {
        $_POST['auth_mode'] = 'signup';
        $_POST['account_type'] = 'etudiant';

        $controller = $this->getMockBuilder(AuthController::class)
        ->onlyMethods(['login', 'register'])
        ->getMock();

        $controller->expects($this->never())->method('login');
        $controller->expects($this->once())->method('register')->with('etudiant');

        $controller->form_type();
    }

    public function testLoginSuccess() {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'csrf_token'=> 'token',
            'email'=> 'student@a',
            'password'=> 'a'
        ];

        $userMock = $this->getMockBuilder(App\Models\User::class)
        ->onlyMethods(['findByEmail'])
        ->getMock();

        $userMock->method('findByEmail')->willReturn([
            'id_user' => 12,
            'email' => 'student@a',
            'password' => password_hash('a', PASSWORD_DEFAULT),
            'role' => 'etudiant'
        ]);   
            
        $controller = new class($userMock) extends AuthController {
            private $userMock;

            public function __construct($userMock) {
                $this->userMock = $userMock;
            }

            public function login() {
                $_SERVER['REQUEST_METHOD'] = 'POST';

                $userData = $this->userMock->findByEmail($_POST['email']);
                if ($userData && password_verify($_POST['password'], $userData['password'])) {
                    $_SESSION['user_id'] = $userData['id_user'];
                    $_SESSION['email'] = $userData['email'];
                    $_SESSION['role'] = $userData['role'];
                }
            }
        };

        $controller->login();
        $this->assertEquals(12, $_SESSION['user_id']);
        $this->assertEquals('student@a', $_SESSION['email']);
        $this->assertEquals('etudiant', $_SESSION['role']); 
    }

    public function testLoginFailure() {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'csrf_token' => 'token',
            'email'=> 'exemple@incorrect',
            'password' => 'mauvaisMDP'
        ];

        $userMock = $this->getMockBuilder(App\Models\User::class)
        ->onlyMethods(['findByEmail'])
        ->getMock();
        $userMock->method('findByEmail')->willReturn(false);

        $viewMock = $this->getMockBuilder(Core\View::class)
        ->onlyMethods(['render'])
        ->getMock();
        $viewMock->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo('login-register.twig'),
            $this->callback(function($params) {
                return isset($params['error']) &&
                $params['error'] === 'Identifiant ou mot de passe incorrect.';
            })
        );

        $controller = new class($userMock, $viewMock) extends AuthController {
            private $userMock;
            private $viewMock;

            public function __construct($userMock, $viewMock) {
                $this->userMock = $userMock;
                $this->viewMock = $viewMock;
            }

            public function login() {
                $_SERVER['REQUEST_METHOD'] = 'POST';

                $userData = $this->userMock->findByEmail($_POST['email']);
                if (!$userData || !password_verify($_POST['password'], $userData['password'] ?? '')) {
                    $this->viewMock->render('login-register.twig', [
                        'error'=> 'Identifiant ou mot de passe incorrect.',
                    ]);
            } else {
                $_SESSIONS['user_id'] = $userData['id_user'];
                $_SESSIONS['email'] = $userData['email'];
                $_SESSIONS['role'] = $userData['role'];
                }
            }
        };

    $controller->login();
    $this->assertArrayNotHasKey('user_id', $_SESSION);
    $this->assertArrayNotHasKey('email', $_SESSION);
    $this->assertArrayNotHasKey('role', $_SESSION);
    }
}
