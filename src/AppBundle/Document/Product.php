<?php

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="product", repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product {

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $name;

    /**
     * @MongoDB\Field(type="float")
     */
    protected $cost;

    /**
     * @MongoDB\Field(type="integer")
     */
    protected $stock;

    /**
     * @MongoDB\Field(type="string")
     * @MongoDB\Index(unique=true, order="asc")
     */
    protected $code;

    /**
     * @MongoDB\Field(type="boolean", name="is_discount")
     */
    protected $isDiscount;

    /**
     * @MongoDB\Field(type="date", name="created_at")
     */
    protected $createdAt;

    /**
     * @MongoDB\PrePersist()
     * @MongoDB\PreUpdate()
     */
    public function preUpdate()
    {
        if (!$this->setIsDiscount()) {
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
     * 
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
     *
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
     * Set stock
     *
     * @param integer $stock
     *
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
     * Set code
     *
     * @param string $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string $code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set isDiscount
     *
     * @param boolean $isDiscount
     *
     * @return $this
     */
    public function setIsDiscount($isDiscount)
    {
        $this->isDiscount = $isDiscount;

        return $this;
    }

    /**
     * Get isDiscount
     *
     * @return boolean $isDiscount
     */
    public function getIsDiscount()
    {
        return $this->isDiscount;
    }

    /**
     * Set createdAt
     *
     * @param date $createdAt
     *
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
