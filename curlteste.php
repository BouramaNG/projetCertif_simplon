<?php

// Remplacez l'URL par celle de votre endpoint
$endpoint = 'http://127.0.0.1:8091/api/register/dahra';

// Remplacez les valeurs par celles de votre formulaire
$data = [
    'email' => 'contact@gmail.com',
    'numeroTelephone' => '783718472',
    'password' => 'Passer123',
    'nom' => 'Dahra Al Azhar',
    'nomOuztas' => 'Cheikh Modou',
    'adresse' => 'Marabouts',
    'region' => 'Dakar',
    'numeroTelephoneOuztas' => '783718472',
    'nombreTalibe' => 100,
];

// Chemin absolu vers le fichier à télécharger
$filePath = __DIR__ . '/public/uploads/image.jpg';


// Initialisez une ressource cURL
$ch = curl_init($endpoint);

// Configuration des options cURL
$options = [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_HTTPHEADER => [
        'Content-Type: multipart/form-data',
    ],
];

// Ajoutez le fichier au champ imageFile
$options[CURLOPT_POSTFIELDS]['imageFile'] = new CURLFile($filePath, 'image/jpeg', 'imageFile');

curl_setopt_array($ch, $options);

// Exécutez la requête cURL
$response = curl_exec($ch);

// Vérifiez s'il y a des erreurs
if (curl_errno($ch)) {
    echo 'Erreur cURL : ' . curl_error($ch);
} else {
    // Traitez la réponse comme nécessaire
    echo $response;
}

// Fermez la ressource cURL
curl_close($ch);
