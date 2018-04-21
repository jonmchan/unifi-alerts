<?php


require_once('vendor/autoload.php');


## Can you tell I've been coding lots of ruby lately? :P

$controller_user = getenv('UNIFI_USER');
$controller_password = getenv('UNIFI_PASS');
$controller_url = getenv('UNIFI_URL');
$site_id = getenv('SITE_ID');
$controller_version = getenv('CONTROLLER_VERSION');
$smtp_server = getenv('SMTP_HOST') ? getenv('SMTP_HOST') : 'localhost' ;
$smtp_port = getenv('SMTP_PORT') ? getenv('SMTP_PORT') : 25;
$smtp_user = getenv('SMTP_USER');
$smtp_pass = getenv('SMTP_PASS');
$smtp_from = getenv('SMTP_FROM');
$smtp_to = getenv('SMTP_TO');

if(filter_var(getenv('SMTP_TLS'),FILTER_VALIDATE_BOOLEAN)) {
  $smtp_tls = true;
  $smtp_secure = 'tls';
} else {
  $smtp_tls = false;
  $smtp_secure = false;
}
if (filter_var(getenv('SMTPDEBUG'),FILTER_VALIDATE_BOOLEAN)) {
  $smtpdebug = 2;
} else {
  $smtpdebug = 0;
}

if ($controller_url == 'https://unifi.hostname' || $controller_url === false) {
  exit("You must set the UNIFI_USER, UNIFI_PASS, UNIFI_URL, SITE_ID, and CONTROLLER_VERSION environment variables.\n");
}


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //Server settings
    $mail->SMTPDebug = $smtpdebug;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = $smtp_server;  // Specify main and backup SMTP servers
    $mail->SMTPAuth =  ($smtp_user || $smtp_pass) ? true : false;                               // Enable SMTP authentication
    $mail->SMTPAutoTLS = $smtp_tls;
    $mail->Username = $smtp_user;                 // SMTP username
    $mail->Password = $smtp_pass;   
    $mail->SMTPSecure = $smtp_false;
    $mail->Port = $smtp_port;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom($smtp_from);
    $mail->addAddress($smtp_to);     // Add a recipient

    //Content
    $mail->isHTML(false);                                  // Set email format to HTML
} catch (Exception $e) {
    echo 'Email setup did not work... Mailer Error: ', $mail->ErrorInfo;
}

/* # debug stuff for lazy dev.
echo $controller_user;
echo "\n";
echo $controller_password;
echo "\n";
echo $controller_url;
echo "\n";
*/
$unifi_connection = new UniFi_API\Client($controller_user, $controller_password, $controller_url, $site_id, $controller_version, false);
$login            = $unifi_connection->login();

$serialized_data = file_get_contents('data/hosts');
if ($serialized_data === false) {
  $known_hosts = array();
} else {
  $known_hosts = unserialize($serialized_data);
}

while(true) {
  $results = $unifi_connection->list_clients(); // returns a PHP array containing alarm objects
  if(!is_array($results)) {
    exit("Results returning bad data...");
  }
  foreach($results as $client) {
    if (isset($known_hosts[$client->mac])) {
      continue;
    }
    $known_hosts[$client->mac] = $client;

    $mail->Subject = "Never Seen $client->mac - ($client->hostname) connecting to $client->essid";
    $mail->Body = "Full Info: " . json_encode($client, JSON_PRETTY_PRINT) . "\n\n"; 

    try {
      $mail->send(); 
    } catch (Exception $e) {
      echo 'Email sending did not work... Mailer Error: ', $mail->ErrorInfo; 
    }
  }

  file_put_contents('data/hosts',serialize($known_hosts));

  sleep(15);
  echo "polling again...\n";
}
?>
