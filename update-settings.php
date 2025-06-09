<?php
  $data = json_decode(file_get_contents('php://input'), true);
  $settings = $data["settings"];
  $projectId = $data["projectId"];
  $accessToken = $data["accessToken"];
  $uid = $data["uid"];
  
  $usersApi = "https://firestore.googleapis.com/v1/projects/" . $projectId . "/databases/(default)/documents/users/" . $uid;
  $opts = array (
    'http' => array (
      'method' => 'GET',
      'header'=> "Authorization: Bearer " . $accessToken
    )
  );
  $user = json_decode(file_get_contents($usersApi, false, stream_context_create($opts)), true);

  if($user["fields"]["role"]["stringValue"] == "admin") {
    $file = fopen("assets/settings.json", "w+");
    fwrite($file, json_encode($settings));
    fclose($file);
    header('Content-Type: application/json');
    echo json_encode(array("response" => "Updated successfully."));
  } else {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "You do not have access to this page."));
  }
?>