<?php

namespace App\Controller;

use App\Entity\Db;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DbController extends AbstractController
{

public function createDb(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $db = new Db();
        $db->setTitle('SupBoard');
        //$db->setPrice(1999);
        //$db->setDescription('Ergonomic and stylish!');

        // сообщите Doctrine, что вы хотите (в итоге) сохранить Продукт (пока без запросов)
        $entityManager->persist($db);

        // действительно выполните запросы (например, запрос INSERT)
        $entityManager->flush();

        return new Response('Saved new news with id '.$db->getId());
    }

}