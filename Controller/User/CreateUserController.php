<?php

namespace Controller\User;

use Auxilium\Data\Request;
use Exception;
use GameServer\Auxilium\Support\App;
use GameServer\Auxilium\Support\Validator;
use Model\User\User;

class CreateUserController
{
    public function __construct(
        private readonly Validator $validator
    ) {
    }

    public function __invoke(Request $request)
    {
        $rules = [
            User::USERNAME => 'string|max:16|min:4|required',
            User::PASSWORD => 'string|max:16|min:6|required',
            'repeat_password' => 'string|max:16|min:6|required',
            User::EMAIL => 'string|max:32|min:4|required',
        ];

        (new $this->validator())($request, $rules);

        if ($request->input('password') != $request->input('repeat_password'))
            throw new Exception("Passwords must be the same");

        if (!str_contains($request->input('email'), '@'))
            throw new Exception("{$request->input('email')} is not a valid E-Mail");

        User::query()->create([
            User::ID => App::uuid(),
            User::USERNAME => $request->input(User::USERNAME),
            User::PASSWORD => User::hashPassword($request->input(User::PASSWORD)),
            User::EMAIL => $request->input(User::EMAIL),
            User::ROLE => 'user',
            User::BLOCKED => false,
        ]);
    }
}