<?php

namespace UploadBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use UploadBundle\Validator\Constraints as UploadAssert;

/**
 * @MongoDB\Document(collection="product")
 * @MongoDB\HasLifecycleCallbacks
 * @UploadAssert\ContainsCostStock
 */
class Product
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @MongoDB\Field(type="float")
     * @Assert\LessThan(1000)
     */
    protected $cost;

    /**
     * @MongoDB\Field(type="integer")
     */
    protected $stock;

    /**
     * @MongoDB\Field(type="boolean")
     */
    protected $isDiscontinued;

    /**
     * @MongoDB\Field(type="date")
     */
    protected $createdAt;

    /**
     * @MongoDB\PrePersist()
     * @MongoDB\PreUpdate()
     */
    public function preUpdate()
    {
        if (!$this->getIsDiscontinued()) {
            $this->setCreatedAt(new \DateTime());
        }
    }

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set cost
     *
     * @param float $cost
     * @return $this
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
        return $this;
    }

    /**
     * Get cost
     *
     * @return float $cost
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set isDiscontinued
     *
     * @param boolean $isDiscontinued
     * @return $this
     */
    public function setIsDiscontinued($isDiscontinued)
    {
        $this->isDiscontinued = $isDiscontinued;
        return $this;
    }

    /**
     * Get isDiscontinued
     *
     * @return boolean $isDiscontinued
     */
    public function getIsDiscontinued()
    {
        return $this->isDiscontinued;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     * @return $this
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
        return $this;
    }

    /**
     * Get stock
     *
     * @return integer $stock
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set createdAt
     *
     * @param date $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return date $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
