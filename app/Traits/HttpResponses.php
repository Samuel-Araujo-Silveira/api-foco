<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\MessageBag;

trait HttpResponses
{
    public function response(string $message, string|int $status, array|Model $data = [])
    {
        return response()->json([
            'message' => $message,
            'status'  => $status,
            'data'    => $data
        ], $status);
    }

    public function error(string $message, string|int $status, array|MessageBag $errors = [], array $data = [])
    {
        return response()->json([
            'message' => $message,
            'status'  => $status,
            'erros'   => $errors,
            'data'    => $data
        ], $status);
    }
}