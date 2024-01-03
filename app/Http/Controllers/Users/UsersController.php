<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Interfaces\Users\UserServiceInterface;
use App\Http\Requests\Users\CreateUserRequest;
use Exception;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    protected $userService;

    public function __construct(
        UserServiceInterface $userService
    ) {
        $this->userService = $userService;
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        try {
            $validated = $request->validated();

            $validateUserCreation = $this->userService->validateUserCreation($validated);

            if (!$validateUserCreation['canUserBeCreated']) {
                throw new Exception($validateUserCreation['message'], 406);
            }

            $this->userService->create($validated);
            return response()->json(['message' => 'User created successfully!'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
            try {
                $user = $this->userService->findById($id);

                if (!$user) {
                    throw new Exception('User not found!', 404);
                }

                return response()->json(['user' => $user], 200);
            } catch (Exception $e) {
                return response()->json(['message' => $e->getMessage()], $e->getCode());
            }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        try {
            $user = $this->userService->findById($id);

            if (!$user) {
                throw new Exception('User not found!', 404);
            }

            $id = (int) $id;
            $updatedUser = $this->userService->updateUser($user, $id, $request->all());

            return response()->json(['user' => $updatedUser], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

}
