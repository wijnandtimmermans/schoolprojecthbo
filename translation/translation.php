<?php
header('Content-Type: application/json');

$request_uri = getenv('REQUEST_URI');
$request_uri = str_replace('/?', '', $request_uri);
$request_uri = urldecode($request_uri);
$array = json_decode($request_uri);

$word = $array->search;
$lang_code = $array->language;

$jsonResponse = new stdClass();

/**
 * De functie returnError beeindigd het script en geeft een json object
 * terug met de error melding.
 *
 * @param $code code van de error
 * @param $message beschrijving van de error
 */
function returnError($code, $message) {
    $jsonResponse = new stdClass();
    $jsonResponse->success = false;
    $jsonResponse->error = new stdClass();
    $jsonResponse->error->code = $code;
    $jsonResponse->error->message = $message;
    die(json_encode($jsonResponse));
}

if (!isset($word)) {
    returnError(2, 'Invalid request!');
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=microservice;charset=utf8;", 'root', '');

    $getTranslationsSql = "SELECT `lang_code`, `translation`
                           FROM `words`
                           WHERE word = :word
                                 AND (`lang_code` = :lang OR :lang = 'all')";
    $getTranslations = $conn->prepare($getTranslationsSql);
    $getTranslations->bindValue(':word', $word);
    $getTranslations->bindValue(':lang', $lang_code);
    $getTranslations->execute();
    $translations = $getTranslations->fetchAll(PDO::FETCH_OBJ);
} catch(PDOException $e) {
    returnError(3, 'An unknown error occured!');
}

if (empty($translations)) {
    returnError(1, 'No translations found!');
}

$results = new stdClass();
foreach ($translations as $translation) {

    $langCode = $translation->lang_code;
    if (!isset($results->$langCode))
        $results->$langCode = array();

    array_push($results->$langCode, $translation->translation);
}

$jsonResponse->success = true;
$jsonResponse->results = $results;

echo json_encode($jsonResponse);