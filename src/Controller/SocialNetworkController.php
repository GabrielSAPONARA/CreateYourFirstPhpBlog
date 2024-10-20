<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\SocialNetwork;
use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;



class SocialNetworkController extends BasicController
{
    public function index(): void
    {
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';

        $socialNetworkRepository = $entityManager->getRepository(SocialNetwork::class);
        $socialNetworks = $socialNetworkRepository->findAll();
        $this->twig->display('socialNetwork/index.html.twig',
        [
            'socialNetworks' => $socialNetworks,
        ]);
    }

    public function add(): void
    {
        $this->twig->display('socialNetwork/add.html.twig');

    }

    public function process($params = []) : void
    {
        $socialNetworkId = $params['id'] ?? null;
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $url = "Location: http://";
        $host = $_SERVER["SERVER_NAME"];
        $port = $_SERVER["SERVER_PORT"];
        $url .= $host .":". $port . "/";
        if(isset($_POST["name"]))
        {
            if($socialNetworkId !== null)
            {
                $socialNetworkRepository = $entityManager->getRepository(SocialNetwork::class);
                $socialNetwork = $socialNetworkRepository->findById($socialNetworkId);
                $socialNetwork->setName($_POST["name"]);
                $entityManager->flush();
            }
            else
            {
                $socialNetwork = new SocialNetwork();
                $socialNetwork->setName($_POST["name"]);
                $entityManager->persist($socialNetwork);
                $entityManager->flush();
            }
            $url .= "social/network";
        }
        else
        {
            $url .= "social/network/add";
        }
        header($url);
        exit();
    }

    public function modify(array $params) : void
    {
        $socialNetworkId = $params["id"];
        dump($socialNetworkId);
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $socialNetworkRepository = $entityManager->getRepository(SocialNetwork::class);
        $socialNetwork = $socialNetworkRepository->find($socialNetworkId);

        $this->twig->display('socialNetwork/modify.html.twig',
        [
            'socialNetwork' => $socialNetwork,
        ]);
    }

    public function delete(array $params) : void
    {
        $socialNetworkId = $params["id"];
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $socialNetworkRepository = $entityManager->getRepository(SocialNetwork::class);
        $socialNetwork = $socialNetworkRepository->findById($socialNetworkId);
        $entityManager->remove($socialNetwork);
        $entityManager->flush();
        $url = "Location: http://";
        $host = $_SERVER["SERVER_NAME"];
        $port = $_SERVER["SERVER_PORT"];
        $url .= $host .":". $port . "/";
        $url .= "social/network";
        header($url);
        exit();
    }
}