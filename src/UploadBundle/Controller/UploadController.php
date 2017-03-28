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

  /**
   * @Route("/upload")
   */
    public function indexUpload()
    {
      // Create and configure the reader
      $file = new \SplFileObject('products.csv');

      $reader = new CsvReader($file, ';');

      $reader->setHeaderRowNumber(0);


      $dm = $this->get('doctrine_mongodb')->getManager();

        $document = new DoctrineReader($dm, 'UploadBundle\Document\Product');

        $doctrineWriter = new DoctrineWriter($reader1, 'UploadBundle\Document\Product');

        echo "<pre>";
        print_r($doctrineWriter->getFields());
        die();

      foreach ($reader as $row) {
        $product = new Product();
        $product->setName($row["product"]);
        $product->setPrice($row["price"]);
        $product->setIsDiscontinued($row["discontinued"]);

        $dm->persist($product);
      }

      $dm->flush();

      return new Response($reader->count());
    }
}
