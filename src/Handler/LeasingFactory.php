<?php

namespace AcMarche\Leasing\Handler;

use AcMarche\Leasing\Entity\LeasingData;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class LeasingFactory
{
    use PdfDownloaderTrait;

    public function __construct(private readonly Environment $environment) {}

    public function sampleData(string $uuid): LeasingData
    {
        $leasingData = new LeasingData(0, 0);
        $leasingData->statut = 'contractuel';
        $leasingData->grossAnnualFull = 0;
        $leasingData->monthlyHouseholdResidency = 0;
        $leasingData->indexValue = 0;
        $leasingData->workRegime = 0;
        $leasingData->taxableAnnualAFA = 0;
        $leasingData->uuid = $uuid;

        return $leasingData;
    }

    /**
     * @param LeasingData $leasingData
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function createHtml(LeasingData $leasingData): string
    {
        $html = $this->environment->render(
            '@AcMarcheLeasing/leasing/_html_leasing.html.twig',
            [
                'leasingData' => $leasingData,
            ],
        );

        $name = $this->getFileName($leasingData->uuid, 'html');
        $filePath = $this->getTmpDir().$name;

        $filesystem = new Filesystem();
        $filesystem->dumpFile($filePath, $html);

        return $name;
    }

    /**
     * @param Request $request
     * @param array $data
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function createPdf(Request $request, array $data = []): string
    {
        $uuid = self::getUuid($request);

        $fileNameHtml = $this->getFileName($uuid, 'html');
        $filePathHtml = $this->getTmpDir().$fileNameHtml;
        $htmlLeasing = file_get_contents($filePathHtml);

        $html = $this->environment->render(
            '@AcMarcheLeasing/leasing/pdf.html.twig',
            [
                'data' => $data,
                'htmlLeasing' => $htmlLeasing,
            ],
        );

        $fileNamePdf = $this->getFileName($uuid, 'pdf');
        $filePath = $this->getTmpDir().$fileNamePdf;
        $this->generateAndSavePdf($html, $filePath);

        return $fileNamePdf;
    }

    /**
     * @param Request $request
     * @return void
     */
    public function cleanFiles(Request $request): void
    {
        $uuid = self::getUuid($request);
        $filesystem = new Filesystem();
        $fileNameHtml = $this->getFileName($uuid, 'html');
        $fileNamePdf = $this->getFileName($uuid, 'pdf');

        $filePathPdf = $this->getTmpDir().$fileNameHtml;
        $filePathHtml = $this->getTmpDir().$fileNamePdf;

        $request->getSession()->remove('leasing_uuid');
        $filesystem->remove([$filePathPdf, $filePathHtml]);
    }

    public static function getUuid(Request $request): string
    {
        if (!$request->getSession()->has('leasing_uuid')) {
            $request->getSession()->set('leasing_uuid', Uuid::v4()->toString());
        }

        return $request->getSession()->get('leasing_uuid');
    }

    public function getFileName(string $uuid, string $extension): string
    {
        return 'grh_leasing_'.$uuid.'.'.$extension;
    }

}