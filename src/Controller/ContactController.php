<?php
namespace App\Controller;

use App\Service\DatabaseService;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContactController extends BaseController
{
    private DatabaseService $database;
    private MailerService $mailer;

    public function __construct(
        \Twig\Environment $twig,
        DatabaseService $database,
        MailerService $mailer
    ) {
        parent::__construct($twig);
        $this->database = $database;
        $this->mailer = $mailer;
    }

    public function index()
    {
        return $this->render('pages/contact.html.twig', [
            'current_page' => 'contact'
        ]);
    }

    public function submit(Request $request): Response
    {
        if ($request->getMethod() !== 'POST') {
            return new JsonResponse(['error' => 'Method not allowed'], 405);
        }

        // Honeypot check - if this field is filled, it's likely a bot
        if (!empty($request->request->get('website'))) {
            // Log potential spam attempt but return success to confuse bots
            error_log('Potential spam detected from IP: ' . $request->getClientIp());
            return new JsonResponse([
                'success' => true,
                'message' => 'Vielen Dank für Ihre Nachricht. Ich werde mich schnellstmöglich bei Ihnen melden.'
            ]);
        }

        // Time-based check - form submitted too quickly (less than 3 seconds)
        $formTime = $request->request->get('form_time');
        if ($formTime && (time() - intval($formTime) < 3)) {
            error_log('Form submitted too quickly from IP: ' . $request->getClientIp());
            return new JsonResponse([
                'success' => true,
                'message' => 'Vielen Dank für Ihre Nachricht. Ich werde mich schnellstmöglich bei Ihnen melden.'
            ]);
        }

        // Rate limiting - max 5 submissions per hour per IP
        $clientIp = $request->getClientIp();
        if ($clientIp) {
            $recentSubmissions = $this->database->getRecentSubmissionsCount($clientIp, 60);
            if ($recentSubmissions >= 5) {
                return new JsonResponse([
                    'error' => 'Zu viele Anfragen. Bitte versuchen Sie es später erneut.'
                ], 429);
            }
        }

        $firstname = trim($request->request->get('firstname', ''));
        $lastname = trim($request->request->get('lastname', ''));

        $data = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'name' => $firstname . ' ' . $lastname,
            'email' => trim($request->request->get('email', '')),
            'phone' => trim($request->request->get('phone', '')),
            'message' => trim($request->request->get('message', '')),
            'timestamp' => date('d.m.Y H:i'),
            'ip_address' => $request->getClientIp(),
            'user_agent' => $request->headers->get('User-Agent')
        ];

        // Validate required fields
        $errors = [];
        if (empty($data['firstname'])) {
            $errors[] = 'Vorname ist erforderlich';
        }
        if (empty($data['lastname'])) {
            $errors[] = 'Nachname ist erforderlich';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Gültige E-Mail-Adresse ist erforderlich';
        }
        if (empty($data['phone'])) {
            $errors[] = 'Telefonnummer ist erforderlich';
        }
        if (empty($data['message'])) {
            $errors[] = 'Nachricht ist erforderlich';
        }

        if (!empty($errors)) {
            return new JsonResponse(['errors' => $errors], 400);
        }

        try {
            // Save to database
            $contactId = $this->database->saveContactEmail($data);
            
            // Send emails
            $contactEmailSent = $this->mailer->sendContactEmail($data);
            $confirmationEmailSent = $this->mailer->sendConfirmationEmail($data);
            
            // Log email sending
            if ($contactEmailSent) {
                $this->database->logEmail([
                    'contact_email_id' => $contactId,
                    'email_type' => 'contact',
                    'recipient_email' => $_ENV['SMTP_FROM'],
                    'subject' => 'Neue Kontaktanfrage über die Website',
                    'body' => 'Contact email sent',
                    'status' => 'sent',
                    'sent_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            if ($confirmationEmailSent) {
                $this->database->logEmail([
                    'contact_email_id' => $contactId,
                    'email_type' => 'confirmation',
                    'recipient_email' => $data['email'],
                    'subject' => 'Ihre Kontaktanfrage bei Psychotherapie Neuss',
                    'body' => 'Confirmation email sent',
                    'status' => 'sent',
                    'sent_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            // Update contact status
            $status = ($contactEmailSent && $confirmationEmailSent) ? 'sent' : 'failed';
            $this->database->updateContactEmailStatus($contactId, $status);
            
            return new JsonResponse([
                'success' => true,
                'message' => 'Vielen Dank für Ihre Nachricht. Ich werde mich schnellstmöglich bei Ihnen melden.'
            ]);
            
        } catch (\Exception $e) {
            error_log('Contact form error: ' . $e->getMessage());
            return new JsonResponse([
                'error' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.'
            ], 500);
        }
    }
}