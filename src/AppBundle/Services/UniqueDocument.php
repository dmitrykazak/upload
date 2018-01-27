<?php
declare(strict_types=1);

namespace AppBundle\Services;

use Doctrine\ODM\MongoDB\DocumentManager;

class UniqueDocument
{
    /**
     * @var DocumentManager $manager
     */
    private $manager;

    /**
     * @var array $productCode
     */
    private $productCode = [];

    /**
     * Unique constructor.
     * @param DocumentManager $manager
     */
    public function __construct(DocumentManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param array $item
     *
     * @return bool
     */
    public function __invoke(array $item)
    {
        if (array_key_exists($item['code'], $this->productCode)) {
            return false;
        }

        $this->productCode[$item['code']] = true;

        return true;
    }
}
