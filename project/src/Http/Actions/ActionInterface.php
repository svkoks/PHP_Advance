<?php

namespace GeekBrains\Project\Http\Actions;

use GeekBrains\Project\Http\Request;
use GeekBrains\Project\Http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}
