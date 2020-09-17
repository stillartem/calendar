<?php


namespace Calendar\Domain\Booking\Application\ValueObject;

use Calendar\SharedKernel\ValueObject;
use Calendar\Domain\Booking\Exception\BookingException;

class Phone implements ValueObject
{
    /** @var string */
    private string $value;

    private const DEFAULT_PATTERN = '/^[+]38[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.0-9]*$/m';

    /**
     *
     * @param string $value
     * @param string $pattern
     */
    private function __construct(string $value, $pattern = self::DEFAULT_PATTERN)
    {
        $this->value = $value;
    }

    /**
     * @param string $value
     *
     * @return Phone
     * @throws BookingException
     */
    public static function fromValue(string $value): Phone
    {
        try {
            //  Assert::that($value)->regex('/^[+]38[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.0-9]*$/m');
        } catch (\Exception $exception) {
            throw new BookingException(BookingException::WRONG_PHONE_FORMAT);
        }

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
