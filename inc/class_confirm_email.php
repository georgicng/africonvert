<?php
/**
 * Plugin Name: Email Confirmation
 * Description: Send an email to the user with confirmation code and store form data in database until user confirms.
 * Author: Cedric Ruiz
 */
class EmailConfirmation
{
  const PREFIX = 'email-confirmation-';

  public static function send($to, $subject, $message, $headers)
  {
    $token = sha1(uniqid());

    $oldData = get_option(self::PREFIX .'data') ? get_option(self::PREFIX .'data') : array();
    $data = array();
    $data[$token] = $_POST;
    update_option(self::PREFIX .'data', array_merge($oldData, $data));

    wp_mail($to, $subject, sprintf($message, $token), $headers);
  }

  public static function check($token)
  {
    $data = get_option(self::PREFIX .'data');
    $userData = $data[$token];

    if (isset($userData)) {
      unset($data[$token]);
      update_option(self::PREFIX .'data', $data);
    }

    return $userData;
  }
}
