<?php if(!isset($_POST['path'])): ?>

<html>
  <body>
    <form action="tester.php" method="POST">
    <label for="host">Host:</label><br/><input type="text" id="host" name="host" size="97"/>
    <br/>
    <label for="path">Path:</label><br/><input type="text" id="path" name="path" size="97"/>
    <br/><br/>
    <label for="post">Post/Put Content</label><br/><textarea id="post" name="post" cols="70" rows="20"></textarea>
    <br/><br/>
    <label for="dopost">Do post request?</label><input type="checkbox" name="dopost" checked="true" value="1" />
    <br/>
    <label for="doput">Do put request?</label><input type="checkbox" name="doput" value="1" />
    <br/><br/>
    <input type="submit" value="DO IT" />
  </body>
</html>
<?php else:
  $server = $_POST['host']; 
  
  $encoded = '';
  $ch = curl_init($server.$_POST['path']);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  
  curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
  
  //TODO - reset this to something more reasonable
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
  
  //TODO - add a headers option to allow for setting custom headers
    
  $encoded = $_POST['post'];
  if(isset($_POST['dopost']) && $_POST['dopost']){
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,  $encoded);
  } else if(isset($_POST['doput']) && $_POST['doput']){
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS,  $encoded);
  }
  
  $result = curl_exec($ch); 
  $sentHeaders = curl_getinfo($ch, CURLINFO_HEADER_OUT);
  $sentContent = curl_getinfo($ch); 
  $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
  curl_close($ch);
  
  
  list($header, $body) = explode("\r\n\r\n", $result, 2);  
  $obj = new stdClass ;
  $obj->request_text = $sentHeaders;
  $obj->request = $sentContent;
  $obj->response_headers = $header;
  $obj->response_content = (stristr($content_type, 'json') ||stristr($content_type, 'javascript')) ? json_decode($body) : $body;
  echo json_encode($obj);
endif;

?>