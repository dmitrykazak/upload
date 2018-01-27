<?php
declare(strict_types=1);

namespace AppBundle\Services;

use Doctrine\ODM\MongoDB\DocumentManager;
use Port\Filter\ValidatorFilter;
use Port\Steps\Step\FilterStep;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UploadValidate
{
    const MIN_LIMIT_STOCK = 5;

    const MIN_LIMIT_COST = 10;

    const MAX_LIMIT_COST = 1000;

    /**
     * @var ValidatorInterface $validator
     */
    private $validator;

    /**
     * @var FilterStep $stepFilter
     */
    private $stepFilter;

    /**
     * @var ValidatorFilter $filter
     */
    private $filter;

    /**
     * @var DocumentManager $manager
     */
    private $manager;

    /**
     * @var UniqueDocument $uniqueDocument
     */
    private $uniqueDocument;

    /**
     * ValidateImport constructor.
     *
     * @param DocumentManager $manager
     * @param ValidatorInterface $validator
     * @param UniqueDocument $uniqueDocument
     */
    public function __construct(
        DocumentManager $manager,
        ValidatorInterface $validator,
        UniqueDocument $uniqueDocument
    )
    {
        $this->manager = $manager;
        $this->validator = $validator;
        $this->uniqueDocument = $uniqueDocument;
        $this->stepFilter = new FilterStep();
        $this->filter = new ValidatorFilter($this->validator);
    }

    public function stepValidate(): FilterStep
    {
        $this->stepFilter->add($this->uniqueDocument);

        $this->stepFilter->add(function(array $item) {
            $isLimitStock = $item['stock'] < static::MIN_LIMIT_STOCK;
            $isLimitCost = $item['cost'] < static::MIN_LIMIT_COST;

            return !($isLimitCost && $isLimitStock);
        });

        $this->filter->setStrict(false);

        $this->filter->add('name', new Assert\NotBlank());
        $this->filter->add('cost', new Assert\LessThan(static::MAX_LIMIT_COST));

        $this->stepFilter->add($this->filter);

        return $this->stepFilter;
    }
}