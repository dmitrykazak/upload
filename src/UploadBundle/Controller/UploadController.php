<?php

namespace UploadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use UploadBundle\Document\Product;

use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\Reader\DoctrineReader;

class UploadController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
      $product = new Product();
      $product->setName('New Product');
      $product->setPrice('10.11');

      $dm = $this->get('doctrine_mongodb')->getManager();
      $dm->persist($product);
      $dm->flush();

      return new Response('Created product id '.$product->getId());
    }
}
