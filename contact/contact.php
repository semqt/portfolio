<?php

// Configuration
$config = [
    'from_email' => 'noreply@yourdomain.com',
    'from_name' => 'Portfolio Contact Form',
    'to_email' => 'workwithsem@gmail.com',
    'to_name' => 'Sem',
    'subject_prefix' => '[Portfolio Contact] '
];

// Form fields configuration
$fields = [
    'name' => 'Name',
    'subject' => 'Subject',
    'email' => 'Email',
    'message' => 'Message'
];

// Response messages
$messages = [
    'success' => 'Thank you for your message. I will get back to you soon!',
    'error' => 'There was an error sending your message. Please try again later.',
    'validation' => 'Please fill in all required fields.'
];

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate required fields
    foreach ($fields as $field => $label) {
        if (empty($_POST[$field])) {
            throw new Exception($messages['validation']);
        }
    }

    // Validate email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }

    // Prepare email content
    $emailBody = "New contact form submission\n\n";
    $emailBody .= "----------------------------------------\n";

    foreach ($fields as $field => $label) {
        $value = strip_tags(trim($_POST[$field]));
        $emailBody .= "{$label}: {$value}\n";
    }

    $emailBody .= "\n----------------------------------------\n";
    $emailBody .= "Sent from: " . $_SERVER['HTTP_HOST'];

    // Setup email headers
    $headers = [
        'Content-Type: text/plain; charset="UTF-8"',
        'From: ' . $config['from_name'] . ' <' . $config['from_email'] . '>',
        'Reply-To: ' . $_POST['name'] . ' <' . $_POST['email'] . '>',
        'X-Mailer: PHP/' . phpversion()
    ];

    // Send email
    $subject = $config['subject_prefix'] . $_POST['subject'];
    
    if (!mail($config['to_email'], $subject, $emailBody, implode("\n", $headers))) {
        throw new Exception($messages['error']);
    }

    $response = [
        'type' => 'success',
        'message' => $messages['success']
    ];
} catch (Exception $e) {
    $response = [
        'type' => 'error',
        'message' => $e->getMessage()
    ];
}

// Send response
header('Content-Type: application/json');
echo json_encode($response);