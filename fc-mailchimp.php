<?php
require 'class.rc4crypt.php';
require 'class.xmlparser.php';
include 'MailChimpUtils.php';   // Bring out the Chimp.

$MailChimp_Auth = array(
    'user'  => 'username',     // Replace "username" with your MailChimp username
    'pass'  => 'password'      // Replace "password" with your MailChimp password
);

/**
 * Use a custom field during checkout?  If true, check for the presence of $Custom_Field below.
 * If false, always subscribe the customer.  Use wisely.
 */
$Use_Custom_Field = true;

$Custom_Field = 'Subscribe';    // Name of the custom "Opt In" field during checkout.
$Custom_Field_Value = 'yes';    // The value of the custom field that indicates the customer's agreement.

$Email_Format = 'html';         // The customer's preferred email format.
$Send_Confirmation = true;      // If true, MailChimp will send a confirmation email to the customer.

$key = 'CHANGE THIS TEXT to your own datafeed keyphrase';

$_POST['FoxyData'] or die("error"); // Make sure we got passed some FoxyData

try {
    $FoxyData = rc4crypt::decrypt($key, urldecode($_POST["FoxyData"]));

    $data = new XMLParser($FoxyData);   // Parse that XML.
    $data->Parse();
    
    foreach ($data->document->transactions[0]->transaction as $tx) {
        $subscribe = !$Use_Custom_Field;

        if ($Use_Custom_Field) {
            foreach ($tx->custom_fields[0]->custom_field as $field) {
                $subscribe = $subscribe ||
                 ($field->custom_field_name[0]->tagData == $Custom_Field &&
                  $field->custom_field_value[0]->tagData == $Custom_Field_Value);
            }
        }

        if ($subscribe) {
            subscribe_user_to_list(// See MailChimpUtils.php for documentation.
             array( 
              'first_name' => $tx->customer_first_name[0]->tagData,
              'last_name' => $tx->customer_last_name[0]->tagData,
              'email' => $tx->customer_email[0]->tagData,
              'format' => $Email_Format,
              'confirm' => $Send_Confirmation),
             'My First List',
             $MailChimp_Auth);
        }
    }
    print "foxy";
}
catch (Exception $e) {
    print "error\n\n";
    print $e;   // Nothing else that we can do.  Hopefuly someone will notice.
}
?>
