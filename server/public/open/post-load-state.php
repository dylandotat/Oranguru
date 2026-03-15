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

// Select state
$select_state = $db->prepare('SELECT encrypted_state, version FROM state WHERE user_id = :user_id');
$select_state->bindParam(':user_id', $user_id);
$select_state->execute();
$select_state_result = $select_state->fetch(PDO::FETCH_ASSOC);

if ($select_state_result) {
  echo json_encode([
    'success' => true,
    'encrypted_state' => $select_state_result['encrypted_state'],
    'version' => intval($select_state_result['version']),
  ]);
}
else {
  echo json_encode([
    'success' => true,
    'encrypted_state' => null,
    'version' => 0,
  ]);
}

?>
