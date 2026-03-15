<?php

require $_SERVER['DIR_PACKAGES'] . '/vendor/autoload.php';
use Ramsey\Uuid\Uuid;

header('Content-Type: application/json');

function respond_with_failure() {
  echo json_encode([
    'success' => false,
  ]);
  exit();
}

// Get request body
$request_body_json = file_get_contents('php://input');
if ($request_body_json === false) {
  respond_with_failure();
}
$request_body = json_decode($request_body_json, true);
if ($request_body === null) {
  respond_with_failure();
}

// Get user ID
if (!array_key_exists('user_id', $request_body)) {
  respond_with_failure();
}
$user_id = $request_body['user_id'];

// Get API key
if (!array_key_exists('api_key', $request_body)) {
  respond_with_failure();
}
$api_key = $request_body['api_key'];

// Get encrypted bundle
if (!array_key_exists('encrypted_bundle', $request_body)) {
  respond_with_failure();
}
$encrypted_bundle = $request_body['encrypted_bundle'];

// Connect to database
if (file_exists($_SERVER['FILE_SQLITEDB'])) {
  $db = new PDO('sqlite:' . $_SERVER['FILE_SQLITEDB']);
}
else {
  respond_with_failure();
}

// Verify credentials
$select_user = $db->prepare('SELECT user_id FROM users WHERE user_id = :user_id AND api_key = :api_key');
$select_user->bindParam(':user_id', $user_id);
$select_user->bindParam(':api_key', $api_key);
$select_user->execute();
$select_user_result = $select_user->fetch(PDO::FETCH_ASSOC);
if (!$select_user_result) {
  respond_with_failure();
}

// Create pairing table if not exists
$db->exec('CREATE TABLE IF NOT EXISTS pairing (
        pairing_id TEXT PRIMARY KEY,
        encrypted_bundle TEXT NOT NULL,
        created_at INTEGER NOT NULL
    );'
);

// Clean up expired entries
$db->exec('DELETE FROM pairing WHERE created_at < ' . (time() - 300));

// Generate pairing ID and insert
$pairing_id = Uuid::uuid4()->toString();
$created_at = time();
$insert_pairing = $db->prepare('INSERT INTO pairing (pairing_id, encrypted_bundle, created_at) VALUES (:pairing_id, :encrypted_bundle, :created_at)');
$insert_pairing->bindParam(':pairing_id', $pairing_id);
$insert_pairing->bindParam(':encrypted_bundle', $encrypted_bundle);
$insert_pairing->bindParam(':created_at', $created_at);
$insert_pairing->execute();

// Respond with pairing ID
echo json_encode([
  'success' => true,
  'pairing_id' => $pairing_id,
]);

?>
