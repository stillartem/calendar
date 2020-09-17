<?php


namespace Calendar\Port\Controller;

use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Form;

/**
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
class DashboardController extends EasyAdminController
{

}
