<?php
  $data = json_decode(file_get_contents('php://input'), true);
  $manifest = $data["manifest"];
  $favicon = $data["icon144"];
  $icon72 = $data["icon72"];
  $icon96 = $data["icon96"];
  $icon128 = $data["icon128"];
  $icon144 = $data["icon144"];
  $icon152 = $data["icon152"];
  $icon192 = $data["icon192"];
  $icon384 = $data["icon384"];
  $icon512 = $data["icon512"];
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
    $file = fopen("manifest.webmanifest", "w+");
    fwrite($file, json_encode($manifest));
    fclose($file);
    $favicon = str_replace('data:image/png;base64,', '', $favicon);
    $favicon = str_replace(' ', '+', $favicon);
    file_put_contents("assets/favicon.png", base64_decode($favicon));
    
    $icon72 = str_replace('data:image/png;base64,', '', $icon72);
    $icon72 = str_replace(' ', '+', $icon72);
    file_put_contents("assets/icons/72.png", base64_decode($icon72));
    
    $icon96 = str_replace('data:image/png;base64,', '', $icon96);
    $icon96 = str_replace(' ', '+', $icon96);
    file_put_contents("assets/icons/96.png", base64_decode($icon96));
    
    $icon128 = str_replace('data:image/png;base64,', '', $icon128);
    $icon128 = str_replace(' ', '+', $icon128);
    file_put_contents("assets/icons/128.png", base64_decode($icon128));
    
    $icon144 = str_replace('data:image/png;base64,', '', $icon144);
    $icon144 = str_replace(' ', '+', $icon144);
    file_put_contents("assets/icons/144.png", base64_decode($icon144));
    
    $icon152 = str_replace('data:image/png;base64,', '', $icon152);
    $icon152 = str_replace(' ', '+', $icon152);
    file_put_contents("assets/icons/152.png", base64_decode($icon152));
    
    $icon192 = str_replace('data:image/png;base64,', '', $icon192);
    $icon192 = str_replace(' ', '+', $icon192);
    file_put_contents("assets/icons/192.png", base64_decode($icon192));
    
    $icon384 = str_replace('data:image/png;base64,', '', $icon384);
    $icon384 = str_replace(' ', '+', $icon384);
    file_put_contents("assets/icons/384.png", base64_decode($icon384));
    
    $icon512 = str_replace('data:image/png;base64,', '', $icon512);
    $icon512 = str_replace(' ', '+', $icon512);
    file_put_contents("assets/icons/512.png", base64_decode($icon512));
    header('Content-Type: application/json');
    echo json_encode(array("response" => "Updated successfully."));
  } else {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "You do not have access to this page."));
  }
?>