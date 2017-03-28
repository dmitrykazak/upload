<?php

namespace UploadBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsCostStock extends Constraint
{
    const MIN_LIMIT_STOCK = 10;

    const MIN_LIMIT_COST = 5;

    public $message = 'The value is not a valid.';

    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }

    public function getMinLimitStock()
    {
        return self::MIN_LIMIT_COST;
    }

    public function getMinLimitCost()
    {
        return self::MIN_LIMIT_COST;
    }

    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }
}