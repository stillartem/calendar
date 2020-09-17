<?php

namespace Calendar\Domain\Booking\Form;

use Calendar\Domain\Admin\Infrastructure\DoctrineORM\Entity\Customer;
use Calendar\Domain\Booking\Application\ValueObject\CustomerBookData;
use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\Booking;
use Calendar\Domain\Booking\Infrastructure\DoctrineORM\Entity\BookingTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'approved',
                [
                    'constraints' => new NotBlank(),
                    'property_path' => 'receiverCity'
                ]
            )
            ->add('bookedSlots', NumberType::class)
            ->add(
                'bookingDate',
                DateTimeType::class,
                [
                    //'widget' => 'single_text',
                 //   'format' => BookingTime::APPLICATION_DATETIME_FORMAT,
                ]
            )
            ->add(
                'bookingTime',
                DateTimeType::class,
                [
               //     'widget' => 'single_text',
              //      'format' => BookingTime::APPLICATION_TIME_FORMAT,
                ]);
//            )
//            ->add(
////                'customer', EntityType::class, [
////                    'class' => Customer::class,
////                ]
//            //'invalid_message' => BookingException::
//            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => CustomerBookData::class,
            ]
        );
    }
}
