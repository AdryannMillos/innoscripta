<?php

namespace Tests\Unit\Users;

use App\Interfaces\Users\UserRepositoryInterface;
use App\Models\User;
use App\Services\Users\UserService;
use Exception;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected $userRepositoryInterface;

    public function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepositoryInterface = Mockery::mock(UserRepositoryInterface::class);
    }

    /**
     * @test
     * @return void
     */
    public function itShouldReturnAnUserInstance()
    {
        $dataToCreate = [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => '12Ab@346',
            'confirmPassword' => '12Ab@346',
        ];

        $dataToReturn = [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => Hash::make('12Ab@346')
        ];

        $this->userRepositoryInterface
            ->shouldReceive('store')
            ->once()
            ->andReturn(new User($dataToReturn));

        $userService = new UserService($this->userRepositoryInterface);

        $response = $userService->create($dataToCreate);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($dataToCreate['name'], $response->name);
        $this->assertEquals($dataToCreate['email'], $response->email);

        $this->assertTrue(Hash::check($dataToCreate['password'], $response->password));
    }
    /** @test */
    public function itShouldValidateUserCreation()
    {
        $this->userRepositoryInterface->shouldReceive('find')->andReturnNull();

        $data = [
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'confirmPassword' => 'password123',
        ];
        $userService = new UserService($this->userRepositoryInterface);

        $result = $userService->validateUserCreation($data);

        $this->assertTrue($result['canUserBeCreated']);
        $this->assertEquals('User can be created', $result['message']);
    }

    /** @test */
    public function itShouldValidateUserCreationWithExistingEmail()
    {
        $this->userRepositoryInterface->shouldReceive('find')->andReturn(['some_user_data']);

        $data = [
            'email' => 'existinguser@example.com',
            'password' => 'password123',
            'confirmPassword' => 'password123',
        ];
        $userService = new UserService($this->userRepositoryInterface);

        $result = $userService->validateUserCreation($data);

        $this->assertFalse($result['canUserBeCreated']);
        $this->assertEquals('Email already in use!', $result['message']);
    }

    // /** @test */
    public function itShouldValidateUserCreationWithNonMatchingPasswords()
    {
        $this->userRepositoryInterface->shouldReceive('find')->andReturnNull();

        $data = [
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'confirmPassword' => 'mismatchedpassword',
        ];

        $userService = new UserService($this->userRepositoryInterface);

        $result = $userService->validateUserCreation($data);

        $this->assertFalse($result['canUserBeCreated']);
        $this->assertEquals('Password and confirm password must match', $result['message']);
    }
}
