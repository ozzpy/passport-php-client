<!DOCTYPE html>
<html lang="en">
<head></head>
<body>
<?php
require 'PassportClient.php';
$passport = new PassportClient('722290b4-214d-4dd4-aedc-842c84fcbc4e', '127.0.0.1:9011');
$userId = "00000000-0000-0000-0000-000000000001";

$data = array();
$data['attributes'] = array();
$data['attributes']['attr1'] = "value1";
$data['attributes']['attr2'] = "value2";
$data["preferredLanguages"] = array("en");

$registration1 = array();
$registration1["registration"]["applicationId"] = "3c219e58-ed0e-4b18-ad48-f4f92793ae32";
$registration1["registration"]["data"] = $data;
$registration1["registration"]["id"] = "00000000-0000-0000-0000-000000000003";
$registration1["registration"]["roles"] = array("admin");
$registration1["registration"]["username"] = "username0";
$registration1["registration"]["usernameStatus"] = "ACTIVE";

$userJson = array();
$userJson["user"] = array();
$userJson["user"]["id"] = "00000000-0000-0000-0000-000000000001";
$userJson["user"]["active"] = true;
$userJson["user"]["email"] = "username@inversoft.com";
$userJson["user"]["password"] = "password";
$userJson["user"]["username"] = "username0";
$userJson["user"]["timezone"] = "Denver";
$userJson["user"]["twoFactorSecret"] = "secret";
$userJson["user"]["data"] = $data;
$userJson["user"]["registrations"] = array();

$registration2 = array();
$registration2["user"]["id"] = $userId;
$registration2["user"]["active"] = true;
$registration2["user"]["email"] = "username@inversoft.com";
$registration2["user"]["password"] = "password";
$registration2["user"]["username"] = "username0";
$registration2["user"]["timezone"] = "Denver";
$registration2["user"]["twoFactorSecret"] = "secret";
$registration2["user"]["data"] = $data;
$registration2["registration"]["applicationId"] = "954fb463-25e0-4a39-bf17-d971a96d6f80";
$registration2["registration"]["data"] = $data;
$registration2["registration"]["id"] = "00000000-0000-0000-0000-000000000004";
$registration2["registration"]["roles"] = array("test");
$registration2["registration"]["username"] = "username0";
$registration2["registration"]["usernameStatus"] = "ACTIVE";

$search = array();
$search['queryString'] = "inversoft";
$search['numberOfResults'] = 100;

$loginRequest = array();
$loginRequest["email"] = "username@inversoft.com";
$loginRequest["password"] = "password";
$loginRequest["applicationId"] = "3c219e58-ed0e-4b18-ad48-f4f92793ae32";

echo "<h3>create user</h3>";
$createResponse = $passport->createUser($userId,json_encode($userJson, JSON_PRETTY_PRINT));
echo "<p>status</p>";
echo $createResponse->status;
echo"</br>";
echo "<p>error</p>";
echo var_dump($createResponse->errorResponse);
echo"</br>";
echo "<p>success</p>";
echo var_dump($createResponse->successResponse);
echo"</br>";
echo "<p>exception</p>";
echo var_dump($createResponse->exception);
echo"</br>";
//
//echo "<h3>register user</h3>";
//$registrationResponse = $passport->register($userId, json_encode($registration1, JSON_PRETTY_PRINT));
//echo $registrationResponse;
//echo"</br>";

echo "<h3>retrieve user by ID</h3>";
$retrieveResponse = $passport->retrieveUser($userId);
echo "<p>status</p>";
echo $retrieveResponse->status;
echo"</br>";
echo "<p>error</p>";
echo var_dump($retrieveResponse->errorResponse);
echo"</br>";
echo "<p>success</p>";
echo var_dump($retrieveResponse->successResponse);
echo"</br>";
echo "<p>exception</p>";
echo var_dump($retrieveResponse->exception);
echo"</br>";


echo "<h3>retrieve user by Email</h3>";
$retrieveResponse = $passport->retrieveUserByEmail("username@inversoft.com");
echo "<p>status</p>";
echo $retrieveResponse->status;
echo"</br>";
echo "<p>error</p>";
echo var_dump($retrieveResponse->errorResponse);
echo"</br>";
echo "<p>success</p>";
echo var_dump($retrieveResponse->successResponse);
echo"</br>";
echo "<p>exception</p>";
echo var_dump($retrieveResponse->exception);
echo"</br>";

echo "<h3>search user by query</h3>";
$searchResponse = $passport->searchUsersByQueryString($search);
echo "<p>status</p>";
echo $searchResponse->status;
echo"</br>";
echo "<p>error</p>";
echo var_dump($searchResponse->errorResponse);
echo"</br>";
echo "<p>success</p>";
echo var_dump($searchResponse->successResponse);
echo"</br>";
echo "<p>exception</p>";
echo var_dump($searchResponse->exception);
echo"</br>";

//echo "<h3>retrieve user by email</h3>";
//$retrieveResponse = $passport->retrieveUserByEmail("username@inversoft.com");
//echo $retrieveResponse;
//echo"</br>";
//
//echo "<h3>retrieve user by username</h3>";
//$retrieveResponse = $passport->retrieveUserByUsername("username0");
//echo $retrieveResponse;
//echo"</br>";
//
//echo "<h3>login user</h3>";
//$retrieveResponse = $passport->login(json_encode($loginRequest, JSON_PRETTY_PRINT));
//echo $retrieveResponse;
//echo"</br>";
//
//echo "<h3>deactivate user</h3>";
//$deactivateResponse = $passport->deactivateUser("00000000-0000-0000-0000-000000000001");
//echo $deactivateResponse;
//echo"</br>";
//
//echo "<h3>reactivate user</h3>";
//$reactivateResponse = $passport->reactivateUser("00000000-0000-0000-0000-000000000001");
//echo $reactivateResponse;
//echo"</br>";
//
//echo "<h3>delete user</h3>";
//$deleteResponse = $passport->deleteUser("00000000-0000-0000-0000-000000000001");
//echo $deleteResponse;
//echo"</br>";
//
//echo "<h3>create and register user</h3>";
//$registrationResponse = $passport->register($userId, json_encode($registration2, JSON_PRETTY_PRINT));
//echo $registrationResponse;
//echo"</br>";
//
//echo "<h3>search user by ID</h3>";
//$searchResponse = $passport->searchUsers($userId);
//echo $searchResponse;
//echo"</br>";
//
//echo "<h3>search user by query</h3>";
//$searchResponse = $passport->searchUsersByQueryString($search);
//echo $searchResponse;
//echo"</br>";
//
//$passport->deleteUser("00000000-0000-0000-0000-000000000001"); //cleanup

?>
</body>
