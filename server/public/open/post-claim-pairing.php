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

// Get pairing ID
if (!array_key_exists('pairing_id', $request_body)) {
  respond_with_failure();
}
$pairing_id = $request_body['pairing_id'];

// Connect to database
if (file_exists($_SERVER['FILE_SQLITEDB'])) {
  $db = new PDO('sqlite:' . $_SERVER['FILE_SQLITEDB']);
}
else {
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

// Select and delete pairing
$select_pairing = $db->prepare('SELECT encrypted_bundle FROM pairing WHERE pairing_id = :pairing_id');
$select_pairing->bindParam(':pairing_id', $pairing_id);
$select_pairing->execute();
$select_pairing_result = $select_pairing->fetch(PDO::FETCH_ASSOC);
if (!$select_pairing_result) {
  respond_with_failure();
}

$delete_pairing = $db->prepare('DELETE FROM pairing WHERE pairing_id = :pairing_id');
$delete_pairing->bindParam(':pairing_id', $pairing_id);
$delete_pairing->execute();

// Respond with encrypted bundle
echo json_encode([
  'success' => true,
  'encrypted_bundle' => $select_pairing_result['encrypted_bundle'],
]);

?>
