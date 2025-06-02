<?php

namespace AcMarche\Leasing\Controller;

use AcMarche\Leasing\Entity\KilometerDto;
use AcMarche\Leasing\Form\KilometerForm;
use AcMarche\Leasing\Handler\KilometerHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/kilometer')]
class KilometerController extends AbstractController
{
    public function __construct(
        #[Autowire(env: 'BICYCLE_ALLOWANCE')]
        private readonly string $allowance,
        private readonly KilometerHandler $kilometerHandler
    ) {
    }

    #[Route(path: '/', name: 'leasing_kilometer_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $kilometerDto = new KilometerDto();
        $form = $this->createForm(KilometerForm::class, $kilometerDto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $result = $this->kilometerHandler->calculateBicycleAllowance($data);
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@AcMarcheLeasing/kilometer/index.html.twig',
            [
                'form' => $form,
                'allowance' => $this->allowance,
                'result' => $result ?? [],
            ],
            $response
        );
    }

}