<?php

/**
 * @author Derek Klatt
 */
class RestClient
{
  public $headers = array();

  public $parameters = array();

  public $url;

  public $certificate;

  public $connectTimeout = 2000;

  public $key;

  public $method;

  public $readTimeout = 2000;

  public $request;

  public function __construct()
  {
    include_once 'ClientResponse.php';
  }

  public function authorization($key)
  {
    $this->headers[] = "Authorization:" . $key;
    return $this;
  }

  public function basicAuthorization($username, $password)
  {
    if (!$username && !$password) {
      $credentials = $username . ":" . $password;
      $encoded = base64_encode($credentials);
      $this->headers[] = "Authorization: " . "Basic " . $encoded;
    }
    return $this;
  }

  public function certificate($certificate)
  {
    $this->certificate = $certificate;
    return $this;
  }

  public function connectTimeout($connectTimeout)
  {
    $this->connectTimeout = $connectTimeout;
    return $this;
  }

  public function delete()
  {
    $this->method = "DELETE";
    return $this;
  }

  public function get()
  {
    $this->method = "GET";
    return $this;
  }

  public function go()
  {
    if (!$this->url) {
      throw new Exception("You must specify a URL");
    }

    if (!$this->method) {
      throw new Exception("You must specify a HTTP method");
    }

    $response = new ClientResponse();
    try {
      if ($this->parameters) {
        if (substr($this->url, -1) != '?') {
          $this->url = $this->url . '?';
        }
        $params = http_build_query($this->parameters);
        $this->url = $this->url . $params;
      }

      $curl = curl_init();
      if (substr($this->url, 0, 5) == "https" && !$this->certificate) {
        curl_setopt($curl, CURLOPT_SSLCERT, $this->certificate);
        if (!$this->key) {
          curl_setopt($curl, CURLOPT_SSLKEY, $this->key);
        }
      }
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, $this->connectTimeout);
      curl_setopt($curl, CURLOPT_TIMEOUT_MS, $this->readTimeout);
      curl_setopt($curl, CURLOPT_URL, $this->url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_POST, false);
      if ($this->method == 'POST') {
        curl_setopt($curl, CURLOPT_POST, true);
      } elseif ($this->method == 'PUT') {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
      } elseif ($this->method == 'DELETE') {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
      }
      if ($this->request) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->request);
      }

      curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);

      $result = curl_exec($curl);
    } catch (Exception $e) {
      $response->exception = $e;
      curl_close($curl);
      return $response;
    }

    try {
      $response->status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    } catch (Exception $e) {
      $response->exception = $e;
      curl_close($curl);
      return $response;
    }

    if ($response->status < 200 || $response->status > 299) {
      try {
        if ($result) {
          $response->errorResponse = json_decode($result);
        }
      } catch (Exception $e) {
        $response->exception = $e;
        curl_close($curl);
        return $response;
      }
    } else {
      try {
        if ($result) {
          $response->successResponse = json_decode($result);
        }
      } catch (Exception $e) {
        $response->exception = $e;
        curl_close($curl);
        return $response;
      }
    }
    curl_close($curl);
    return $response;
  }

  public function header($name, $value)
  {
    $this->headers[$name] = $value;
    return $this;
  }

  public function headers($headers)
  {
    $this->headers[] = $headers;
    return $this;
  }

  public function key($key)
  {
    $this->key = $key;
    return $this;
  }

  public function post()
  {
    $this->method = "POST";
    return $this;
  }

  public function put()
  {
    $this->method = "PUT";
    return $this;
  }

  public function readTimeout($readTimeout)
  {
    $this->readTimeout = $readTimeout;
    return $this;
  }

  public function request($request)
  {
    $this->request = $request;
    return $this->headers("Content-Type:application/json")->headers("Content-Length:" . strlen($request));
  }

  public function uri($uri)
  {
    if (!$this->url) {
      return $this;
    }

    if (substr($this->url, -1) == '/' && substr($uri, 1, 1) == '/') {
      $this->url = $this->url . ltrim($uri, '/');
    } else if (substr($this->url, -1) != '/' && substr($uri, 0, 1) != '/') {
      $this->url = $this->url . '/' . $uri;
    } else {
      $this->url = $this->url . $uri;
    }

    return $this;
  }

  public function url($url)
  {
    $this->url = $url;
    return $this;
  }

  public function urlParameter($name, $value)
  {
    if (!$value) {
      return $this;
    }

    $this->parameters[$name] = $value;

    return $this;
  }

  public function urlSegment($value)
  {
    if ($value) {
      if (substr($this->url, -1) != '/') {
        $this->url = $this->url . '/';
      }
      $this->url = $this->url . $value;
    }
    return $this;
  }

}