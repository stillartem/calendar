<?php

namespace Calendar\Domain\Booking\Exception;

use Throwable;

class BookingException extends \Exception
{

    public const WRONG_PHONE_FORMAT = 'wrong-phone.exception';

    public const WRONG_EMAIL_FORMAT = 'wrong-email.exception';

    public const WRONG_NAME_FORMAT = 'wrong-name.exception';

    public const WRONG_PERSONS_FORMAT = 'wrong-persons.exception';

    public const WRONG_DATE_FORMAT = 'wrong-date.exception';

    public const WRONG_TIME_SLOT = 'wrong-time-slot.exception';

    public const WRONG_SERVICE = 'wrong-time-slot.exception';

    public const WRONG_ADDRESS = 'wrong-address.exception';

    public const BOOKING_NOT_EXIST = 'entity.booking_not_exist';

    public const BOOKING_TIME_NOT_EXIST = 'entity.booking_time_not_exist';

    public const SERVICES_NOT_EXIST = 'entity.services_not_exist';


    public function __construct(string $message = '', int $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
