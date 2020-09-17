<?php

declare(strict_types=1);

namespace Calendar\Domain\Core\Exception;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;

class InvalidFormException extends \Exception
{
    public function __construct(FormInterface $form)
    {
        print_r($this->getErrorMessages($form));die;
        parent::__construct(
          'some error',
            Response::HTTP_BAD_REQUEST,
            \implode(' ', $this->getErrorMessages($form))
        );
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    private function getErrorMessages(FormInterface $form): array
    {
        $errors = [];

        if (!$form->isSubmitted()) {
            $fieldNames = \array_map(
                function (FormInterface $item) {
                    return $item->getName();
                },
                $form->all()
            );

            $errors[] = 'There are no form fields in the request. At least one field from the list is required: '
                . \implode(', ', $fieldNames);

            return $errors;
        }

        foreach ($form->getErrors(true) as $error) {
            $propertyPath = '';
            $invalidValueString = '';
            $errorMessage = '';
            $cause = $error->getCause();

            if ($cause instanceof ConstraintViolation) {
                $propertyPath = $cause->getPropertyPath();
                $invalidValue = $cause->getInvalidValue();

                if (\is_array($invalidValue)) {
                    $invalidValue = \array_filter($invalidValue, function ($item) {
                        return \is_string($item) || \is_numeric($item) || \is_bool($item);
                    });
                    $invalidValueString = \implode(', ', $invalidValue);
                } elseif ($invalidValue instanceof Offer) {
                    $invalidValueString = null;
                } elseif ($invalidValue instanceof CustomerInterface) {
                    $invalidValueString = $invalidValue->getId();
                } elseif (\method_exists($invalidValue, '__toString')) {
                    $invalidValueString = (string) $invalidValue;
                } elseif ($invalidValue instanceof \DateTime) {
                    $invalidValueString = $invalidValue->format('Y-m-d H:i:s');
                }
            }

            if ($propertyPath) {
                $errorMessage = $propertyPath . ': ';
            }

            $errorMessage .= $error->getMessage();

            if (!empty($invalidValueString)) {
                $errorMessage .= ' (invalid values: ' . $invalidValueString . ').';
            }

            $errors[] = $errorMessage;
        }

        return $errors;
    }
}
