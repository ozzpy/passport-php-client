## Passport PHP Client ![semver 2.0.0 compliant](http://img.shields.io/badge/semver-2.0.0-brightgreen.svg?style=flat-square)
If you're integrating Passport with a PHP application, this library will speed up your development time.

For additional information and documentation on Passport refer to [https://www.inversoft.com](https://www.inversoft.com).

**Note:** This project uses the Savant build tool. To compile using using Savant, follow these instructions:

```bash
$ mkdir ~/savant
$ cd ~/savant
$ wget http://savant.inversoft.org/org/savantbuild/savant-core/1.0.0/savant-1.0.0.tar.gz
$ tar xvfz savant-1.0.0.tar.gz
$ ln -s ./savant-1.0.0 current
$ export PATH=$PATH:~/savant/current/bin/
```

Then, perform an integration build of the project by running:
```bash
$ sb int
```

For more information, checkout [savantbuild.org](http://savantbuild.org/).

### Examples Usages:

#### Build the Client

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