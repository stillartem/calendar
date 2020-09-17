<?php

namespace Calendar\Domain\Booking\Application\ValueObject;

use Calendar\SharedKernel\ValueObject;

class Persons implements ValueObject
{
    /** @var string */
    private string $value;

    /**
     *
     * @param string $value
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }


    /**
     * @param string $value
     *
     * @return Persons
     */
    public static function fromValue(string $value): Persons
    {
        return new self($value);
    }


    /**
     * @return int
     */
    public function getValue(): int
    {
        return (int)$this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
}
