<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use App\Models\ChatMessageModel;
use CodeIgniter\HTTP\ResponseInterface;

class Chat extends BaseController
{
    protected $usersModel;
    protected $chatModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->chatModel = new ChatMessageModel();
    }

    public function index()
    {
        return view('users/chat');
    }

    // Return messages visible to this user (global + targeted)
    public function getMessages()
    {
        $session = session();
        $userId = $session->get('user_id');

        if (! $userId) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)->setJSON(['error' => 'Not authenticated']);
        }

        $since = $this->request->getGet('since');
        $builder = $this->chatModel->groupStart()
            ->where('user_id', null)
            ->orWhere('user_id', (int)$userId)
            ->groupEnd()
            ->orderBy('created_at', 'ASC');

        if ($since) {
            $ts = date('Y-m-d H:i:s', strtotime($since));
            $builder->where('created_at >', $ts);
        }

        $messages = $builder->findAll() ?: [];

        // Build a friendly response for the user view where admin remains anonymous
        $user = $this->usersModel->find($userId);
        $userDisplay = $user ? trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) : null;

        $out = [];
        foreach ($messages as $m) {
            $author = 'System';
            if (isset($m['sender']) && $m['sender'] === 'admin') {
                $author = 'Admin';
            } elseif (isset($m['sender']) && $m['sender'] === 'user') {
                if (!empty($m['user_name'])) {
                    $author = $m['user_name'];
                } elseif ($userDisplay) {
                    $author = $userDisplay;
                } else {
                    $author = 'User';
                }
            }

            $out[] = array_merge($m, ['author' => $author]);
        }

        return $this->response->setJSON($out);
    }

    public function postMessage()
    {
        $session = session();
        $userId = $session->get('user_id');

        if (! $userId) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)->setJSON(['error' => 'Not authenticated']);
        }

        $text = $this->request->getPost('message');
        if (! $text) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['error' => 'Message required']);
        }

        $user = $this->usersModel->find($userId);
        $author = $user ? trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) : 'User';

        $data = [
            'external_id' => bin2hex(random_bytes(6)),
            'user_id' => (int)$userId,
            'sender' => 'user',
            'message' => $text,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Include user_name only if the column exists (keeps code safe if migration not run)
        try {
            $hasCol = false;
            try {
                $hasCol = (bool) $this->db->fieldExists('user_name', $this->chatModel->table);
            } catch (\Exception $inner) {
                // DB driver may throw if not available — keep false
                $hasCol = false;
            }
            if ($hasCol) {
                $data['user_name'] = $author;
            }
            // Log whether we included user_name for debugging when users report missing values
            log_message('info', 'Chat::postMessage user_name column exists=' . ($hasCol ? '1' : '0'));
        } catch (\Exception $e) {
            // ignore and proceed without user_name
            log_message('error', 'Chat::postMessage fieldExists check failed: ' . $e->getMessage());
        }

        $insertId = $this->chatModel->insert($data);
        $data['id'] = $insertId;
        return $this->response->setJSON($data);
    }
}
