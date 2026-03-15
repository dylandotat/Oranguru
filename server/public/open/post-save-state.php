<?php

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

// Get encrypted state
if (!array_key_exists('encrypted_state', $request_body)) {
  respond_with_failure();
}
$encrypted_state = $request_body['encrypted_state'];
if (!str_starts_with($encrypted_state, '-----BEGIN PGP MESSAGE-----')) {
  respond_with_failure();
}

// Get version
if (!array_key_exists('version', $request_body)) {
  respond_with_failure();
}
$version = intval($request_body['version']);

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

// Create state table if not exists
$db->exec('CREATE TABLE IF NOT EXISTS state (
        user_id TEXT PRIMARY KEY,
        encrypted_state TEXT NOT NULL,
        version INTEGER NOT NULL
    );'
);

// Upsert state
$upsert_state = $db->prepare('INSERT INTO state (user_id, encrypted_state, version) VALUES (:user_id, :encrypted_state, :version) ON CONFLICT(user_id) DO UPDATE SET encrypted_state = :encrypted_state2, version = :version2 WHERE version < :version3');
$upsert_state->bindParam(':user_id', $user_id);
$upsert_state->bindParam(':encrypted_state', $encrypted_state);
$upsert_state->bindParam(':version', $version);
$upsert_state->bindParam(':encrypted_state2', $encrypted_state);
$upsert_state->bindParam(':version2', $version);
$upsert_state->bindParam(':version3', $version);
$upsert_state->execute();

// Respond with success
echo json_encode([
  'success' => true,
]);

?>
