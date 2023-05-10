<?php

function toExcept()
{
    try {
        throw new Exception("error ");
    } catch (Exception $exception) {
        return 'ERROR';
    }
    return true;
}

echo 'start' . PHP_EOL;
var_dump(toExcept());
echo 'end';
