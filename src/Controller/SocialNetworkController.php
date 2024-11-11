<?php

namespace App\Controller;

use App\Entity\SocialNetwork;
use App\Form\SocialNetworkFormType;

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
        $form = SocialNetworkFormType::buildForm();

        $this->twig->display('socialNetwork/add.html.twig',
        [
            'formFields' => $form->getFields(),
        ]);

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
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $socialNetworkRepository = $entityManager->getRepository(SocialNetwork::class);
        $socialNetwork = $socialNetworkRepository->find($socialNetworkId);

        $form = SocialNetworkFormType::buildForm($socialNetwork);

        $this->twig->display('socialNetwork/modify.html.twig',
        [
            'socialNetwork' => $socialNetwork,
            'formFields' => $form->getFields(),
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