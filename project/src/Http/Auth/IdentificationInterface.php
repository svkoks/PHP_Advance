<?php

namespace GeekBrains\Project\Http\Auth;

use GeekBrains\Project\Blog\User;
use GeekBrains\Project\Http\Request;

interface IdentificationInterface
{
    public function user(Request $request): User;
}
