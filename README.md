## Passport PHP Client ![semver 2.0.0 compliant](http://img.shields.io/badge/semver-2.0.0-brightgreen.svg?style=flat-square)
If you're integrating Passport with a PHP application, this library will speed up your development time.

For additional information and documentation on Passport refer to [https://www.inversoft.com](https://www.inversoft.com).

### Examples Usages:

#### Install the Code

To use the client library on your project simply copy the PHP source files from the `src` directory to your project.

#### Create the Client

```PHP
$apiKey = "5a826da2-1e3a-49df-85ba-cd88575e4e9d";
$client = new PassportClient($apiKey, "http://localhost:9011");
```

#### Login a user

```PHP
$applicationId = "68364852-7a38-4e15-8c48-394eceafa601";

$request = array();
$request["applicationId"] = $applicationId;
$request["email"] = "joe@inversoft.com";
$request["password"] = "abc123";
$result = client->login(json_encode($request));
if (!$result->wasSuccessful()) {
 // Error
}

// Hooray! Success
```
