<?php

/**
 * @uathor Derek Klatt
 */
class ClientResponse
{
  public $errorResponse;

  public $exception;

  public $successResponse;

  public $status;

  public function wasSuccessful()
  {
    return $this->status >= 200 && $this->status <= 299 && $this->exception == null;
  }

  public function __construct()
  {
  }

}
