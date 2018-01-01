<?php

namespace AppBundle\Services;

use AppBundle\Document\Product;
use Doctrine\ODM\MongoDB\DocumentManager;

class UniqueDocument
{
    /**
     * @var DocumentManager $manager
     */
    private $manager;

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
        $document = $this->manager
            ->getRepository(Product::class)
            ->findOneBy(
                [
                    'code' => $item['code']
                ]
            );

        return !($document);
    }
}
