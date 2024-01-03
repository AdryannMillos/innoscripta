<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Interfaces\Users\UserServiceInterface;
use App\Http\Requests\Users\CreateUserRequest;
use Exception;

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

            $validateUserCreation = $this->userService->validateUserCreation ($validated);

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
        //
    }

}
