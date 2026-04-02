<?php

use PHPunit\Framework\TestCase;
use App\Controllers\CreateOfferController;
use App\Models\Offer;

class CreateOfferControllerTest extends TestCase {
    protected function setUp(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();    
            $_SESSION = [];
            $_POST = [];
        }
    }

    public function testCreateOfferSuccess() {
        session_start();
        $_SESSION['user_id'] = 5;
        $_POST = [
            'csrf_token' => 'token',
            'domain' => 'IT',
            'offer_object' => 'Développeur PHP',
            'place' => 'Paris',
            'annual_salary' => '40000',
            'description' => 'Offre de dev PHP'
        ];

        $offerMock = $this->getMockBuilder(App\Models\Offer::class)
            ->onlyMethods(['create_offer'])
            ->getMock();
        $offerMock->expects($this->once())
            ->method('create_offer')
            ->with(
                5,
                'IT',
                'Développeur PHP',
                'Paris',
                '40000',
                'Offre de dev PHP'
            );

        $controller = new class($offerMock) extends \App\Controllers\CreateOfferController {
            private $offerMock;

            public function __construct($offerMock) {
                $this->offerMock = $offerMock;
            }

            public function createOffer() {
                if (session_status() === PHP_SESSION_NONE) session_start();

                $this->offerMock->create_offer(
                    $_SESSION["user_id"],
                    $_POST["domain"],
                    $_POST["offer_object"],
                    $_POST["place"],
                    $_POST["annual_salary"],
                    $_POST["description"]
                );
            }
        };
        $controller->createOffer();
    }
}