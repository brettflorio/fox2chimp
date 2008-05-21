<?php
require 'MCAPI.class.php';

/**
 * Given a user, the name of a MailChimp list, and the MailChimp API credentials, subscribes user
 *  to named list.
 *
 * For the MailChimp API documentation pertinent to subscribing a user, see here:
 *  <a href="http://www.mailchimp.com/api/1.0/listsubscribe.func.php">MailChimp API</a>
 * 
 * @param array $user           Contains the information about the user to subscribe.  Keys:
 *                                 'first_name'         => string; the user's first name
 *                                 'last_name'          => string; the user's last name
 *                                 'email'              => string; the user's email address
 *                                 'format'  (optional) => string; list format preference, either 'html' or 'text'
 *                                                         If not present, defaults to 'html'.
 *                                 'confirm' (optional) => boolean; when true, send a subscription confirmation.
 *                                                         If not present, defaults to true.
 *
 * @param string $list_name     The name of the list to subscribe to.
 *
 * @param array $credentials    Contains the credentials for the MailChimp account that owns the list.  Keys:
 *                                  'user'              => string; MailChimp username
 *                                  'pass'              => string; MailChimp password
 *
 * @throws  Exception           On any error while communicating with MailChimp.
 *
 * @return  boolean             Returns true if member subscribed to the list.
 */
function subscribe_user_to_list($user, $list_name, $credentials) {
    $mc = new MCAPI($credentials['user'], $credentials['pass']);

    if ($mc->errorMessage) {
        throw new Exception("Unable to connect to MailChimp API, error: ".
         $mc->errorMessage);
    }

    $lists = $mc->lists();  // Retrieve all lists from MailChimp.
    $list_id = null;

    foreach ($lists AS $list) { // Iterate, finding the list named $list_name
        if ($list['name'] == $list_name) {
            $list_id = $list['id'];
        }
    }
    
    if (!$list_id)  // Did we find the list?
        throw new Exception("Couldn't find a list named '$list_name'!");

    $retval = $mc->listSubscribe($list_id,
     $user['email'],
     array('FNAME' => $user['first_name'], 'LNAME' => $user['last_name']),
     isset($user['format']) ? $user['format'] : 'html',
     isset($user['confirm']) ? $user['confirm'] : true
     );

    if (!$retval) { // Did we subscribe the user?
        if (!preg_match("/already subscribed/i", $mc->errorMessage)) {
            throw new Exception("Unable to load listSubscribe()! ".
             "MailChimp reported error:\n".
             "\tCode=".$mc->errorCode."\n".
             "\tMsg=".$mc->errorMessage."\n");
        }
    }

    return true;    // All's well.
}
?>