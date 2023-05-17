<?php

namespace GeekBrains\Project\tests\Container;

class ClassDependingOnAnother
{
    public function __construct(
        private SomeClassWithoutDependencies $one,
        private SomeClassWithParameter       $two,
    ) {
    }
}
