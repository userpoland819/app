<?php
  $data = json_decode(file_get_contents('php://input'), true);
  $domain = $data["domain"];
  $projectId = $data["projectId"];
  $postsSlug = $data["postsSlug"];
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

  if($user["fields"]["role"]["stringValue"] == "admin" && $domain && $projectId && $postsSlug) {
    $url = "https://" . $domain . "/";
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
      '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
      '<url>' .
      '  <loc>' . $url . '</loc>' .
      '  <changefreq>daily</changefreq>' .
      '  <priority>1.00</priority>' .
      '</url>';
    $postsApi = "https://firestore.googleapis.com/v1/projects/" . $projectId . "/databases/(default)/documents/posts/";
    $posts = json_decode(file_get_contents($postsApi), true);
    foreach($posts["documents"] as $i) {
      $xml = $xml . "<url>" . "<loc>" . $url . $postsSlug . strrchr( $i["name"], "/posts/") . "</loc>" . "<lastmod>" . $i["updateTime"] . "</lastmod>" . "<priority>1.00</priority></url>";
    }
  
    $xml = $xml . "</urlset>";
    $file = fopen("sitemap.xml", "w+");
    fwrite($file, $xml);
    fclose($file);
    header('Content-Type: application/json');
    echo json_encode(array("response" => "Updated successfully."));
  } else {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "You do not have access to this page."));
  }
?>