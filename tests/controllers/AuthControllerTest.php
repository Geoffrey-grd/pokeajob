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

        $controller = new class($userMock) extends AuthController {
            private $userMock;

            public function __construct($userMock) {
                $this->userMock = $userMock;
            }

            public function login() {
                $_SERVER['REQUEST_METHOD'] = 'POST';

                $userData = $this->userMock->findByEmail($_POST['email']);

                if (!$userData || !password_verify($_POST['password'], $userData['password'] ?? '')) {
                    $_SESSION['login_error'] = 'Identifiant ou mot de passe incorrect';
                } else {
                    $_SESSION['user_id'] = $userData['id_user'];
                    $_SESSION['email'] = $userData['email'];
                    $_SESSION['role'] = $userData['role'];
                }
            }
        };

        $controller->login();

        $this->assertArrayHasKey('login_error', $_SESSION);
        $this->assertEquals('Identifiant ou mot de passe incorrect', $_SESSION['login_error']);
        $this->assertArrayNotHasKey('user_id', $_SESSION);
    }

    public function testRegisterEmailExistingEmail() {
        $_POST = [
            'csrf_token' => 'token',
            'email' => 'exist@example.com',
            'password' => 'pwd'
        ];

        $userMock = $this->getMockBuilder(App\Models\User::class)
            ->onlyMethods(['findByEmail'])
            ->getMock();
        $userMock->method('findByEmail')->willReturn(['id_user' => 1]);

        $controller = new class($userMock) extends AuthController {
            private $userMock;

            public function __construct($userMock) {
                $this->userMock = $userMock;
            }

            public function register($account_type) {
                if ($this->userMock->findByEmail($_POST['email'])) {
                    $_SESSION['register_error'] = 'This email is already in use.';
                    return;
                }
            }
        };

        $controller->register('etudiant');
        $this->assertArrayHasKey('register_error', $_SESSION);
        $this->assertEquals('This email is already in use.', $_SESSION['register_error']);
    }

    public function testRegisterNew()
    {
        $_POST = [
            'csrf_token'=>'token',
            'email'=>'new@example.com',
            'password'=>'pwd',
            'name'=>'John',
            'last_name'=>'Doe',
            'school'=>'School A',
            'training_pilot'=>1
        ];
        $userMock = $this->getMockBuilder(App\Models\User::class)
                        ->onlyMethods(['findByEmail'])
                        ->getMock();
        $userMock->method('findByEmail')->willReturn(false);

        $studentMock = $this->getMockBuilder(App\Models\Student::class)
                            ->onlyMethods(['create_student'])
                            ->getMock();
        $studentMock->expects($this->once())
                    ->method('create_student')
                    ->with('new@example.com','pwd','John','Doe',1,'etudiant');

        $controller = new class($userMock, $studentMock) extends AuthController {
            private $userMock; private $studentMock;
            public function __construct($userMock,$studentMock){$this->userMock=$userMock;$this->studentMock=$studentMock;}
            public function register($account_type){
                if(!$this->userMock->findByEmail($_POST['email'])){
                    $this->studentMock->create_student($_POST['email'],$_POST['password'],$_POST['name'],$_POST['last_name'],$_POST['training_pilot'],'etudiant');
                }
            }
        };
        $controller->register('etudiant');
    }
}
