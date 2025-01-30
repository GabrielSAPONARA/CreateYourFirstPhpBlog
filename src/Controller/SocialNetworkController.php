<?php

namespace App\Controller;

use App\Entity\SocialNetwork;
use App\Form\SocialNetworkFormType;
use App\Router\RouteManager;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

class SocialNetworkController extends BasicController
{
    private EntityManagerInterface $entityManager;
    protected Environment $twig;
    private RouteManager $routeManager;
    protected array $loggers;


    public function __construct
    (
        EntityManagerInterface $entityManager,
        \Twig\Environment $twig,
        \App\Router\RouteManager $routeManager,
        array $loggers
    )
    {
        parent::__construct($twig, $routeManager, $loggers);
        $this->entityManager = $entityManager;
    }
    public function index(): void
    {
        $this->beforeAction('Administrator');

        $socialNetworkRepository = $this->entityManager->getRepository
        (SocialNetwork::class);
        $socialNetworks = $socialNetworkRepository->findAll();
        $this->twig->display('socialNetwork/index.html.twig',
        [
            'socialNetworks' => $socialNetworks,
        ]);
    }

    public function add(): void
    {
        $this->beforeAction('Administrator');
        $form = SocialNetworkFormType::buildForm();

        $this->twig->display('socialNetwork/add.html.twig',
        [
            'formFields' => $form->getFields(),
        ]);

    }

    public function process($params = []) : void
    {
        $this->beforeAction('Administrator');
        $socialNetworkId = $params['id'] ?? null;
        $url = "Location: http://";
        $host = $_SERVER["SERVER_NAME"];
        $port = $_SERVER["SERVER_PORT"];
        $url .= $host .":". $port . "/";
        if(isset($_POST["name"]))
        {
            if($socialNetworkId !== null)
            {
                $socialNetworkRepository = $this->entityManager->getRepository
                (SocialNetwork::class);
                $socialNetwork = $socialNetworkRepository->findById($socialNetworkId);
                $socialNetwork->setName($_POST["name"]);
                $this->entityManager->flush();
            }
            else
            {

                $socialNetwork = new SocialNetwork();
                $socialNetwork->setName($_POST["name"]);
                $this->entityManager->persist($socialNetwork);
                $this->entityManager->flush();
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
        $this->beforeAction('Administrator');
        $socialNetworkId = $params["id"];
        $socialNetworkRepository = $this->entityManager->getRepository
        (SocialNetwork::class);
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
        $this->beforeAction('Administrator');
        $socialNetworkId = $params["id"];
        $socialNetworkRepository = $this->entityManager->getRepository
        (SocialNetwork::class);
        $socialNetwork = $socialNetworkRepository->findById($socialNetworkId);
        $this->entityManager->remove($socialNetwork);
        $this->entityManager->flush();
        $url = "Location: http://";
        $host = $_SERVER["SERVER_NAME"];
        $port = $_SERVER["SERVER_PORT"];
        $url .= $host .":". $port . "/";
        $url .= "social/network";
        header($url);
        exit();
    }
}