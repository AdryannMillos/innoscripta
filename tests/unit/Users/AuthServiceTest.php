<?php

namespace Tests\Unit\Users;

use App\Interfaces\Users\UserRepositoryInterface;
use App\Models\User;
use App\Services\Users\AuthService;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthServiceTest extends TestCase
{
    protected $userServiceInterface;

    public function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->userServiceInterface = Mockery::mock(UserRepositoryInterface::class);
    }

    /** @test */
    public function itShouldCheckIfUserCanLoginSuccessfully()
    {
        $userData = [
            'email' => 'john@example.com',
            'password' => Hash::make('12Ab@346'),
        ];

        $loginData = [
            'email' => 'john@example.com',
            'password' => '12Ab@346',
        ];

        $this->userServiceInterface
            ->shouldReceive('find')
            ->with($loginData['email'], 'email')
            ->andReturn((object) $userData);

        $authService = new AuthService($this->userServiceInterface);

        $result = $authService->canUserLogin($loginData);

        $this->assertTrue($result);
    }

    /** @test */
    public function itShouldCheckIfUserCannotLoginWithWrongPassword()
    {
        $userData = [
            'email' => 'john@example.com',
            'password' => Hash::make('12Ab@346'),
        ];

        $loginData = [
            'email' => 'john@example.com',
            'password' => 'wrong_password',
        ];

        $this->userServiceInterface
            ->shouldReceive('find')
            ->with($loginData['email'], 'email')
            ->andReturn((object) $userData);

        $authService = new AuthService($this->userServiceInterface);

        $result = $authService->canUserLogin($loginData);

        $this->assertFalse($result);
    }

    /** @test */
    public function itShouldCheckIfUserCannotLoginWithNonExistingEmail()
    {
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'some_password',
        ];

        $this->userServiceInterface
            ->shouldReceive('find')
            ->with($loginData['email'], 'email')
            ->andReturnNull();

        $authService = new AuthService($this->userServiceInterface);

        $result = $authService->canUserLogin($loginData);

        $this->assertFalse($result);
    }
}
