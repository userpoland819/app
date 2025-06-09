<?php
  $data = json_decode(file_get_contents('php://input'), true);
  $language = $data["language"];
  $domain = $data["domain"];
  $websiteName = $data["websiteName"];
  $fbAppID = $data["fbAppID"];
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
  
  if($user["fields"]["role"]["stringValue"] == "admin" && $language && $domain && $websiteName && $projectId) {
    $php = '<!DOCTYPE html>' . PHP_EOL .
      '<html lang="' . $language . '">' . PHP_EOL .
      '<head>' . PHP_EOL .
      '<?php' . PHP_EOL .
      '$language = "' . $data["language"] . '";' . PHP_EOL .
      '$domain = "' . $data["domain"] . '";' . PHP_EOL .
      '$websiteName = "'  . $data["websiteName"] . '";' . PHP_EOL .
      '$fbAppID = "' . $data["fbAppID"] . '";' . PHP_EOL .
      '$projectId = "' . $data["projectId"] . '";' . PHP_EOL . PHP_EOL .
      '$postsSlug = "' . $data["postsSlug"] . '";' . PHP_EOL . PHP_EOL .
      
      '$url = "https://" . $domain . "/";' . PHP_EOL .
      '$slug = substr($_SERVER["REQUEST_URI"], strrpos($_SERVER["REQUEST_URI"], "?") + 1);' . PHP_EOL .
      '$postUrl = "https://" . $domain . "/post/" . $slug;' . PHP_EOL .
      '$api = "https://firestore.googleapis.com/v1/projects/" . $projectId . "/databases/(default)/documents/posts/" . $slug;' . PHP_EOL .
      '$data = json_decode(file_get_contents($api), true);' . PHP_EOL .
      '$title = $data["fields"]["title"]["stringValue"] . " • " . $websiteName;' . PHP_EOL .
      '$description = substr($data["fields"]["content"]["stringValue"], 0, 150);' . PHP_EOL .
      '$descriptionFull = $data["fields"]["content"]["stringValue"];' . PHP_EOL .
      '$image = "https://firebasestorage.googleapis.com/v0/b/" . $projectId . ".appspot.com/o/" . str_replace("/","%2F",$data["fields"]["image"]["stringValue"]) . "?alt=media";' . PHP_EOL .
      '$date = date("Y-m-d\TH:i:s.Z\Z", $data["fields"]["created"]["integerValue"]);' . PHP_EOL . PHP_EOL .
      '?>' . PHP_EOL . PHP_EOL .

      '<title><?php echo $title; ?></title>' . PHP_EOL .
      '<meta name="description" content="<?php echo $description; ?>">' . PHP_EOL . PHP_EOL .

      '<!-- Facebook Meta Tags -->' . PHP_EOL .
      '<meta property="og:title" content="<?php echo $title; ?>">' . PHP_EOL .
      '<meta property="og:description" content="<?php echo $description; ?>">' . PHP_EOL .
      '<meta property="og:image" content="<?php echo $image; ?>">' . PHP_EOL .
      '<meta property="og:image:alt" content="<?php echo $title; ?>">' . PHP_EOL .
      '<meta property="og:type" content="website">' . PHP_EOL .
      '<meta property="og:url" content="<?php echo $postUrl; ?>">' . PHP_EOL .
      '<meta property="og:site_name" content="<?php echo $websiteName; ?>">' . PHP_EOL .
      '<meta property="fb:app_id" content="<?php echo $fbAppID; ?>">' . PHP_EOL . PHP_EOL .

      '<!-- Twitter Meta Tags -->' . PHP_EOL .
      '<meta name="twitter:card" content="summary_large_image">' . PHP_EOL .
      '<meta property="twitter:domain" content="<?php echo $domain; ?>">' . PHP_EOL .
      '<meta property="twitter:url" content="<?php echo $url; ?>">' . PHP_EOL .
      '<meta name="twitter:title" content="<?php echo $title; ?>">' . PHP_EOL .
      '<meta name="twitter:description" content="<?php echo $description; ?>">' . PHP_EOL .
      '<meta name="twitter:image" content="<?php echo $image; ?>">' . PHP_EOL . PHP_EOL .
    
      '<!-- SCHEMA MARKUP -->' . PHP_EOL .
      '<script type="application/ld+json">' . PHP_EOL .
      '{' . PHP_EOL .
        '"@context": "https://schema.org",' . PHP_EOL .
        '"@graph": [' . PHP_EOL .
        '  {' . PHP_EOL .
        '    "@type": "Article",' . PHP_EOL .
        '    "author": "<?php echo $websiteName; ?>",' . PHP_EOL .
        '    "datePublished": "<?php echo $date; ?>",' . PHP_EOL .
        '    "headline": "<?php echo $title; ?>",' . PHP_EOL .
        '    "description": "<?php echo $description; ?>",' . PHP_EOL .
        '    "name":"<?php echo $title; ?>",' . PHP_EOL .
        '    "image": {' . PHP_EOL .
        '      "@type": "ImageObject",' . PHP_EOL .
        '      "@id": "<?php echo $image; ?>",' . PHP_EOL .
        '      "url": "<?php echo $image; ?>",' . PHP_EOL .
        '      "height": 670,' . PHP_EOL .
        '      "width": 1200,' . PHP_EOL .
        '      "caption": "<?php echo $title; ?>"' . PHP_EOL .
        '    },' . PHP_EOL .
        '    "thumbnailUrl": "<?php echo $image; ?>"' . PHP_EOL .
        '  }' . PHP_EOL .
        ']' . PHP_EOL .
      '}' . PHP_EOL .
      '</script>' . PHP_EOL . PHP_EOL .
    
      '</head>' . PHP_EOL .
      '<body>' . PHP_EOL .
      '  <div style="background-color: rgba(247, 247, 247, 1); border-radius: 24px; padding: 32px; margin: 0px auto 0px auto; width: 80%; max-width: 800px; position: relative;">' . PHP_EOL .
      '    <p style="color: blue; text-decoration: underline; font-size: 16px; font-weight: 700;"><?php echo $slug; ?></p>' . PHP_EOL .
      '    <p style="font-size: 24px; font-weight: 700;"><?php echo $title; ?></p>' . PHP_EOL .
      '    <img style="width: calc(100% - 0px); border-radius: 24px;" src="<?php echo $image; ?>">' . PHP_EOL .
      '    <p><?php echo $descriptionFull; ?></p>' . PHP_EOL .
      '  </div>' . PHP_EOL .
      '</body>' . PHP_EOL .
      '</html>';

    $file = fopen("posts.php", "w+");
    fwrite($file, $php);
    fclose($file);
    header('Content-Type: application/json');
    echo json_encode(array("response" => "Updated successfully."));
  } else {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "You do not have access to this page."));
  }
?>