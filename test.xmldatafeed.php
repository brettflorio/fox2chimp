<?php
/*
	test.xmldatafeed.php

	The purpose of this file is to help you set up and debug your FoxyCart XML DataFeed scripts.
	It's designed to mimic FoxyCart.com and send encrypted and encoded XML to a URL of your choice.
	It will print out the response that your script gives back, which should be "foxy" if successful.

	NOTE: This script uses cURL, which isn't always enabled, especially on shared hosting.

	Change user@example.com below to an email address you want to test with.

*/

// ======================================================================================
// CHANGE THIS DATA:
// Set the URL you want to post the XML to.
// Set the key you entered in your FoxyCart.com admin.
// Modify the XML below as necessary.  DO NOT modify the structure, just the data
// ======================================================================================
$myURL = 'http://path/to/file.php'; //Set the URL you want to post the XML to.
$myKey = ''; // Set the key you entered in your FoxyCart.com admin.

// This is FoxyCart Version 0.6 XML.  See http://wiki.foxycart.com/docs:datafeed?s[]=xml
$XMLHeader = '<' . '?xml version=\'1.0\' standalone=\'yes\'?' . '>';

$XMLOutput = <<<XML
$XMLHeader
<foxydata>
	<datafeed_version>XML FoxyCart Version 0.6</datafeed_version>
	<transactions>
		<transaction>
			<id>616</id>
			<transaction_date>2007-05-04 20:53:57</transaction_date>
			<customer_id>122</customer_id>
			<customer_first_name>Dirk</customer_first_name>
			<customer_last_name>Gently</customer_last_name>
			<customer_address1>12345 Any Street</customer_address1>
			<customer_address2></customer_address2>
			<customer_city>Any City</customer_city>
			<customer_state>TN</customer_state>
			<customer_postal_code>37013</customer_postal_code>
			<customer_country>US</customer_country>
			<customer_phone>(123) 456-7890</customer_phone>
			<customer_email>email@example.com</customer_email>
			<customer_ip>71.228.237.177</customer_ip>
			<shipping_first_name>John</shipping_first_name>
			<shipping_last_name>Doe</shipping_last_name>
			<shipping_address1>1234 Any Street</shipping_address1>
			<shipping_address2></shipping_address2>
			<shipping_city>Some City</shipping_city>
			<shipping_state>TN</shipping_state>
			<shipping_postal_code>37013</shipping_postal_code>
			<shipping_country>US</shipping_country>
			<shipping_phone></shipping_phone>
			<shipping_service_description>UPS: Ground</shipping_service_description>
			<purchase_order></purchase_order>
			<product_total>20.00</product_total>
			<tax_total>0.00</tax_total>
			<shipping_total>4.38</shipping_total>
			<order_total>24.38</order_total>
			<order_total>24.38</order_total>
			<customer_password>1aab23051b24582c5dc8e23fc595d505</customer_password>
			<custom_fields>
				<custom_field>
					<custom_field_name>My_Cool_Text</custom_field_name>
					<custom_field_value>Value123</custom_field_value>
				</custom_field>
				<custom_field>
					<custom_field_name>Subscribe</custom_field_name>
					<custom_field_value>yes</custom_field_value>
				</custom_field>
			</custom_fields>
			<transaction_details>
				<transaction_detail>
					<product_name>foo</product_name>
					<product_price>20.00</product_price>
					<product_quantity>1</product_quantity>
					<product_weight>0.10</product_weight>
					<product_code></product_code>
					<subscription_frequency>1m</subscription_frequency>
					<subscription_startdate>2007-07-07</subscription_startdate>
					<next_transaction_date>2007-08-07</next_transaction_date>
					<shipto>John Doe</shipto>
					<category_description>Default for all products</category_description>
					<category_code>DEFAULT</category_code>
					<product_delivery_type>shipped</product_delivery_type>
					<transaction_detail_options>
						<transaction_detail_option>
							<product_option_name>color</product_option_name>
							<product_option_value>blue</product_option_value>
							<price_mod></price_mod>
							<weight_mod></weight_mod>
						</transaction_detail_option>
					</transaction_detail_options>
				</transaction_detail>
			</transaction_details>
			<shipto_addresses>
				<shipto_address>
					<address_name>John Doe</address_name>
					<shipto_first_name>John</shipto_first_name>
					<shipto_last_name>Doe</shipto_last_name>
					<shipto_address1>2345 Some Address</shipto_address1>
					<shipto_address2></shipto_address2>
					<shipto_city>Some City</shipto_city>
					<shipto_state>TN</shipto_state>
					<shipto_postal_code>37013</shipto_postal_code>
					<shipto_country>US</shipto_country>
					<shipto_shipping_service_description>DHL: Next Afternoon</shipto_shipping_service_description>
					<shipto_subtotal>52.15</shipto_subtotal>
					<shipto_tax_total>6.31</shipto_tax_total>
					<shipto_shipping_total>15.76</shipto_shipping_total>
					<shipto_total>74.22</shipto_total>
					<shipto_custom_fields>
						<shipto_custom_field>
							<shipto_custom_field_name>My_Custom_Info</shipto_custom_field_name>
							<shipto_custom_field_value>john's stuff</shipto_custom_field_value>
						</shipto_custom_field>
						<shipto_custom_field>
							<shipto_custom_field_name>More_Custom_Info</shipto_custom_field_name>
							<shipto_custom_field_value>more of john's stuff</shipto_custom_field_value>
						</shipto_custom_field>
					</shipto_custom_fields>
				</shipto_address>
			</shipto_addresses>
		</transaction>
	</transactions>
</foxydata>
XML;


// ======================================================================================
// ENCRYPT YOUR XML
// Modify the include path to go to the rc4crypt file.
// ======================================================================================
include 'class.rc4crypt.php';
$XMLOutput_encrypted = rc4crypt::encrypt($myKey,$XMLOutput);
$XMLOutput_encrypted = urlencode($XMLOutput_encrypted);


// ======================================================================================
// POST YOUR XML TO YOUR SITE
// Do not modify.
// ======================================================================================
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $myURL);
curl_setopt($ch, CURLOPT_POSTFIELDS, array("FoxyData" => $XMLOutput_encrypted));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);


header("content-type:text/plain");
print $response;

?>
