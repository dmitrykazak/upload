<?php

namespace AppBundle\Writer;

use Port\Doctrine\DoctrineWriter;

class DoctrineMongoDbWriter extends DoctrineWriter
{
    private $isTest = false;

    /**
     * @param array $item
     */
    public function writeItem(array $item)
    {
        $object = $this->findOrCreateItem($item);

        $this->updateObject($item, $object);

        if (!$this->isTest) {
            $this->objectManager->persist($object);
        }
    }

    /**
     * @param bool $isTest
     */
    public function setIsTest(bool $isTest)
    {
        $this->isTest = $isTest;
    }
}