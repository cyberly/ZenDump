<?php
namespace ZenDump;
class ZdCurl {

  public $environment;

  public $envFile;

  public function __construct($environment) {
    $envFile = ".env";
    $environments = json_decode(file_get_contents($envFile));
    $this->user = $environments->$environment->username;
    $this->pass = $environments->$environment->password;
    $this->baseUrl = $environments->$environment->url;
    $this->status = null;

    //echo $this->environment->username;
  }

  protected function doCurl($url, $verb, $data) {
    echo "URL: $url", PHP_EOL;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERPWD, $this->user.":".$this->pass);
    curl_setopt($ch, CURLOPT_USERAGENT, "curl/7.26.0");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_URL, $url);
    switch($verb){
      case "GET":
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-type: application/json'
        ));
        break;
      case "GETFILE":
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/binary'
        ));
        break;
      case "POST":
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataEncoded);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-type: application/json',
          'Content-length: ' . strlen($dataEncoded)
        ));
        break;
      case "PUT":
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataEncoded);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-type: application/json',
          'Content-length: ' . strlen($dataEncoded)
        ));
        break;
      case "DELETE":
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-type: application/json'
      ));
    break;
    }
    //Should probably test for output, but this works for now.
    if ($verb == "GETFILE"){
        $output = curl_exec($ch);
    } else {
        $output = json_decode(curl_exec($ch), true);
    }
    $this->status = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    //$curlInfo = curl_getinfo($ch);
    curl_close($ch);
    //var_dump($curlInfo);
    return $output;
  }

  public function delete($endpoint){
    $resp = $this->doCurl($endpoint);
    $this->response = $resp;
  }

  public function get($endpoint) {
     $resp = $this->doCurl($this->isEndpoint($endpoint), "GET", null);
     $this->response = $resp;
     return $this;
  }

  public function getFile($endpoint) {
     $resp = $this->doCurl($this->isEndpoint($endpoint), "GETFILE", null);
     $this->response = $resp;
     return $this;
  }

  public function post($endpoint, $data) {
    $resp = $this->doCurl($endpoint, "POST", $data);
    $this->response = $resp;
  }

  public function put($endpoint, $data) {
    $resp = $this->doCurl($endpoint, "PUT", $data);
    $this->response = $resp;
  }

  public function isEndpoint($url) {
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
      //echo "$url \n";
      return $this->baseUrl . $url;
    } else {
      //echo "TRUED";
      return $url;
    }
  }
}
