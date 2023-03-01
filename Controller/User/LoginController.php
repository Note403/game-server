<?php

namespace Controller\User;

use Auxilium\Controller;
use Auxilium\Data\Request;
use Exception;
use Auxilium\Support\Response;
use Model\User\User;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = User::query()
            ->where(User::USERNAME, $request->input(User::USERNAME))
            ->get();

        if ($user == null)
            throw new Exception('Username or Password wrong');

        $pwData = explode('$', $user[User::PASSWORD]);

        if ($pwData[1] != User::hashPasswordWithSalt($request->input(User::PASSWORD), $pwData[0]))
            throw new Exception('Username or Password wrong');

        Response::success();
    }
}