<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/PassportClient.php';

/**
 * @covers PassportClient
 */
final class PassportClientTest extends TestCase
{
  private $client;

  private $application;

  public function setUp()
  {
    $this->client = new PassportClient('bf69486b-4733-4470-a592-f1bfce7af580', 'http://localhost:9011');

    $retrieveApplicationResponse = $this->client->retrieveApplication(null);
    $this->handleResponse($retrieveApplicationResponse);

    foreach ($retrieveApplicationResponse->successResponse->applications as $application) {
      if ($application->name == "PHP Client Application") {
        $this->application = $application;
        break;
      }
    }

    // Delete if it exists
    if (isset($this->application)) {
      $deleteResponse = $this->client->deleteApplication($this->application->id);
      $this->handleResponse($deleteResponse);
    }

    $applicationRequest = json_encode(["application" => ["name" => "PHP Client Application"]]);
    $createApplicationResponse = $this->client->createApplication(null, $applicationRequest);
    $this->handleResponse($createApplicationResponse);
    $this->application = $createApplicationResponse->successResponse->application;
  }

  public function tearDown()
  {
    if (isset($this->application)) {
      $deleteResponse = $this->client->deleteApplication($this->application->id);
      $this->handleResponse($deleteResponse);
    }
  }

  private function handleResponse($response)
  {
    if ($response->status == 400) {
      print json_encode($response->errorResponse, JSON_PRETTY_PRINT);
    }

    $this->assertTrue($response->wasSuccessful());
  }

  public function test_retrieveApplications()
  {
    $response = $this->client->retrieveApplication($this->application->id);
    $this->assertTrue($response->wasSuccessful());
  }
}