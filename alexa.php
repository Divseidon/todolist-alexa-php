<?php

/// ALEXA

// Get request data from Alexa
$data = file_get_contents('php://input');
$request = json_decode($data);

// Check if the request is for the LaunchRequest
if ($request->request->type === 'LaunchRequest') {
    // Return a welcome message
    $response = [
        'version' => '1.0',
        'response' => [
            'outputSpeech' => [
                'type' => 'PlainText',
                'text' => 'Bienvenue sur votre liste de tâches. Que puis-je faire pour vous?'
            ],
            'shouldEndSession' => false
        ]
    ];
    echo json_encode($response);
    exit;
}

// Check if the request is for the AddTodoIntent
if ($request->request->type === 'IntentRequest' && $request->request->intent->name === 'AddTodoIntent') {
    // Check if the todos slot is present in the request
    if (!isset($request->request->intent->slots->todos)) {
        // Handle the error by returning a response with a message asking the user to provide a todo
        $response = [
            'version' => '1.0',
            'response' => [
                'outputSpeech' => [
                    'type' => 'PlainText',
                    'text' => 'Veuillez fournir une tâche à ajouter à votre liste'
                ],
                'shouldEndSession' => false
            ]
        ];
        echo json_encode($response);
        exit;
    }

    // Get the todo from the request
    $todo = $request->request->intent->slots->todos->value;

    if (!$todo) {
        $error = ERROR_REQUIRED;
    } else if (mb_strlen($todo) < 5) {
        $error = ERROR_TOO_SHORT;
    }

    if (!$error) {
        $todos = [...$todos, [
            'name' => $todo,
            'done' => false,
            'id' => time()
        ]];
        file_put_contents($filename, json_encode($todos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        // Return a success message
        $response = [
            'version' => '1.0',
            'response' => [
                'outputSpeech' => [
                    'type' => 'PlainText',
                    'text' => "La tâche $todo a été ajoutée avec succès"
                ],
                'shouldEndSession' => false
            ]
        ];
        echo json_encode($response);
        exit;
    } else {
        // Return an error message
        $response = [
            'version' => '1.0',
            'response' => [
                'outputSpeech' => [
                    'type' => 'PlainText',
                    'text' => $error
                ],
                'shouldEndSession' => false
            ]
        ];
        echo json_encode($response);
        exit;
    }
}


// ALEXA FIN