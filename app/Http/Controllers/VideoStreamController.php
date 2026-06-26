<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoStreamController extends Controller
{
    public function stream($filePath)
    {
        $filePath = base64_decode($filePath);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        $fileSize = filesize($filePath);
        $fileName = basename($filePath);

        $response = new StreamedResponse(function () use ($filePath) {
            $file = fopen($filePath, 'r');
            while (!feof($file)) {
                echo fread($file, 1024 * 8);
            }
            fclose($file);
        });

        $response->headers->set('Content-Type', 'video/mp4');
        $response->headers->set('Content-Length', $fileSize);
        $response->headers->set('Content-Disposition', "inline; filename=\"$fileName\"");
        $response->headers->set('Accept-Ranges', 'bytes');

        return $response;
    }
}
