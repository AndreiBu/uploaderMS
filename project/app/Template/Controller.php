<?php

namespace App\Template;

use App\AbstractController;

class Controller extends AbstractController
{
    public function index(): void
    {
        echo twig()->render('index.twig', []);
    }

    public function swager(): void
    {
        echo twig()->render('api/docs.twig', [
            'version' => time()
        ]);
    }

    public function getUploadTemplate()
    {
        echo twig()->render('uploader.twig', []);
    }
}
