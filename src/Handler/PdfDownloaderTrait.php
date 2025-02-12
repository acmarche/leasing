<?php

namespace AcMarche\Leasing\Handler;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Contracts\Service\Attribute\Required;

trait PdfDownloaderTrait
{
    public Pdf $pdf;
    public string $projectDir;

    #[Required]
    public function setProjetDir(#[Autowire('%kernel.project_dir%')]string $projectDir): void
    {
        $this->projectDir = $projectDir;
    }

    public function getTmpDir(): string
    {
       return  $this->projectDir.'/var/tmp/';
    }

    #[Required]
    public function setPdf(Pdf $pdf): void
    {
        $this->pdf = $pdf;
    }

    public function getPdf(): Pdf
    {
        return $this->pdf;
    }

    public function downloadPdf(string $html, string $fileName, bool $debug = false, array $options = []): Response
    {
        if ($debug) {
            return new Response(
                $html,
                status: Response::HTTP_ACCEPTED,
            );
        }

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html, $options),
            fileName: $fileName,
            status: Response::HTTP_ACCEPTED,
        );
    }

    public function generateAndSavePdf(
        string $html,
        string $filePath,
        bool $debug = false,
        bool $overwrite = false,
        array $options = [],
    ): string {
        if ($debug) {
            return new Response(
                $html,
                status: Response::HTTP_ACCEPTED,
            );
        }
        $this->pdf->generateFromHtml($html, $filePath, overwrite: $overwrite);

        return $filePath;
    }

    public function readFromPath(
        string $filePath,
        string $fileName,
        array $options = [],
    ): Response {
        $response = new BinaryFileResponse($filePath);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName,
        );

        return $response;
    }
}
