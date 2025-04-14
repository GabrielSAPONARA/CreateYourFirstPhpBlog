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
        $route = "";
        if((filter_input(INPUT_POST,"name") !== null))
        {
            if($socialNetworkId !== null)
            {
                $socialNetworkRepository = $this->entityManager->getRepository
                (SocialNetwork::class);
                $socialNetwork = $socialNetworkRepository->findById($socialNetworkId);
                $socialNetwork->setName(filter_input(INPUT_POST,"name"));
                $this->entityManager->flush();
            }
            else
            {

                $socialNetwork = new SocialNetwork();
                $socialNetwork->setName(filter_input(INPUT_POST,"name"));
                $this->entityManager->persist($socialNetwork);
                $this->entityManager->flush();
            }
            $route .= "social_networks";
        }
        else
        {
            $route .= "social_networks_addition";
        }
        $this->redirectToRoute($route);

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
        $this->redirectToRoute("social_networks");
    }
}