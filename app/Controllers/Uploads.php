<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class Uploads extends BaseController
{
    /**
     * Serve uploaded files from public/uploads or writable/uploads.
     * URL pattern: /uploads/{folder}/{filename}
     */
    public function serve(string $folder = null, string $filename = null)
    {
        // Basic sanitization: allow only safe filenames and folder names
        $folder = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string) $folder);
        $filename = basename($filename);

        if (empty($folder) || empty($filename)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Candidate paths
        $publicPath = FCPATH . 'uploads/' . $folder . '/' . $filename;
        $writablePath = WRITEPATH . 'uploads/' . $folder . '/' . $filename;

        if (is_file($publicPath) && is_readable($publicPath)) {
            $path = $publicPath;
        } elseif (is_file($writablePath) && is_readable($writablePath)) {
            $path = $writablePath;
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("File not found: {$folder}/{$filename}");
        }

        // Determine mime type
        $mime = mime_content_type($path) ?: 'application/octet-stream';

        // Stream file
        $this->response->setHeader('Content-Type', $mime);
        $this->response->setHeader('Content-Length', (string) filesize($path));

        // Read file and output
        // Using readfile and exit to ensure no extra output is appended
        ob_clean();
        flush();
        readfile($path);
        exit;
    }

}
