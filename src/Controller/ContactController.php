<?php

namespace App\Controller;

use App\Database;

class ContactController
{
    private $db;
    
    public function __construct()
    {
        $this->db = new Database();
    }
    
    public function submitForm($post)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Méthode non autorisée'];
        }
        
        $name = htmlspecialchars(trim($post['name'] ?? ''));
        $email = htmlspecialchars(trim($post['email'] ?? ''));
        $subject = htmlspecialchars(trim($post['subject'] ?? ''));
        $message = htmlspecialchars(trim($post['message'] ?? ''));
        
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            return ['success' => false, 'message' => 'Tous les champs sont requis'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email invalide'];
        }
        
        try {
            $this->db->insert('contacts', [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            return ['success' => true, 'message' => 'Message envoyé avec succès!'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    public function getMessages()
    {
        return $this->db->select('contacts');
    }
}
