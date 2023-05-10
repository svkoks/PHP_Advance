<?php

namespace GeekBrains\Project\tests;

use PHPUnit\Framework\TestCase;

class HelloTest extends TestCase
{
    public function testItWorks(): void
    {
        // Проверяем, что true – это true
        $this->assertTrue(true);
    }

    public function testAdd(): void
    {
        $this->assertEquals(10, 5 + 5);
    }
}
