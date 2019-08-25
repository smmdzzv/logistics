<?php

namespace App\Common;


class ResponseFactory{
    public static function badRequest($message = ""){
        return response($message, 400);
    }
}
