<?php

namespace App\Exceptions;

use Exception;

class PublicException extends Exception
{

    protected $message;
    protected $code;

    public function __construct($message, $code)
    {
        $this->message = $message;
        $this->code = $code;
    }

    public function render()
    {
        if (request()->is("api/*") || request()->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'code' => $this->code,
                'message' => $this->message
            ])->setStatusCode($this->code);
        }

        return response($this->message)->setStatusCode($this->code);
    }
}
