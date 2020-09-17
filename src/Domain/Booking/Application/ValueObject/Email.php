<?php

namespace Calendar\Domain\Booking\Application\ValueObject;

use Calendar\SharedKernel\ValueObject;

class Email implements ValueObject
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
     * @return Email
     */
    public static function fromValue(string $value): Email
    {
        return new self($value);
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
}
