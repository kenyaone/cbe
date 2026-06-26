<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContentController extends Controller
{
    public function serveInteractive($filePath)
    {
        $filePath = base64_decode($filePath);

        if (!file_exists($filePath) || !str_ends_with($filePath, '.html')) {
            abort(404, 'Interactive not found');
        }

        return response()->file($filePath, [
            'Content-Type' => 'text/html; charset=utf-8'
        ]);
    }

    public function servePdf($filePath)
    {
        $filePath = base64_decode($filePath);

        if (!file_exists($filePath) || !str_ends_with($filePath, '.pdf')) {
            abort(404, 'PDF not found');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }

    public function embedInteractive($filePath)
    {
        $filePath = base64_decode($filePath);

        if (!file_exists($filePath) || !str_ends_with($filePath, '.html')) {
            abort(404, 'Interactive not found');
        }

        $content = file_get_contents($filePath);
        return view('content.interactive-embed', compact('content'));
    }

}
