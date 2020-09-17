<?php


namespace Calendar\Domain\Admin\Security\Subscriber;

use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\Booking;
use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\BookingTime;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class EasyAdminSecurityEventSubscriber implements EventSubscriberInterface
{

    private $decisionManager;

    private $token;


    /**
     * @param AccessDecisionManagerInterface $decisionManager
     * @param TokenStorageInterface $token
     */
    public function __construct()
    {
        //  $this->decisionManager = $decisionManager;
        // $this->token = $token;
    }


    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EasyAdminEvents::PRE_LIST => ['isAuthorized'],
            EasyAdminEvents::PRE_EDIT => [
                ['isAuthorized', 1000],
            ],
            EasyAdminEvents::PRE_DELETE => [
                ['isAuthorized', 1000],
                ['checkDateBeforeAction', 1000],
            ],
            EasyAdminEvents::PRE_UPDATE => [['checkDateBeforeAction', 1000]],
            EasyAdminEvents::PRE_NEW => ['isAuthorized'],
            EasyAdminEvents::PRE_SHOW => ['isAuthorized'],
        ];
    }


    /**
     * @param GenericEvent $event
     */
    public function isAuthorized(GenericEvent $event)
    {
        $entityConfig = $event['entity'];

        $action = $event->getArgument('request')->query->get('action');

        if (!array_key_exists('permissions', $entityConfig)
            || !array_key_exists(
                $action, $entityConfig['permissions']
            )) {
            return;
        }

        $authorizedRoles = $entityConfig['permissions'][$action];

        if (!$this->decisionManager->decide($this->token->getToken(), $authorizedRoles)) {
            throw new AccessDeniedException();
        }
    }


    /**
     * @param GenericEvent $event
     *
     * @throws \Exception
     */
    public function checkDateBeforeAction(GenericEvent $event)
    {
        $entity = $event->getSubject();
        if (!($entity instanceof Booking)) {
            return;
        }
        $isAdmin = $this->decisionManager->decide($this->token->getToken(), ['ROLE_SUPERADMIN']);
        $currentTime = new \DateTime('now', new \DateTimeZone(BookingTime::APPLICATION_TIME_ZONE));
        if (!$isAdmin && $entity->getBookingDateTimeStart() < $currentTime) {
            throw new AccessDeniedException();
        }
    }
}
