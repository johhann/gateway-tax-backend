<?php

namespace App\Services;

use Exception;

class ImageToPdfService
{
    /**
     * @throws Exception
     */
    public function convertToPdf(string $sourcePath, string $destPath, int $targetBytes = 3145728): void
    {
        if (! is_file($sourcePath)) {
            throw new Exception('Source file not found for conversion.');
        }

        if (! class_exists(\Gmagick::class)) {
            $this->writePlaceholderPdf($destPath, basename($sourcePath));

            return;
        }

        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        $isPdf = $ext === 'pdf';

        try {
            $gm = new \Gmagick($sourcePath);

            if ($isPdf) {
                $gm->setimageformat('png');
            }

            $quality = 85;
            $scale = 1.0;
            $minQuality = 35;
            $minScale = 0.3;

            while (true) {
                $tmpBase = tempnam(sys_get_temp_dir(), 'pdfconv_');
                @unlink($tmpBase);
                $tmpPdf = $tmpBase.'.pdf';

                $gmTry = clone $gm;

                if ($scale < 1.0) {
                    $w = max(1, (int) ($gmTry->getimagewidth() * $scale));
                    $h = max(1, (int) ($gmTry->getimageheight() * $scale));
                    $gmTry->scaleimage($w, $h);
                }

                $gmTry->setcompressionquality($quality);
                $gmTry->setimageformat('pdf');
                $gmTry->writeimage($tmpPdf);
                $gmTry->destroy();

                clearstatcache();
                $size = is_file($tmpPdf) ? filesize($tmpPdf) : 0;

                if ($size > 0 && $size <= $targetBytes) {
                    @rename($tmpPdf, $destPath);
                    break;
                }

                @unlink($tmpPdf);

                if ($quality > $minQuality) {
                    $quality -= 10;
                } elseif ($scale > $minScale) {
                    $scale = round($scale - 0.1, 2);
                } else {
                    $gm->setcompressionquality($minQuality);
                    $gm->setimageformat('pdf');
                    $gm->writeimage($destPath);
                    break;
                }
            }

            $gm->destroy();
        } catch (Exception $e) {
            $this->writePlaceholderPdf($destPath, basename($sourcePath));
        }
    }

    private function writePlaceholderPdf(string $destPath, string $originalName): void
    {
        $text = 'Converted preview for '.$originalName;
        $pdf = '%PDF-1.4
1 0 obj<<>>endobj
2 0 obj<< /Length 73 >>stream
BT /F1 12 Tf 72 720 Td ($TEXT$) Tj ET
endstream
endobj
3 0 obj<< /Type /Font /Subtype /Type1 /Name /F1 /BaseFont /Helvetica >>endobj
4 0 obj<< /Type /Page /Parent 5 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 3 0 R >> >> /Contents 2 0 R >>endobj
5 0 obj<< /Type /Pages /Count 1 /Kids [4 0 R] >>endobj
6 0 obj<< /Type /Catalog /Pages 5 0 R >>endobj
xref
0 7
0000000000 65535 f
0000000010 00000 n
0000000053 00000 n
0000000170 00000 n
0000000271 00000 n
0000000424 00000 n
0000000482 00000 n
trailer<< /Size 7 /Root 6 0 R >>
startxref
548
%%EOF';
        $pdf = str_replace('$TEXT$', $this->escapePdfText($text), $pdf);
        file_put_contents($destPath, $pdf);
    }

    private function escapePdfText(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
