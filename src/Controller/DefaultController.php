<?php

namespace AcMarche\Leasing\Controller;

use AcMarche\Leasing\Form\LeasingSendType;
use AcMarche\Leasing\Form\LeasingType;
use AcMarche\Leasing\Handler\LeasingFactory;
use AcMarche\Leasing\Handler\PdfDownloaderTrait;
use AcMarche\Leasing\Mailer\MailerLeasing;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/leasing')]
class DefaultController extends AbstractController
{
    use PdfDownloaderTrait;

    public function __construct(
        private readonly LeasingFactory $leasingFactory,
        private readonly MailerLeasing $mailerLeasing,
        #[Autowire(env: 'GRH_LEASING_EMAIL')]
        private readonly string $mailTo,
        #[AutowireLocator('app.leasing_calculator')]
        private ContainerInterface $handlers,
    ) {}

    #[Route(path: '/', name: 'leasing_index', methods: ['GET'])]
    public function index(): Response
    {
        $leasings = [];

        return $this->render(
            '@AcMarcheLeasing/leasing/index.html.twig',
            [
                'leasings' => $leasings,
            ],
        );
    }

    #[Route(path: '/new', name: 'leasing_new')]
    public function new(Request $request): Response
    {
        //$request->getSession()->remove('leasing_uuid');
        $uuid = LeasingFactory::getUuid($request);
        $leasingData = $this->leasingFactory->sampleData($uuid);

        $form = $this->createForm(LeasingType::class, $leasingData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $statut = $form->getData()->statut;

            try {
                $leasingCalculator = $this->handlers->get($statut);
                $leasingCalculator->calculate($leasingData);
                $leasingData->calculated = true;
                $this->addFlash('success', 'Leasing calculé');
            } catch (\Exception $exception) {
                $this->addFlash('danger', $exception->getMessage());
            }

            try {
                $this->leasingFactory->createHtml($leasingData);
            } catch (\Exception $exception) {
                $this->addFlash('danger', 'Erreur création du pdf : '.$exception->getMessage());
            }
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@AcMarcheLeasing/leasing/new.html.twig',
            [
                'form' => $form,
                'leasingData' => $leasingData,
            ],
            $response,
        );
    }

    #[Route(path: '/send', name: 'leasing_send', methods: ['GET', 'POST'])]
    public function send(
        Request $request,
    ): Response {
        $form = $this->createForm(LeasingSendType::class);
        if (!$this->leasingFactory->checkHtmlExist($request)) {
            $this->addFlash('danger', 'Veuillez remplir l\'étape une.');

            return $this->redirectToRoute('leasing_new');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $namePdf = $this->leasingFactory->createPdf($request, $data);
            } catch (\Exception $exception) {
                $this->addFlash('danger', 'Erreur lors de la création du pdf : '.$exception->getMessage());

                return $this->redirectToRoute('leasing_new');
            }

            $filePdfPath = $this->getTmpDir().$namePdf;
            try {
                $message = $this->mailerLeasing->sendLeasing($data, $filePdfPath, $this->mailTo);
                $this->mailerLeasing->send($message);
                $this->addFlash('success', 'Votre demande a bien été envoyée');

                try {
                    $this->leasingFactory->cleanFiles($request);
                } catch (\Exception) {
                }

                return $this->redirectToRoute('leasing_new');
            } catch (\Exception|TransportExceptionInterface $exception) {
                $this->addFlash('danger', 'Erreur lors de l\'envoie de votre demande : '.$exception->getMessage());
            }
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@AcMarcheLeasing/leasing/send.html.twig',
            [
                'form' => $form,
                'mailTo' => $this->mailTo,
            ],
            $response,
        );
    }

    #[Route(path: '/info', name: 'leasing_info')]
    public function info(): Response
    {
        return $this->render(
            '@AcMarcheLeasing/leasing/info.html.twig',
            [

            ],
        );
    }
}