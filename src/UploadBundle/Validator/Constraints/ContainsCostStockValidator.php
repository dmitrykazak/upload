<?php

namespace UploadBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsCostStockValidator extends ConstraintValidator
{
    public function validate($protocol, Constraint $constraint)
    {
        if ($protocol->getStock() < $constraint->getMinLimitStock()
            && $protocol->getCost() < $constraint->getMinLimitCost()) {

            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $protocol->getCost())
                ->addViolation();
        }
    }

}