<?php

namespace GeekBrains\Project\Person;

class Person
{
    private Name $name;
    private \DateTimeImmutable $registeredOn;

    public function __construct(Name $name, \DateTimeImmutable $registeredOn)
    {
        $this->name = $name;
        $this->registeredOn = $registeredOn;
    }

    public function __toString()
    {
        return $this->name . ' (на сайте с ' . $this->registeredOn->format('Y-m-d') . ')';
    }
}
