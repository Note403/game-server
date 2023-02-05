<?php

namespace Controller\User;

use Auxilium\Data\Request;
use GameServer\Auxilium\Support\Response;
use GameServer\Auxilium\Support\Validator;
use Model\User\User;

class LoginController
{
    public function __invoke(Request $request)
    {
        $user = User::query()
            ->where(User::USERNAME, $request->input(User::USERNAME))
            ->get();
    }
}