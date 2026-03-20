<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;


class AuthController extends Controller
{
    use HttpResponses;

    public function login(Request $request)
    {
        if(Auth::attempt($request->only('email', 'password'))){
            $user = User::where('email', $request->email)->first();
            return $this->response('Autenticado com sucesso', 200, [
                'token' => $request->user()->createToken('API Token')->plainTextToken
            ]);
        }   else {
            return $this->response('Credenciais inválidas', 401);
        }
    }

}
