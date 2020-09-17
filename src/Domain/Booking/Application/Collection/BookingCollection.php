<?php

namespace Calendar\Domain\Booking\Application\Collection;

use Calendar\SharedKernel\Library\Collection;
use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\Booking;

class BookingCollection extends Collection
{
    /**
     * @param int $bookingTimeId
     *
     * @return int
     */
    public function findCountBooksForSlot(int $bookingTimeId): int
    {
        $busySlots = 0;
        if ($this->isEmpty()) {
            return $busySlots;
        }


        /** @var Booking $item */
        foreach ($this->getItems() as $item) {

            if ($item->getBookingTime()->getId() === $bookingTimeId) {
                $busySlots += $item->getBookedSlots();
            }
        }

        return $busySlots;
    }
}
