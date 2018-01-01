<?php

namespace AppBundle\Writer;

use Port\Doctrine\DoctrineWriter;

class DoctrineMongoDbWriter extends DoctrineWriter
{
    /**
     * @param array $item
     */
    public function writeItem(array $item)
    {
        $object = $this->findOrCreateItem($item);

        $this->updateObject($item, $object);

        $this->objectManager->persist($object);
    }
}