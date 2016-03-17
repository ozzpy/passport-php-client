<?php

/*
 * Copyright (c) 2016, Inversoft Inc., All Rights Reserved
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
 *
 * @author Derek Klatt
 */

Class PassportClient
{
  private $apiKey;
  private $baseURL;

  public $connectTimeout = 2000;
  public $readTimeout = 2000;

  public function __construct($apiKey, $baseURL)
  {
    include_once 'RestClient.php';
    $this->apiKey = $apiKey;
    $this->baseURL = $baseURL;
  }

  public function start()
  {
    $rest = new RestClient();
    return $rest->authorization($this->apiKey)->url($this->baseURL)->connectTimeout($this->connectTimeout)->readTimeout($this->readTimeout);
  }

  /**
   * Takes an action on a user. The user being actioned is called the "actionee" and the user taking the action is
   * called the "actioner". Both user ids are required. You pass the actionee's user id into the method and the
   * actioner's is put into the request object.
   *
   * @param string $actioneeUserId The actionee's user id.
   * @param string $actionRequest The action request that includes all of the information about the action being taken
   * including the id of the action, any options and the duration (if applicable).
   * @return ClientResponse When successful, the response will contain the a notification of the action. If there was a validation
   * error or any other type of error, this will return the Errors object in the response. Additionally, if Passport
   * could not be contacted because it is down or experiencing a failure, the response will contain an Exception, which
   * could be an IOException.
   */
  public function actionUser($actioneeUserId, $actionRequest)
  {
    return $this->start()->uri("/api/user/action")
        ->urlSegment($actioneeUserId)
        ->request($actionRequest)
        ->post()
        ->go();
  }

  /**
   * Cancels the user action.
   *
   * @param string $actionId The action id of the action to cancel.
   * @param string $actionRequest The action request that contains the information about the cancellation.
   * @return ClientResponse When successful, the response will contain the a notification of the action. If there was a validation
   * error or any other type of error, this will return the Errors object in the response. Additionally, if Passport
   * could not be contacted because it is down or experiencing a failure, the response will contain an Exception, which
   * could be an IOException.
   */
  public function cancelAction($actionId, $actionRequest)
  {
    return $this->start()->uri("/api/user/action")
        ->urlSegment($actionId)
        ->request($actionRequest)
        ->delete()
        ->go();
  }

  /**
   * Changes a user's password using the verification id. This usually occurs after an email has been sent to the user
   * and they clicked on a link to reset their password.
   *
   * @param string $verificationId The verification id used to find the user.
   * @param string $changePasswordRequest The change password request that contains all of the information used to change the password.
   * @return ClientResponse When successful, the response will contains no body, just a status code. If there was a validation error or
   * any other type of error, this will return the Errors object in the response. Additionally, if Passport could not be
   * contacted because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function changePassword($verificationId, $changePasswordRequest)
  {
    return $this->start()->uri("/api/user/change-password")
        ->urlSegment($verificationId)
        ->request($changePasswordRequest)
        ->post()
        ->go();
  }

  /**
   * Adds a comment to the user's account.
   *
   * @param string $userCommentRequest The comment request that contains all of the information used to add the comment to the user.
   * @return string ClientResponse When successful, the response will not contain a response object, it only contains the status code. If
   * there was a validation error or any other type of error, this will return the Errors object in the response.
   * Additionally, if Passport could not be contacted because it is down or experiencing a failure, the response will
   * contain an Exception, which could be an IOException.
   */
  public function commentOnUser($userCommentRequest)
  {
    return $this->start()->uri("/api/user/comment")
        ->request($userCommentRequest)
        ->post()
        ->go();
  }

  /**
   * Creates an application. You can optionally specify an id for the application, but this is not required.
   *
   * @param string $applicationId (Optional) The id to use for the application.
   * @param string $applicationRequest The application request that contains all of the information used to create the application.
   * @return ClientResponse When successful, the response will contains the application object. If there was a validation error or any
   * other type of error, this will return the Errors object in the response. Additionally, if Passport could not be
   * contacted because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function createApplication($applicationId, $applicationRequest)
  {
    return $this->start()->uri("/api/user/action")
        ->urlSegment($applicationId)
        ->request($applicationRequest)
        ->post()
        ->go();
  }

  /**
   * Creates a new role for an application. You must specify the id of the application you are creating the role for.
   * You can optionally specify an id for the role inside the ApplicationRole object itself, but this is not required.
   *
   * @param string $applicationId The id of the application to create the role on.
   * @param string $applicationRequest The application request that contains all of the information used to create the role.
   * @return ClientResponse When successful, the response will contains the role object. If there was a validation error or any other
   * type of error, his will return the Errors object in the response. Additionally, if Passport could not be contacted
   * because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function createApplicationRole($applicationId, $applicationRequest)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->urlSegment("role")
        ->request($applicationRequest)
        ->post()
        ->go();
  }

  /**
   * Creates an audit log with the message and user name (usually an email). Audit logs should be written anytime you
   * make changes to the Passport database. When using the Passport Backend web interface, any changes are automatically
   * written to the audit log. However, if you are accessing the API, you must write the audit logs yourself.
   *
   * @param string $message The message for the audit log.
   * @param string $insertUser The user that took the action being logged.
   * @return ClientResponse When successful, the response will not contain a response but only contains the status code. If there was a
   * validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function createAuditLog($message, $insertUser)
  {
    $auditLog = array();
    $auditLog['insertInstant'] = '';
    $auditLog['insertUser'] = $insertUser;
    $auditLog['message'] = $message;
    $auditLogRequest = array();
    $auditLogRequest['auditLog'] = $auditLog;
    return $this->start()->uri("/api/system/audit-log")
        ->request($auditLogRequest)
        ->post()
        ->go();
  }

  /**
   * Creates an email template. You can optionally specify an id for the email template when calling this method, but it
   * is not required.
   *
   * @param string $emailTemplateId (Optional) The id for the template.
   * @param string $emailTemplateRequest The email template request that contains all of the information used to create the email template.
   * @return ClientResponse When successful, the response will contain the email template object. If there was a validation error or
   * any other type of error, this will return the Errors object in the response. Additionally, if Passport could not be
   * contacted because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function createEmailTemplate($emailTemplateId, $emailTemplateRequest)
  {
    return $this->start()->uri("/api/email/template")
        ->urlSegment($emailTemplateId)
        ->request($emailTemplateRequest)
        ->post()
        ->go();
  }

  /**
   * Creates a notification server. You can optionally specify an id for the notification server when calling this
   * method, but it is not required.
   *
   * @param string $notificationServerId (Optional) The id for the notification server.
   * @param string $notificationServerRequest The notification server request that contains all of the information used to create the
   * notification server.
   * @return ClientResponse When successful, the response will contain the notification server object. If there was a validation error
   * or any other type of error, this will return the Errors object in the response. Additionally, if Passport could not
   * be contacted because it is down or experiencing a failure, the response will contain an Exception, which could be
   * an IOException.
   */
  public function createNotificationServer($notificationServerId, $notificationServerRequest)
  {
    return $this->start()->uri("/api/notification-server")
        ->urlSegment($notificationServerId)
        ->request($notificationServerRequest)
        ->post()
        ->go();
  }

  /**
   * Creates a user with an optional id.
   *
   * @param string $userId (Optional) The id for the user.
   * @param string $userRequest The user request that contains all of the information used to create the user.
   * @return ClientResponse When successful, the response will contain the user object. If there was a validation error or any other
   * type of error, this will return the Errors object in the response. Additionally, if Passport could not be contacted
   * because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function createUser($userId, $userRequest)
  {
    return $this->start()->uri("/api/user")
        ->urlSegment($userId)
        ->request($userRequest)
        ->post()
        ->go();
  }

  /**
   * Creates a user action. This action cannot be taken on a user until this call successfully returns. Anytime after
   * that the user action can be applied to any user.
   *
   * @param string $userActionId (Optional) The id for the user action.
   * @param string $userActionRequest The user action request that contains all of the information used to create the user action.
   * @return ClientResponse When successful, the response will contain the user action object. If there was a validation error or any
   * other type of error, this will return the Errors object in the response. Additionally, if Passport could not be
   * contacted because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function createUserAction($userActionId, $userActionRequest)
  {
    return $this->start()->uri("/api/user-action")
        ->urlSegment($userActionId)
        ->request($userActionRequest)
        ->post()
        ->go();
  }

  /**
   * Creates a user reason. This user action reason cannot be used when actioning a user until this call completes
   * successfully. Anytime after that the user action reason can be used.
   * @param string $userActionReasonId (optional)
   * @param string $userActionReasonRequest The user action reason request that contains all of the information used to create the user action
   *                reason.
   * @return ClientResponse When successful, the response will contain the user action reason object. If there was a validation error
   * or any other type of error, this will return the Errors object in the response. Additionally, if Passport could not
   * be contacted because it is down or experiencing a failure, the response will contain an Exception, which could be
   * an IOException.
   */
  public function createUserActionReason($userActionReasonId, $userActionReasonRequest)
  {
    return $this->start()->uri("/api/user-action-reason")
        ->urlSegment($userActionReasonId)
        ->request($userActionReasonRequest)
        ->post()
        ->go();
  }

  /**
   * Deactivates the application with the given id.
   *
   * @param string $applicationId The id of the application to deactivate.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status. If there was
   * a validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function deactivateApplication($applicationId)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->delete()
        ->go();
  }

  /**
   * Deactivates the user with the given id.
   *
   * @param string $userId The id of the application to deactivate.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status. If there was
   * a validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function deactivateUser($userId)
  {
    return $this->start()->uri("/api/user")
        ->urlSegment($userId)
        ->delete()
        ->go();
  }

  /**
   * Deactivates the user action with the given id.
   *
   * @param string $userActionId The id of the user action to deactivate.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status. If there was
   * a validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function deactivateUserAction($userActionId)
  {
    return $this->start()->uri("/api/user-action")
        ->urlSegment($userActionId)
        ->delete()
        ->go();
  }

  /**
   * Hard deletes an application. This is a dangerous operation and should not be used in most circumstances. This will
   * delete the application, any registrations for that application, metrics and reports for the application, all the
   * roles for the application, and any other data associated with the application. This operation could take a very
   * long time, depending on the amount of data in your database.
   *
   * @param string $applicationId The id of the application to delete.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status. If there was
   * a validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function deleteApplication($applicationId)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->urlParameter("hardDelete", true)
        ->delete()
        ->go();
  }

  /**
   * Hard deletes an application role. This is a dangerous operation and should not be used in most circumstances. This
   * permanently removes the given role from all users that had it.
   *
   * @param string $applicationId The id of the application that contains the role.
   * @param string $roleId The id of the role to delete.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status. If there was
   * a validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function deleteApplicationRole($applicationId, $roleId)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->urlSegment("role")
        ->urlSegment($roleId)
        ->delete()
        ->go();
  }

  /**
   * Deletes the email template for the given id.
   *
   * @param string $emailTemplateId The id of the email template to delete.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status. If there was
   * a validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function deleteEmailTemplate($emailTemplateId)
  {
    return $this->start()->uri("/api/email/template")
        ->urlSegment($emailTemplateId)
        ->delete()
        ->go();
  }

  /**
   * Deletes the notification server for the given id.
   *
   * @param string $notificationServerId The id of the notification server to delete.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status. If there was
   * a validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function deleteNotificationServer($notificationServerId)
  {
    return $this->start()->uri("/api/notification-server")
        ->urlSegment($notificationServerId)
        ->delete()
        ->go();
  }

  /**
   * Deletes the user registration for the given user and application.
   *
   * @param string $userId The id of the user whose registration is being deleted.
   * @param string $applicationId The id of the application to remove the registration for.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status. If there was
   * a validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function deleteRegistration($userId, $applicationId)
  {
    return $this->start()->uri("/api/user/registration")
        ->urlSegment($userId)
        ->urlSegment($applicationId)
        ->delete()
        ->go();
  }

  /**
   * Deletes the user for the given id. This permanently deletes all information, metrics, reports and data associated
   * with the user.
   *
   * @param string $userId The id of the user to delete.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status. If there was
   * a validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function deleteUser($userId)
  {
    return $this->start()->uri("/api/user")
        ->urlSegment($userId)
        ->urlParameter("hardDelete", true)
        ->delete()
        ->go();
  }

  /**
   * Deletes the user action for the given id. This permanently deletes the user action and also any history and logs of
   * the action being applied to any users.
   *
   * @param string $userActionId The id of the user action to delete.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status. If there was
   * a validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function deleteUserAction($userActionId)
  {
    return $this->start()->uri("/api/user-action")
        ->urlSegment($userActionId)
        ->urlParameter("hardDelete", true)
        ->delete()
        ->go();
  }

  /**
   * Deletes the user action reason for the given id.
   *
   * @param string $userActionReasonId The id of the user action reason to delete.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status. If there was
   * a validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function deleteUserActionReason($userActionReasonId)
  {
    return $this->start()->uri("/api/user-action-reason")
        ->urlSegment($userActionReasonId)
        ->delete()
        ->go();
  }

  /**
   * Begins the forgot password sequence, which kicks off an email to the user so that they can reset their password.
   *
   * @param string $forgotPasswordRequest The request that contains the information about the user so that they can be emailed.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status. If there was
   * a validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function forgotPassword($forgotPasswordRequest)
  {
    return $this->start()->uri("/api/user/forgot-password")
        ->request($forgotPasswordRequest)
        ->post()
        ->go();
  }

  /**
   * Bulk imports multiple users. This does some validation, but then tries to run batch inserts of users. This reduces
   * latency when inserting lots of users. Therefore, the error response might contain some information about failures,
   * but it will likely be pretty generic.
   *
   * @param string $importRequest The request that contains all of the information about all of the users to import.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status. If there was
   * a validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function importUsers($importRequest)
  {
    return $this->start()->uri("/api/user/import")
        ->request($importRequest)
        ->post()
        ->go();
  }

  /**
   * Logs a user in.
   *
   * @param string $loginRequest    The login request that contains the user credentials used to log them in.
   * @param string $callerIPAddress The IP address of the end-user that is logging in.
   * @return ClientResponse When successful, the response will contain the user that was logged in. This user object is complete and
   * contains all of the registrations and data for the user. If there was a validation error or any other type of
   * error, this will return the Errors object in the response. Additionally, if Passport could not be contacted because
   * it is down or experiencing a failure, the response will contain an Exception, which could be an IOException.
   */
  public function login($loginRequest, $callerIPAddress)
  {
    return $this->start()->uri("/api/login")
        ->header("X-Forwarded-For", $callerIPAddress)
        ->request($loginRequest)
        ->post()
        ->go();
  }

  /**
   * Sends a ping to Passport indicating that the user was automatically logged into an application. When using
   * Passport's SSO or your own, you should call this if the user is already logged in centrally, but accesses an
   * application where they no longer have a session. This helps correctly track login counts, times and helps with
   * reporting.
   *
   * @param string $userId The id of the user that was logged in.
   * @param string $applicationId The id of the application that they logged into.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status. If there was
   * a validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function loginPing($userId, $applicationId)
  {
    return $this->start()->uri("/api/login/" . $userId . "/" . $applicationId)
        ->put()
        ->go();
  }

  /**
   * Modifies a temporal user action by changing the expiration of the action and optionally adding a comment to the
   * action.
   *
   * @param string $actionId The id of the action to modify. This is technically the user action log id.
   * @param string $actionRequest The request that contains all of the information about the modification.
   * @return ClientResponse When successful, the response will contain the a notification of the action. If there was a validation
   * error or any other type of error, this will return the Errors object in the response. Additionally, if Passport
   * could not be contacted because it is down or experiencing a failure, the response will contain an Exception, which
   * could be an IOException.
   */
  public function modifyAction($actionId, $actionRequest)
  {
    return $this->start()->uri("/api/user/action")
        ->urlSegment($actionId)
        ->request($actionRequest)
        ->put()
        ->go();
  }

  /**
   * Reactivates the application for the given id.
   *
   * @param string $applicationId The id of the application to reactivate.
   * @return ClientResponse When successful, the response will contain the application that was reactivated. If there was a validation
   * error or any other type of error, this will return the Errors object in the response. Additionally, if Passport
   * could not be contacted because it is down or experiencing a failure, the response will contain an Exception, which
   * could be an IOException.
   */
  public function reactivateApplication($applicationId)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->urlParameter("reactivate", true)
        ->put()
        ->go();
  }

  /**
   * Reactivates the user action for the given id.
   *
   * @param string $userId The id of the user action to reactivate.
   * @return ClientResponse When successful, the response will contain the user that was reactivated. If there was a validation error
   * or any other type of error, this will return the Errors object in the response. Additionally, if Passport could not
   * be contacted because it is down or experiencing a failure, the response will contain an Exception, which could be
   * an IOException.
   */
  public function reactivateUser($userId)
  {
    return $this->start()->uri("/api/user")
        ->urlSegment($userId)
        ->urlParameter("reactivate", true)
        ->put()
        ->go();
  }

  /**
   * Reactivates the user action for the given id.
   *
   * @param string $userActionId The id of the user action to reactivate.
   * @return ClientResponse When successful, the response will contain the user action that was reactivated. If there was a validation
   * error or any other type of error, this will return the Errors object in the response. Additionally, if Passport
   * could not be contacted because it is down or experiencing a failure, the response will contain an Exception, which
   * could be an IOException.
   */
  public function reactivateUserAction($userActionId)
  {
    return $this->start()->uri("/api/user-action")
        ->urlSegment($userActionId)
        ->urlParameter("reactivate", true)
        ->put()
        ->go();
  }

  /**
   * Registers a user for an application. If you provide the User and the UserRegistration object on this request, it
   * will create the user as well as register them for the application. This is called a Full Registration. However, if
   * you only provide the UserRegistration object, then the user must already exist and they will be registered for the
   * application. The user id can also be provided and it will either be used to look up an existing user or it will be
   * used for the newly created User.
   *
   * @param string $userId (optional) The id of the user being registered for the application and optionally created.
   * @param string $registrationRequest The request that optionally contains the User and must contain the UserRegistration.
   * @return ClientResponse When successful, the response will contain the UserRegistration and optionally will contain the User if the
   * request as a Full Registration. If there was a validation error or any other type of error, this will return the
   * Errors object in the response. Additionally, if Passport could not be contacted because it is down or experiencing
   * a failure, the response will contain an Exception, which could be an IOException.
   */
  public function register($userId, $registrationRequest)
  {
    return $this->start()->uri("/api/user/registration")
        ->urlSegment($userId)
        ->request($registrationRequest)
        ->post()
        ->go();
  }

  /**
   * Re-sends the verification email to the user.
   *
   * @param string $email The email address of the user that needs a new verification email.
   * @return ClientResponse When successful, the response will not contain a response object, it will only contain the status. There
   * are also no errors associated with this request. Additionally, if Passport could not be contacted because it is
   * down or experiencing a failure, the response will contain an Exception, which could be an IOException.
   */
  public function resendEmailVerification($email)
  {
    return $this->start()->uri("/api/user/verify-email")
        ->urlParameter("email", $email)
        ->put()
        ->go();
  }

  /**
   * Retrieves a single action for the given id.
   *
   * @param string $actionId The id of the action to retrieve.
   * @return ClientResponse When successful, the response will contain the the action that was previously taken on a user. If there was
   * a validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function retrieveAction($actionId)
  {
    return $this->start()->uri("/api/user/action")
        ->urlSegment($actionId)
        ->get()
        ->go();
  }

  /**
   * Retrieves all of the actions for the user with the given id.
   *
   * @param string $userId The id of the user to fetch the actions for.
   * @return ClientResponse When successful, the response will contain all of the user action notifications for the given use. If there
   * was a validation error or any other type of error, this will return the Errors object in the response.
   * Additionally, if Passport could not be contacted because it is down or experiencing a failure, the response will
   * contain an Exception, which could be an IOException.
   */
  public function retrieveActions($userId)
  {
    return $this->start()->uri("/api/user/action?$userId=" . $userId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the application for the given id or all of the applications if the id is null.
   *
   * @param string $applicationId (Optional) The application id.
   * @return ClientResponse When successful, the response will contain the application or applications. There are no errors associated
   * with this request. Additionally, if Passport could not be contacted because it is down or experiencing a failure,
   * the response will contain an Exception, which could be an IOException.
   */
  public function retrieveApplication($applicationId)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->get()
        ->go();
  }


  /**
   * Retrieves the daily active user report between the two instants. If you specify an application id, it will only
   * return the daily active counts for that application.
   *
   * @param string $applicationId (Optional) The application id.
   * @param integer $start The start instant as UTC milliseconds since Epoch.
   * @param integer $end The end instant as UTC milliseconds since Epoch.
   * @return ClientResponse When successful, the response will contain the daily active user counts between the start().
   * If there was a validation error or any other type of error, this will return the Errors object in the response.
   * Additionally, if Passport could not be contacted because it is down or experiencing a failure, the response will
   * contain an Exception, which could be an IOException.
   */
  public function retrieveDailyActiveReport($applicationId, $start, $end)
  {
    return $this->start()->uri("/api/report/daily-active-user")
        ->urlParameter("start", $start)
        ->urlParameter("end", $end)
        ->urlParameter("$applicationId", $applicationId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the email template for the given id. If you don't specify the id, this will return all of the email
   * templates.
   *
   * @param string $emailTemplateId The id of the email template.
   * @return ClientResponse When successful, the response will contain the email template of the id or all the email templates. There
   * are no errors associated with this request. Additionally, if Passport could not be contacted because it is down or
   * experiencing a failure, the response will contain an Exception, which could be an IOException.
   */
  public function retrieveEmailTemplate($emailTemplateId)
  {
    return $this->start()->uri("/api/email/template")
        ->urlSegment($emailTemplateId)
        ->get()
        ->go();
  }

  /**
   * Creates a preview of the email template provided in the request. This allows you to preview an email template that
   * hasn't been saved to the database yet. The entire email template does not need to be provided on the request. This
   * will create the preview based on whatever is given.
   *
   * @param string $previewRequest The request that contains the email template and optionally a locale to render it in.
   * @return ClientResponse When successful, the response will contain the preview of the email template. If the template was invalid
   * or could not be rendered because of template errors, those will be returned in the Errors object in the response.
   * Additionally, if Passport could not be contacted because it is down or experiencing a failure, the response will
   * contain an Exception, which could be an IOException.
   */
  public function retrieveEmailTemplatePreview($previewRequest)
  {
    return $this->start()->uri("/api/email/template/preview")
        ->request($previewRequest)
        ->post()
        ->go();
  }

  /**
   * Retrieves all of the applications that are currently inactive.
   *
   * @return ClientResponse When successful, the response will contain all of the inactive applications. There are no errors associated
   * with this request. Additionally, if Passport could not be contacted because it is down or experiencing a failure,
   * the response will contain an Exception, which could be an IOException.
   */
  public function retrieveInactiveApplications()
  {
    return $this->start()->uri("/api/application")
        ->urlParameter("inactive", true)
        ->get()
        ->go();
  }

  /**
   * Retrieves all of the user actions that are currently inactive.
   *
   * @return ClientResponse When successful, the response will contain all of the inactive user actions. There are no errors associated
   * with this request. Additionally, if Passport could not be contacted because it is down or experiencing a failure,
   * the response will contain an Exception, which could be an IOException.
   */
  public function retrieveInactiveUserActions()
  {
    return $this->start()->uri("/api/user-action")
        ->urlParameter("inactive", true)
        ->get()
        ->go();
  }

  /**
   * Retrieves the login report between the two instants. If you specify an application id, it will only return the
   * login counts for that application.
   *
   * @param string $applicationId (Optional) The application id.
   * @param integer $start The start instant as UTC milliseconds since Epoch.
   * @param integer $end The end instant as UTC milliseconds since Epoch.
   * @return ClientResponse When successful, the response will contain the login counts between the start(). If there
   * was a validation error or any other type of error, this will return the Errors object in the response.
   * Additionally, if Passport could not be contacted because it is down or experiencing a failure, the response will
   * contain an Exception, which could be an IOException.
   */
  public function retrieveLoginReport($applicationId, $start, $end)
  {
    return $this->start()->uri("/api/report/login")
        ->urlParameter("start", $start)
        ->urlParameter("end", $end)
        ->urlParameter("$applicationId", $applicationId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the monthly active user report between the two instants. If you specify an application id, it will only
   * return the monthly active counts for that application.
   *
   * @param string $applicationId (Optional) The application id.
   * @param integer $start The start instant as UTC milliseconds since Epoch.
   * @param integer $end The end instant as UTC milliseconds since Epoch.
   * @return ClientResponse When successful, the response will contain the monthly active user counts between the start and end
   * instants. If there was a validation error or any other type of error, this will return the Errors object in the
   * response. Additionally, if Passport could not be contacted because it is down or experiencing a failure, the
   * response will contain an Exception, which could be an IOException.
   */
  public function retrieveMonthlyActiveReport($applicationId, $start, $end)
  {
    return $this->start()->uri("/api/report/monthly-active-user")
        ->urlParameter("start", $start)
        ->urlParameter("end", $end)
        ->urlParameter("$applicationId", $applicationId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the notification server for the given id. If you pass in null for the id, this will return all the
   * notification servers.
   *
   * @param string $notificationServerId (Optional) The id of the notification server.
   * @return ClientResponse When successful, the response will contain the notification server for the id or all the notification
   * servers. There are no errors associated with this request. Additionally, if Passport could not be contacted because
   * it is down or experiencing a failure, the response will contain an Exception, which could be an IOException.
   */
  public function retrieveNotificationServer($notificationServerId)
  {
    return $this->start()->uri("/api/notification-server")
        ->urlSegment($notificationServerId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the user registration for the user with the given id and the given application id.
   *
   * @param string $userId The id of the user.
   * @param string $applicationId The id of the application.
   * @return ClientResponse When successful, the response will contain the user registration object. If there was a validation error or
   * any other type of error, this will return the Errors object in the response. Additionally, if Passport could not be
   * contacted because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function retrieveRegistration($userId, $applicationId)
  {
    return $this->start()->uri("/api/user/registration")
        ->urlSegment($userId)
        ->urlSegment($applicationId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the registration report between the two instants. If you specify an application id, it will only return
   * the login counts for that application.
   *
   * @param string $applicationId (Optional) The application id.
   * @param integer $start The start instant as UTC milliseconds since Epoch.
   * @param integer $end The end instant as UTC milliseconds since Epoch.
   * @return ClientResponse When successful, the response will contain the registration counts between the start(). If
   * there was a validation error or any other type of error, this will return the Errors object in the response.
   * Additionally, if Passport could not be contacted because it is down or experiencing a failure, the response will
   * contain an Exception, which could be an IOException.
   */
  public function retrieveRegistrationReport($applicationId, $start, $end)
  {
    return $this->start()->uri("/api/report/registration")
        ->urlParameter("start", $start)
        ->urlParameter("end", $end)
        ->urlParameter("$applicationId", $applicationId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the system configuration.
   *
   * @return ClientResponse When successful, the response will contain the system configuration. There are no errors associated with
   * this request. Additionally, if Passport could not be contacted because it is down or experiencing a failure, the
   * response will contain an Exception, which could be an IOException.
   */
  public function retrieveSystemConfiguration()
  {
    return $this->start()->uri("/api/system-configuration")
        ->get()
        ->go();
  }

  /**
   * Retrieves the totals report. This contains all of the total counts for each application and the global registration
   * count.
   *
   * @return ClientResponse When successful, the response will contain the total counts for logins and registrations for each
   * application as well as the global registration count. If there was a validation error or any other type of error,
   * this will return the Errors object in the response. Additionally, if Passport could not be contacted because it is
   * down or experiencing a failure, the response will contain an Exception, which could be an IOException.
   */
  public function retrieveTotalReport()
  {
    return $this->start()->uri("/api/report/totals")
        ->get()
        ->go();
  }

  /**
   * Retrieves the user for the given id.
   *
   * @param string $userId The id of the user.
   * @return ClientResponse When successful, the response will contain the user object. If there was a validation error or any other
   * type of error, this will return the Errors object in the response. Additionally, if Passport could not be contacted
   * because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function retrieveUser($userId)
  {
    return $this->start()->uri("/api/user")
        ->urlSegment($userId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the user action for the given id. If you pass in null for the id, this will return all of the user
   * actions.
   *
   * @param string $userActionId (Optional) The id of the user action.
   * @return ClientResponse When successful, the response will contain the user action or all the user actions if null is passed in.
   * There are no errors associated with this request. Additionally, if Passport could not be contacted because it is
   * down or experiencing a failure, the response will contain an Exception, which could be an IOException.
   */
  public function retrieveUserAction($userActionId)
  {
    return $this->start()->uri("/api/user-action")
        ->urlSegment($userActionId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the user action reason for the given id. If you pass in null for the id, this will return all of the user
   * action reasons.
   *
   * @param string $userActionReasonId (Optional) The id of the user action.
   * @return ClientResponse When successful, the response will contain the user action reason or all the user action reasons if null is
   * passed in. There are no errors associated with this request. Additionally, if Passport could not be contacted
   * because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function retrieveUserActionReason($userActionReasonId)
  {
    return $this->start()->uri("/api/user-action-reason")
        ->urlSegment($userActionReasonId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the user for the given email.
   *
   * @param string $email The email of the user.
   * @return ClientResponse When successful, the response will contain the user object. If there was a validation error or any other
   * type of error, this will return the Errors object in the response. Additionally, if Passport could not be contacted
   * because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function retrieveUserByEmail($email)
  {
    return $this->start()->uri("/api/user")
        ->urlParameter("email", $email)
        ->get()
        ->go();
  }

  /**
   * Retrieves the user for the given username.
   *
   * @param string $username The username of the user.
   * @return ClientResponse When successful, the response will contain the user object. If there was a validation error or any other
   * type of error, this will return the Errors object in the response. Additionally, if Passport could not be contacted
   * because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function retrieveUserByUsername($username)
  {
    return $this->start()->uri("/api/user")
        ->urlParameter("username", $username)
        ->get()
        ->go();
  }

  /**
   * Retrieves all of the comments for the user with the given id.
   *
   * @param string $userId The id of the user.
   * @return ClientResponse When successful, the response will contain the comments. If there was a validation error or any other type
   * of error, this will return the Errors object in the response. Additionally, if Passport could not be contacted
   * because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function retrieveUserComments($userId)
  {
    return $this->start()->uri("/api/user/comment")
        ->urlSegment($userId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the login report between the two instants. If you specify an application id, it will only return the
   * login counts for that application.
   *
   * @param string $userId The user's id.
   * @param integer $offset The initial record. e.g. 0 is the last login, 100 will be the 100th most recent login.
   * @param integer $limit (Optional, defaults to 10) The number of records to retrieve.
   * @return ClientResponse When successful, the response will contain RawLogin records. If there was a validation error or any other
   * type of error, this will return the Errors object in the response. Additionally, if Passport could not be contacted
   * because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function retrieveUserLoginReport($userId, $offset, $limit)
  {
    return $this->start()->uri("/api/report/user-login")
        ->urlParameter("$userId", $userId)
        ->urlParameter("offset", $offset)
        ->urlParameter("limit", $limit != null ? $limit : 10)
        ->get()
        ->go();
  }

  /**
   * Searches the audit logs with the specified criteria and pagination.
   *
   * @param string $search The search criteria and pagination information.
   * @return ClientResponse When successful, the response will contain the audit logs that match the criteria and pagination
   * constraints. There are no errors associated with this request. Additionally, if Passport could not be contacted
   * because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function searchAuditLogs($search)
  {
    return $this->start()->uri("/api/system/audit-log")
        ->urlParameter("search.user", $search["user"])
        ->urlParameter("search.message", $search["message"])
        ->urlParameter("search.end", $search["end"])
        ->urlParameter("search.start", $search["start"])
        ->urlParameter("search.orderBy", $search["orderBy"])
        ->urlParameter("search.startRow", $search["startRow"])
        ->urlParameter("search.numberOfResults", $search["numberOfResults"])
        ->get()
        ->go();
  }

  /**
   * Retrieves the users for the given ids. If any id is invalid, it is ignored.
   *
   * @param string $ids The user ids to search for.
   * @return ClientResponse When successful, the response will contain the users that match the ids. If there was a validation error or
   * any other type of error, this will return the Errors object in the response. Additionally, if Passport could not be
   * contacted because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function searchUsers($ids)
  {
    return $this->start()->uri("/api/user/search")
        ->urlParameter("ids", $ids)
        ->get()
        ->go();
  }

  /**
   * Retrieves the users for the given search criteria and pagination.
   *
   * @param string $search The search criteria and pagination constraints. Fields used: queryString, numberOfResults, and
   *               startRow
   * @return ClientResponse When successful, the response will contain the users that match the search criteria and pagination
   * constraints. If there was a validation error or any other type of error, this will return the Errors object in the
   * response. Additionally, if Passport could not be contacted because it is down or experiencing a failure, the
   * response will contain an Exception, which could be an IOException.
   */
  public function searchUsersByQueryString($search)
  {
    return $this->start()->uri("/api/user/search")
        ->urlParameter("queryString", $search["queryString"])
        ->urlParameter("numberOfResults", $search["numberOfResults"])
        ->urlParameter("startRow", $search["startRow"])
        ->get()
        ->go();
  }

  /**
   * Send an email using an email template id. You can optionally provide <code>requestData</code> to access key value
   * pairs in the email template.
   *
   * @param string $emailTemplateId The id for the template.
   * @param string $sendRequest The send email request that contains all of the information used to send the email.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status. If there was
   * a validation error or any other type of error, this will return the Errors object in the response. Additionally, if
   * Passport could not be contacted because it is down or experiencing a failure, the response will contain an
   * Exception, which could be an IOException.
   */
  public function sendEmail($emailTemplateId, $sendRequest)
  {
    return $this->start()->uri("/api/email/send")
        ->urlSegment($emailTemplateId)
        ->request($sendRequest)
        ->post()
        ->go();
  }

  /**
   * Updates the application with the given id.
   *
   * @param string $applicationId The id of the application to update.
   * @param string $applicationRequest The request that contains all of the new application information.
   * @return ClientResponse When successful, the response will contain the application. If there was a validation error or any other
   * type of error, this will return the Errors object in the response. Additionally, if Passport could not be contacted
   * because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function updateApplication($applicationId, $applicationRequest)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->request($applicationRequest)
        ->put()
        ->go();
  }

  /**
   * Updates the application role with the given id for the application.
   *
   * @param string $applicationId The id of the application that the role belongs to.
   * @param string $roleId The id of the role to update.
   * @param string $applicationRequest The request that contains all of the new role information.
   * @return ClientResponse When successful, the response will contain the role. If there was a validation error or any other type of
   * error, this will return the Errors object in the response. Additionally, if Passport could not be contacted because
   * it is down or experiencing a failure, the response will contain an Exception, which could be an IOException.
   */
  public function updateApplicationRole($applicationId, $roleId, $applicationRequest)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->urlSegment("role")
        ->urlSegment($roleId)
        ->request($applicationRequest)
        ->put()
        ->go();
  }

  /**
   * Updates the email template with the given id.
   *
   * @param string $emailTemplateId The id of the email template to update.
   * @param string $emailTemplateRequest The request that contains all of the new email template information.
   * @return ClientResponse When successful, the response will contain the email template. If there was a validation error or any other
   * type of error, this will return the Errors object in the response. Additionally, if Passport could not be contacted
   * because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function updateEmailTemplate($emailTemplateId, $emailTemplateRequest)
  {
    return $this->start()->uri("/api/email/template")
        ->urlSegment($emailTemplateId)
        ->request($emailTemplateRequest)
        ->put()
        ->go();
  }

  /**
   * Updates the notification server with the given id.
   *
   * @param string $notificationServerId The id of the notification server to update.
   * @param string $notificationServerRequest The request that contains all of the new notification server information.
   * @return ClientResponse When successful, the response will contain the notification server. If there was a validation error or any
   * other type of error, this will return the Errors object in the response. Additionally, if Passport could not be
   * contacted because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function updateNotificationServer($notificationServerId, $notificationServerRequest)
  {
    return $this->start()->uri("/api/notification-server")
        ->urlSegment($notificationServerId)
        ->request($notificationServerRequest)
        ->put()
        ->go();
  }

  /**
   * Updates the registration for the user with the given id and the application defined in the request.
   *
   * @param string $userId The id of the user whose registration is going to be updated.
   * @param string $registrationRequest The request that contains all of the new registration information.
   * @return ClientResponse When successful, the response will contain the registration. If there was a validation error or any other
   * type of error, this will return the Errors object in the response. Additionally, if Passport could not be contacted
   * because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function updateRegistration($userId, $registrationRequest)
  {
    return $this->start()->uri("/api/user/registration")
        ->urlSegment($userId)
        ->request($registrationRequest)
        ->put()
        ->go();
  }

  /**
   * Updates the system configuration.
   *
   * @param string $systemConfigurationRequest The request that contains all of the new system configuration information.
   * @return ClientResponse When successful, the response will contain the system configuration. If there was a validation error or any
   * other type of error, this will return the Errors object in the response. Additionally, if Passport could not be
   * contacted because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function updateSystemConfiguration($systemConfigurationRequest)
  {
    return $this->start()->uri("/api/system-configuration")
        ->request($systemConfigurationRequest)
        ->put()
        ->go();
  }

  /**
   * Updates the user with the given id.
   *
   * @param string $userId The id of the user to update.
   * @param string $request The request that contains all of the new user information.
   * @return ClientResponse When successful, the response will contain the user. If there was a validation error or any other type of
   * error, this will return the Errors object in the response. Additionally, if Passport could not be contacted because
   * it is down or experiencing a failure, the response will contain an Exception, which could be an IOException.
   */
  public function updateUser($userId, $request)
  {
    return $this->start()->uri("/api/user")
        ->urlSegment($userId)
        ->request($request)
        ->put()
        ->go();
  }

  /**
   * Updates the user action with the given id.
   *
   * @param string $userActionId The id of the user action to update.
   * @param string $userRequest The request that contains all of the new user action information.
   * @return ClientResponse When successful, the response will contain the user action. If there was a validation error or any other
   * type of error, this will return the Errors object in the response. Additionally, if Passport could not be contacted
   * because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function updateUserAction($userActionId, $userRequest)
  {
    return $this->start()->uri("/api/user-action/" . $userActionId)
        ->request($userRequest)
        ->put()
        ->go();
  }

  /**
   * Updates the user action reason with the given id.
   *
   * @param string $userActionReasonId The id of the user action reason to update.
   * @param string $userActionRequest The request that contains all of the new user action reason information.
   * @return ClientResponse When successful, the response will contain the user action reason. If there was a validation error or any
   * other type of error, this will return the Errors object in the response. Additionally, if Passport could not be
   * contacted because it is down or experiencing a failure, the response will contain an Exception, which could be an
   * IOException.
   */
  public function updateUserActionReason($userActionReasonId, $userActionRequest)
  {
    return $this->start()->uri("/api/user-action-reason")
        ->urlSegment($userActionReasonId)
        ->request($userActionRequest)
        ->put()
        ->go();
  }

  /**
   * Confirms a email verification. The id given is usually from an email sent to the user.
   *
   * @param string $verificationId The verification id sent to the user.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status. There are no
   * errors associated with this request. Additionally, if Passport could not be contacted because it is down or
   * experiencing a failure, the response will contain an Exception, which could be an IOException.
   */
  public function verifyEmail($verificationId)
  {
    return $this->start()->uri("/api/user/verify-email")
        ->urlSegment($verificationId)
        ->post()
        ->go();
  }

  /**
   * Confirms a two factor authentication code.
   *
   * @param string $twoFactorRequest The two factor request information.
   * @return ClientResponse When successful, the response will not contain a response object but only contains the status.  If there
   * was a validation error or any other type of error, this will return the Errors object in the response.
   * Additionally, if Passport could not be contacted because it is down or experiencing a failure, the response will
   * contain an Exception, which could be an IOException.
   */
  public function verifyTwoFactor($twoFactorRequest)
  {
    return $this->start()->uri("/api/two-factor")
        ->request($twoFactorRequest)
        ->post()
        ->go();
  }

}
