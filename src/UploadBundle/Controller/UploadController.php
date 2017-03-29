<?php

namespace UploadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class UploadController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
      return new Response('Upload file');
    }
}
