<?php

namespace Calendar\Port\Controller;

use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Address;
use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Service;
use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Repository\AddressRepository;
use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Repository\ServiceRepository;
use Calendar\Domain\Booking\Application\BookingProcessor;
use Calendar\Domain\Booking\Application\ValueObject\CustomerBookData;
use Calendar\Domain\Booking\Application\ValueObject\Name;
use Calendar\Domain\Booking\Application\ValueObject\Persons;
use Calendar\Domain\Booking\Application\ValueObject\Phone;
use Calendar\Domain\Booking\Exception\BookingException;
use Calendar\Domain\Booking\Form\BookingType;
use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\BookingTime;
use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Repository\BookingTimeRepository;
use Calendar\Domain\Core\Exception\InvalidFormException;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/booking", name="booking")
 */
class BookingController extends AbstractController
{
    /** @var BookingProcessor */
    private BookingProcessor $bookingProcessor;

    /** @var BookingTimeRepository */
    private BookingTimeRepository $bookingTimeRepository;

    /** @var AddressRepository */
    private AddressRepository $addressRepository;

    /** @var ServiceRepository */
    private ServiceRepository $serviceRepository;

    /** @var FormFactoryInterface */
    private FormFactoryInterface $formFactory;

    public function __construct(
        BookingProcessor $bookingProcessor,
        BookingTimeRepository $bookingTimeRepository,
        AddressRepository $addressRepository,
        ServiceRepository $serviceRepository,
        FormFactoryInterface $formFactory
    ) {
        $this->bookingProcessor = $bookingProcessor;
        $this->bookingTimeRepository = $bookingTimeRepository;
        $this->addressRepository = $addressRepository;
        $this->serviceRepository = $serviceRepository;
        $this->formFactory = $formFactory;
    }

    /**
     * @Post("/book", name="book")
     * @SWG\Post(
     *      security={
     *          {"api_key":{}}
     *      },
     *      summary="Book Action",
     *      tags={"Booking"},
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JSON Payload",
     *          required=true,
     *          type="json",
     *          format="application/json",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="name", type="string", example="test_name"),
     *              @SWG\Property(property="phone", type="string", example="000-000-00"),
     *              @SWG\Property(property="customer-count", type="string", example="1"),
     *              @SWG\Property(property="booking-time-id", type="string", example="1"),
     *              @SWG\Property(property="address-id", type="string", example="1")
     *          )
     *     )
     *
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the auth token of an user"
     * )
     * @param Request $request
     *
     * @return Response
     * @throws BookingException
     */
    public function bookingAction(Request $request): Response
    {
        $customerBookData = new CustomerBookData(
            Name::fromValue($request->get('name')),
            Phone::fromValue($request->get('phone')),
            Persons::fromValue($request->get('customer-count')),
            $this->retrieveBookingTimeFromRequest($request),
            $this->retrieveDateTimeFromRequest($request)

        );
        try {
            $this->bookingProcessor->processBooking($customerBookData);
        } catch (BookingException $bookingException) {
            return new JsonResponse(
                [
                    'result' => 'internal_error',
                    'error' => $bookingException->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                [
                    'result' => 'internal_error',
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(
            [
                'result' => 'ok',
            ],
            Response::HTTP_CREATED
        );
    }


    /**
     * @Get("/get-services", name="get-services")
     * @SWG\Get(
     *      security={
     *          {"api_key":{}}
     *      },
     *      summary="Get Services",
     *      tags={"Booking"},
     *      produces={"application/json"}
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the auth token of an user"
     * )
     * @return Response
     * @throws BookingException
     */
    public function getServicesAction(): Response
    {
        $services = $this->bookingProcessor->getAvailableServices();

        return new JsonResponse($services, Response::HTTP_OK);
    }


    /**
     * @Get("/get-booking-time", name="booking-time")
     * @SWG\Get(
     *      security={
     *          {"api_key":{}}
     *      },
     *      summary="Get Time Slots",
     *      tags={"Booking"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="booking-time-id",
     *          in="query",
     *          required=true,
     *          type="string",
     *          default="1"
     *     ),
     *      @SWG\Parameter(
     *          name="address-id",
     *          in="query",
     *          required=true,
     *          type="string",
     *          default="1"
     *     ),
     *      @SWG\Parameter(
     *          name="service-id",
     *          in="query",
     *          required=true,
     *          type="string",
     *          default="1"
     *     ),
     *      @SWG\Parameter(
     *          name="date",
     *          in="query",
     *          required=true,
     *          type="string",
     *          default="2020-12-12"
     *     ),
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the auth token of an user"
     * )
     * @param Request $request
     *
     * @return Response
     * @throws BookingException
     * @throws \Exception
     */
    public function getBookingTimeAction(Request $request): Response
    {
        $date = $this->retrieveDateTimeFromRequest($request);
        $service = $this->retrieveServiceFromRequest($request, $date);
        $address = $this->retrieveAddressFromRequest($request);
        $timeSlots = $this->bookingProcessor->getTimeSlotsForDay($date, $service, $address);

        return new JsonResponse($timeSlots, Response::HTTP_OK);
    }


    /**
     * @param Request $request
     *
     * @return BookingTime|object
     * @throws BookingException
     */
    private function retrieveBookingTimeFromRequest(Request $request): BookingTime
    {
        $bookingTimeId = (int)$request->get('booking-time-id', 0);
        if ($bookingTimeId === 0) {
            throw new BookingException(BookingException::WRONG_TIME_SLOT);
        }

        $bookingTime =
            $this->bookingTimeRepository->findOneBy(['id' => $bookingTimeId]);
        if ($bookingTime === null) {
            throw new BookingException(BookingException::WRONG_TIME_SLOT);
        }

        return $bookingTime;
    }


    /**
     * @param Request $request
     *
     * @return Address|object
     * @throws BookingException
     */
    private function retrieveAddressFromRequest(Request $request): Address
    {
        $addressId = (int)$request->get('address-id', 1);
        $address = $this->addressRepository->findOneBy(['id' => $addressId]);

        if ($address === null) {
            throw new BookingException(BookingException::WRONG_ADDRESS);
        }

        return $address;
    }


    /**
     * @param Request $request
     *
     * @param DateTime $date
     *
     * @return Service|object
     * @throws BookingException
     */
    private function retrieveServiceFromRequest(Request $request, DateTime $date): Service
    {
        $serviceId = (int)$request->get('service-id', 1);
        $service = $this->serviceRepository->findOneBy(['id' => $serviceId]);

        if ($service === null) {
            throw new BookingException(BookingException::WRONG_SERVICE);
        }

        return $service;
    }


    /**
     * @param Request $request
     *
     * @return bool|DateTime
     * @throws BookingException
     */
    private function retrieveDateTimeFromRequest(Request $request)
    {
        $selectedDateTime = $request->get('date', null);
        if ($selectedDateTime === null) {
            return new DateTime('now');
        }

        $date = DateTime::createFromFormat(BookingTime::APPLICATION_DATETIME_FORMAT, $selectedDateTime);
        if ($date !== false) {
            return $date;
        }

        throw new BookingException(BookingException::WRONG_DATE_FORMAT);
    }
}
