<?php

class MCAPI {
    var $version = "1.0";
    var $errorMessage;
    var $errorCode;
    
    /**
     * Cache the information on the API location on the server
     */
    var $apiUrl;
    
    /**
     * Default to a 300 second timeout on server calls
     */
    var $timeout = 300; 
    
    /**
     * Default to a 8K chunk size
     */
    var $chunkSize = 8192;
    
    /**
     * Cache the user id so we only have to log in once per client instantiation
     */
    var $uuid;
    
    /**
     * Connect to the MailChimp API for a given list. All MCAPI calls require login before functioning
     * 
     * @param string $username Your MailChimp login user name - always required
     * @param string $password Your MailChimp login password - always required
     */
    function MCAPI($username, $password) {
        $this->apiUrl = parse_url("http://api.mailchimp.com/" . $this->version . "/?output=php");
        $this->uuid = $this->callServer("login", array("username" => $username, "password" => $password));
    }

    /**
     * Unschedule a campaign that is scheduled to be sent in the future
     *
     * @section Campaign  Related
     * @example xml-rpc_campaignUnschedule.php
     *
     * @param string $cid the id for the campaign to unschedule
     * @return boolean true on success
     */
    function campaignUnschedule($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignUnschedule", $params);
    }

    /**
     * Schedule a campaign to be sent in the future
     *
     * @section Campaign  Related
     * @example xml-rpc_campaignSchedule.php
     *
     * @param string $cid the id for the campaign to schedule
     * @param string $schedule_time the time to schedule the campaign - in YYYY-MM-DD HH:II:SS format in GMT
     * @return boolean true on success
     */
    function campaignSchedule($cid, $schedule_time) {
        $params = array();
        $params["cid"] = $cid;
        $params["schedule_time"] = $schedule_time;
        return $this->callServer("campaignSchedule", $params);
    }

    /**
     * Send a given campaign immediately
     *
     * @section Campaign  Related
     * @example xml-rpc_campaignSend.php
     *
     * @param string $cid the id for the campaign to send
     * @return boolean true on success
     */
    function campaignSendNow($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignSendNow", $params);
    }

    /**
     * Send a test of this campaign to the provided email address
     *
     * @section Campaign  Related
     * @example xml-rpc_campaignSendTest.php
     *
     * @param string $cid the id for the campaign to test
     * @param string $test_emails an array of email address to receive the test message
     * @param string $send_type optional default (null) sends both, "html" or "text" send just that format
     * @return boolean true on success
     */
    function campaignSendTest($cid, $test_emails=array (
), $send_type=NULL) {
        $params = array();
        $params["cid"] = $cid;
        $params["test_emails"] = $test_emails;
        $params["send_type"] = $send_type;
        return $this->callServer("campaignSendTest", $params);
    }

    /**
     * Retrieve all templates defined for your user account
     *
     * @section Campaign  Related
     * @example xml-rpc_campaignTemplates.php
     *
     * @return array An array of structs, one for each template (see Returned Fields for details)
     * @returnf integer id Id of the template
     * @returnf string name Name of the template
     * @returnf string layout Layout of the template - "basic", "left_column", "right_column", or "postcard"
     * @returnf array sections associative array of editable sections in the template that can accept custom HTML when sending a campaign
     */
    function campaignTemplates() {
        $params = array();
        return $this->callServer("campaignTemplates", $params);
    }

    /**
     * Create a new draft campaign to send
     *
     * @section Campaign  Related
     * @example xml-rpc_createCampaign.php
     *
     * @param string $list_id the list to send this campaign to- get lists using getLists()
     * @param string $subject the subject line for your campaign message
     * @param string $from_email the From: email address for your campaign message
     * @param string $from_name the From: name for your campaign message
     * @param array $content the content for this campaign - use a struct with the following keys: "html" for pasted HTML content and "text" for the plain-text version.  If you chose a template instead of pasting in your HTML content, then use "html_" followed by the template sections as keys - for example, use a key of "html_MAIN" to fill in the "MAIN" section of a template.
     * @param integer $template_id optional - use this template to generate the HTML content of the campaign
     * @param array $tracking optional - set which recipient actions will be tracked, as a struct of boolean values with the following keys: "opens", "html_clicks", and "text_clicks".  By default, opens and HTML clicks will be tracked.
     * @param string $title optional - an internal name to use for this campaign.  By default, the campaign subject will be used.
     * @param boolean $authenticate optional - set to true to enable SenderID, DomainKeys, and DKIM authentication, defaults to false
     * @param string $analytics optional - if provided, use a struct with "service type" as a key and the "service tag" as a value. For Google, this should be "google"=>"your_google_analytics_key_here". Note that only "google" is currently supported - a Google Analytics tags will be added to all links in the campaign with this string attached. Others may be added in the future
     * @return string the ID for the created campaign
     */
    function campaignCreate($list_id, $subject, $from_email, $from_name, $content, $template_id=NULL, $tracking=array (
  'opens' => true,
  'html_clicks' => true,
  'text_clicks' => false,
), $title=NULL, $authenticate=false, $analytics=NULL) {
        $params = array();
        $params["list_id"] = $list_id;
        $params["subject"] = $subject;
        $params["from_email"] = $from_email;
        $params["from_name"] = $from_name;
        $params["content"] = $content;
        $params["template_id"] = $template_id;
        $params["tracking"] = $tracking;
        $params["title"] = $title;
        $params["authenticate"] = $authenticate;
        $params["analytics"] = $analytics;
        return $this->callServer("campaignCreate", $params);
    }

    /**
     * Get the list of campaigns and associated information for a list
     *
     * @section Campaign  Related
     *
     * @param string $filter_id optional - only show campaigns from this list id - get lists using getLists()
     * @param integer $filter_folder optional - only show campaigns from this folder id - get folders using campaignFolders()
     * @param string $filter_fromname optional - only show campaigns that have this "From Name"
     * @param string $filter_fromemail optional - only show campaigns that have this "Reply-to Email"
     * @param string $filter_title optional - only show campaigns that have this title
     * @param string $filter_subject optional - only show campaigns that have this subject
     * @param string $filter_sendtimestart optional - only show campaigns that have been sent since this date/time
     * @param string $filter_sendtimeend optional - only show campaigns that have been sent before this date/time
     * @param boolean $filter_exact optional - flag for whether to filter on exact values when filtering, or search within content for filter values
     * @param integer $start optional - control paging of campaigns, start results at this campaign #, defaults to 0 (beginning)
     * @param integer $limit optional - control paging of campaigns, number of campaigns to return with each call, defaults to 25 (max=5000)
     * @return array list of campaigns and their associated information (see Returned Fields for description)
     * @returnf string id Campaign Id (used for all other campaign functions)
     * @returnf string title Title of the campaign
     * @returnf date create_time Creation time for the campaign
     * @returnf date send_time Send time for the campaign
     * @returnf int emails_sent Number of emails email was sent to
     * @returnf string status Status of the given campaign (sent,scheduled,etc.)
     * @returnf string from_name From name of the given campaign
     * @returnf string from_email Reply-to email of the given campaign
     * @returnf string subject Subject of the given campaign
     * @returnf string to_email To email string
     * @returnf string archive_url Archive link for the given campaign
     */
    function campaigns($filter_id=NULL, $filter_folder=NULL, $filter_fromname=NULL, $filter_fromemail=NULL, $filter_title=NULL, $filter_subject=NULL, $filter_sendtimestart=NULL, $filter_sendtimeend=NULL, $filter_exact=true, $start=0, $limit=25) {
        $params = array();
        $params["filter_id"] = $filter_id;
        $params["filter_folder"] = $filter_folder;
        $params["filter_fromname"] = $filter_fromname;
        $params["filter_fromemail"] = $filter_fromemail;
        $params["filter_title"] = $filter_title;
        $params["filter_subject"] = $filter_subject;
        $params["filter_sendtimestart"] = $filter_sendtimestart;
        $params["filter_sendtimeend"] = $filter_sendtimeend;
        $params["filter_exact"] = $filter_exact;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaigns", $params);
    }

    /**
     * List all the folders for a user account
     *
     * @section Campaign  Related
     *
     * @return array Array of folder structs (see Returned Fields for details)
     * @returnf integer folder_id Folder Id for the given folder, this can be used in the campaigns() function to filter on.
     * @returnf string name Name of the given folder
     */
    function campaignFolders() {
        $params = array();
        return $this->callServer("campaignFolders", $params);
    }

    /**
     * Given a list and a campaign, get all the relevant campaign statistics (opens, bounces, clicks, etc.)
     *
     * @section Campaign  Stats
     *
     * @param string $cid the campaign id to pull stats for (can be gathered using campaigns($id))
     * @return array struct of the statistics for this campaign
     * @returnf integer syntax_errors Number of email addresses in campaign that had syntactical errors.
     * @returnf integer hard_bounces Number of email addresses in campaign that hard bounced.
     * @returnf integer soft_bounces Number of email addresses in campaign that soft bounced.
     * @returnf integer unsubscribes Number of email addresses in campaign that unsubscribed.
     * @returnf integer abuse_reports Number of email addresses in campaign that reported campaign for abuse.
     * @returnf integer forwards Number of times email was forwarded to a friend.
     * @returnf integer forwards_opens Number of times a forwarded email was opened.
     * @returnf integer opens Number of times the campaign was opened.
     * @returnf date last_open Date of the last time the email was opened.
     * @returnf integer unique_opens Number of people who opened the campaign.
     * @returnf integer clicks Number of times a link in the campaign was clicked.
     * @returnf integer unique_clicks Number of unique recipient/click pairs for the campaign.
     * @returnf date last_click Date of the last time a link in the email was clicked.
     * @returnf integer users_who_clicked Number of unique recipients who clicked on a link in the campaign.
     * @returnf integer emails_sent Number of email addresses campaign was sent to.
     */
    function campaignStats($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignStats", $params);
    }

    /**
     * Get an array of the urls being tracked, and their click counts for a given campaign
     *
     * @section Campaign  Stats
     *
     * @param string $cid the campaign id to pull stats for (can be gathered using campaigns($id))
     * @return struct list of urls and their associated statistics
     * @returnf integer clicks Number of times the specific link was clicked
     * @returnf integer unique Number of unique people who clicked on the specific link
     */
    function campaignClickStats($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignClickStats", $params);
    }

    /**
     * Get all bounced email addresses for a given campaign<br/>
     * <strong>DEPRECATED:</strong> campaignBounces() has been deprecated and will be removed completely in a future release. see campaignHardBounces() and campaignSoftBounces() for replacements.
     *
     * @section Campaign  Stats
     *
     * @deprecated See campaignHardBounces() and campaignSoftBounces() for replacements
     * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns($id))
     * @return struct Struct of arrays of bounced email addresses (hard and soft)
     * @returnf array hard Array of all email addresses that had Hard bounces for this campaign
     * @returnf array soft Array of all email addresses that had Soft bounces for this campaign
     * @returnf array syntax Array of all email addresses that had syntax errors in them (historical - always empty)
     */
    function campaignBounces($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignBounces", $params);
    }

    /**
     * Get all email addresses with Hard Bounces for a given campaign
     *
     * @section Campaign  Stats
     *
     * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns($id))
     * @param int    $start optional, for large data sets, the page number to start at - defaults to 1st page of data
     * @param int    $limit optional, for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
     * @return array Arrays of email addresses with Hard Bounces
     */
    function campaignHardBounces($cid, $start=0, $limit=1000) {
        $params = array();
        $params["cid"] = $cid;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaignHardBounces", $params);
    }

    /**
     * Get all email addresses with Soft Bounces for a given campaign
     *
     * @section Campaign  Stats
     *
     * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns($id))
     * @param int    $start optional, for large data sets, the page number to start at - defaults to 1st page of data
     * @param int    $limit optional, for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
     * @return array Arrays of email addresses with Soft Bounces
     */
    function campaignSoftBounces($cid, $start=0, $limit=1000) {
        $params = array();
        $params["cid"] = $cid;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaignSoftBounces", $params);
    }

    /**
     * Get all unsubscribed email addresses for a given campaign
     *
     * @section Campaign  Stats
     *
     * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns($id))
     * @param int    $start optional, for large data sets, the page number to start at - defaults to 1st page of data
     * @param int    $limit optional, for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
     * @return array list of email addresses that unsubscribed from this campaign
     */
    function campaignUnsubscribes($cid, $start=0, $limit=1000) {
        $params = array();
        $params["cid"] = $cid;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaignUnsubscribes", $params);
    }

    /**
     * Get all email addresses that complained about a given campaign
     *
     * @section Campaign  Stats
     *
     * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns($id))
     * @param int    $start optional, for large data sets, the page number to start at - defaults to 1st page of data
     * @param int    $limit optional, for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
     * @return array list of email addresses that complained (filed abuse reports) about this campaign
     */
    function campaignAbuseReports($cid, $start=0, $limit=1000) {
        $params = array();
        $params["cid"] = $cid;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaignAbuseReports", $params);
    }

    /**
     * Get the content (both html and text) for a campaign, exactly as it would appear in the campaign archive
     *
     * @section Campaign  Related
     *
     * @param string $cid the campaign id to get content for (can be gathered using campaigns($id))
     * @return struct Struct containing all content for the campaign (see Returned Fields for details
     * @returnf string html The HTML content used for the campgain with merge tags intact
     * @returnf string text The Text content used for the campgain with merge tags intact
     */
    function campaignContent($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignContent", $params);
    }

    /**
     * Retrieve the list of email addresses that opened a given campaign with how many times they opened
     *
     * @section Campaign AIM
     *
     * @param string $cid the campaign id to get opens for (can be gathered using campaigns($id))
     * @param int    $start optional, for large data sets, the page number to start at - defaults to 1st page of data
     * @param int    $limit optional, for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
     * @return array Array of structs containing email addresses and open counts
     * @returnf string email Email address that opened the campaign
     * @returnf integer open_count Total number of times the campaign was opened by this email address
     */
    function campaignOpenedAIM($cid, $start=0, $limit=1000) {
        $params = array();
        $params["cid"] = $cid;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaignOpenedAIM", $params);
    }

    /**
     * Retrieve the list of email addresses that did not open a given campaign
     *
     * @section Campaign AIM
     *
     * @param string $cid the campaign id to get no opens for (can be gathered using campaigns($id))
     * @param int    $start optional, for large data sets, the page number to start at - defaults to 1st page of data
     * @param int    $limit optional, for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
     * @return array list of email addresses that did not open a campaign
     */
    function campaignNotOpenedAIM($cid, $start=0, $limit=1000) {
        $params = array();
        $params["cid"] = $cid;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaignNotOpenedAIM", $params);
    }

    /**
     * Return the list of email addresses that clicked on a given url, and how many times they clicked
     *
     * @section Campaign AIM
     *
     * @param string $cid the campaign id to get click stats for (can be gathered using campaigns($id))
     * @param string $url the URL of the link that was clicked on
     * @param int    $start optional, for large data sets, the page number to start at - defaults to 1st page of data 
     * @param int    $limit optional, for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
     * @return array Array of structs containing email addresses and click counts
     * @returnf string email Email address that opened the campaign
     * @returnf integer clicks Total number of times the URL was clicked on by this email address
     */
    function campaignClickDetailAIM($cid, $url, $start=0, $limit=1000) {
        $params = array();
        $params["cid"] = $cid;
        $params["url"] = $url;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaignClickDetailAIM", $params);
    }

    /**
     * Given a campaign and email address, return the entire click and open history with timestamps, ordered by time
     *
     * @section Campaign AIM
     *
     * @param string $cid the campaign id to get stats for (can be gathered using campaigns($id))
     * @param string $email_address the email address to get activity history for
     * @return array Array of structs containing actions (opens and clicks) that the email took, with timestamps
     * @returnf string action The action taken (open or click)
     * @returnf date timestamp Time the action occurred
     * @returnf string url For clicks, the URL that was clicked
     */
    function campaignEmailStatsAIM($cid, $email_address) {
        $params = array();
        $params["cid"] = $cid;
        $params["email_address"] = $email_address;
        return $this->callServer("campaignEmailStatsAIM", $params);
    }

    /**
     * Retrieve all of the lists defined for your user account
     *
     * @section List Related
     * @example xml-rpc_lists.php
     *
     * @return array list of your Lists and their associated information (see Returned Fields for description)
     * @returnf string id The list id for this list. This will be used for all other list management functions.
     * @returnf string name The name of the list.
     * @returnf date date_created The date that this list was created.
     * @returnf integer member_count The number of active members in the given list.
     */
    function lists() {
        $params = array();
        return $this->callServer("lists", $params);
    }

    /**
     * Get the list of merge tags for a given list, including their name, tag, and required setting
     *
     * @section List Related
     *
     * @param string $id the list id to connect to
     * @return array list of merge tags for the list
     * @returnf string name Name of the merge field
     * @returnf char req Denotes whether the field is required (Y) or not (N)
     * @returnf string tag The merge tag that's used for forms and listSubscribe() and listUpdateMember()
     */
    function listMergeVars($id) {
        $params = array();
        $params["id"] = $id;
        return $this->callServer("listMergeVars", $params);
    }

    /**
     * Get the list of interest groups for a given list, including the label and form information
     *
     * @section List Related
     *
     * @param string $id the list id to connect to
     * @return struct list of interest groups for the list
     * @returnf string name Name for the Interest groups
     * @returnf string form_field Gives the type of interest group: checkbox,radio,select
     * @returnf array groups Array of the group names
     */
    function listInterestGroups($id) {
        $params = array();
        $params["id"] = $id;
        return $this->callServer("listInterestGroups", $params);
    }

    /**
     * Subscribe the provided email to a list
     *
     * @section List Related
     *
     * @example mcapi_listSubscribe.php
     * @example xml-rpc_listSubscribe.php
     *
     * @param string $id the list id to connect to
     * @param string $email_address the email address to subscribe
     * @param array $merge_vars array of merges for the email (FNAME, LNAME, etc.) (see examples below for handling "blank" arrays)
     * @param string $email_type optional - email type preference for the email (html or text, defaults to html)
     * @param boolean $double_optin optional - flag to control whether a double opt-in confirmation message is sent, defaults to true
     * @return boolean true on success, false on failure. When using MCAPI.class.php, the value can be tested and error messages pulled from the MCAPI object (see below)
     */
    function listSubscribe($id, $email_address, $merge_vars, $email_type='html', $double_optin=true) {
        $params = array();
        $params["id"] = $id;
        $params["email_address"] = $email_address;
        $params["merge_vars"] = $merge_vars;
        $params["email_type"] = $email_type;
        $params["double_optin"] = $double_optin;
        return $this->callServer("listSubscribe", $params);
    }

    /**
     * Unsubscribe the given email address from the list
     *
     * @section List Related
     * @example mcapi_listUnsubscribe.php
     *
     * @param string $id the list id to connect to
     * @param string $email_address the email address to unsubscribe
     * @param boolean $delete_member flag to completely delete the member from your list instead of just unsubscribing, default to false
     * @param boolean $send_goodbye flag to send the goodbye email to the email address, defaults to true
     * @param boolean $send_notify flag to send the unsubscribe notification email to the address defined in the list email notification settings, defaults to true
     * @return boolean true on success, false on failure. When using MCAPI.class.php, the value can be tested and error messages pulled from the MCAPI object (see below)
     */
    function listUnsubscribe($id, $email_address, $delete_member=false, $send_goodbye=true, $send_notify=true) {
        $params = array();
        $params["id"] = $id;
        $params["email_address"] = $email_address;
        $params["delete_member"] = $delete_member;
        $params["send_goodbye"] = $send_goodbye;
        $params["send_notify"] = $send_notify;
        return $this->callServer("listUnsubscribe", $params);
    }

    /**
     * Edit the email address, merge fields, and interest groups for a list member
     *
     * @section List Related
     *
     * @param string $id the list id to connect to
     * @param string $email_address the current email address of the member to update
     * @param array $merge_vars array of new field values to update the member with.  Use "EMAIL" to update the email address and "INTERESTS" to update the interest groups
     * @param string $email_type change the email type preference for the member ("html" or "text").  Leave blank to keep the existing preference (optional)
     * @param boolean $replace_interests flag to determine whether we replace the interest groups with the updated groups provided, or we add the provided groups to the member's interest groups (optional, defaults to true)
     * @return boolean true on success, false on failure. When using MCAPI.class.php, the value can be tested and error messages pulled from the MCAPI object
     */
    function listUpdateMember($id, $email_address, $merge_vars, $email_type='', $replace_interests=true) {
        $params = array();
        $params["id"] = $id;
        $params["email_address"] = $email_address;
        $params["merge_vars"] = $merge_vars;
        $params["email_type"] = $email_type;
        $params["replace_interests"] = $replace_interests;
        return $this->callServer("listUpdateMember", $params);
    }

    /**
     * Subscribe a batch of email addresses to a list at once
     *
     * @section List Related
     * @example xml-rpc_listBatchSubscribe.php
     *
     * @param string $id the list id to connect to
     * @param array $batch an array of structs for each address to import with two special keys: "EMAIL" for the email address, and "EMAIL_TYPE" for the email type option (html or text) 
     * @param boolean $double_optin flag to control whether to send an opt-in confirmation email - defaults to true
     * @param boolean $update_existing flag to control whether to update members that are already subscribed to the list or to return an error, defaults to false (return error)
     * @param boolean $replace_interests flag to determine whether we replace the interest groups with the updated groups provided, or we add the provided groups to the member's interest groups (optional, defaults to true)
     * @return struct Array of result counts and any errors that occurred
     * @returnf integer success_count Number of email addresses that were succesfully added/updated
     * @returnf integer error_count Number of email addresses that failed during addition/updating
     * @returnf array errors Array of error structs. Each error struct will contain "code", "message", and "email_address"
     */
    function listBatchSubscribe($id, $batch, $double_optin=true, $update_existing=false, $replace_interests=true) {
        $params = array();
        $params["id"] = $id;
        $params["batch"] = $batch;
        $params["double_optin"] = $double_optin;
        $params["update_existing"] = $update_existing;
        $params["replace_interests"] = $replace_interests;
        return $this->callServer("listBatchSubscribe", $params);
    }

    /**
     * Unsubscribe a batch of email addresses to a list
     *
     * @section List Related
     *
     * @param string $id the list id to connect to
     * @param array $emails array of email addresses to unsubscribe
     * @param boolean $delete_member flag to completely delete the member from your list instead of just unsubscribing, default to false
     * @param boolean $send_goodbye flag to send the goodbye email to the email addresses, defaults to true
     * @param boolean $send_notify flag to send the unsubscribe notification email to the address defined in the list email notification settings, defaults to false
     * @return struct Array of result counts and any errors that occurred
     * @returnf integer success_count Number of email addresses that were succesfully added/updated
     * @returnf integer error_count Number of email addresses that failed during addition/updating
     * @returnf array errors Array of error structs. Each error struct will contain "code", "message", and "email_address"
     */
    function listBatchUnsubscribe($id, $emails, $delete_member=false, $send_goodbye=true, $send_notify=false) {
        $params = array();
        $params["id"] = $id;
        $params["emails"] = $emails;
        $params["delete_member"] = $delete_member;
        $params["send_goodbye"] = $send_goodbye;
        $params["send_notify"] = $send_notify;
        return $this->callServer("listBatchUnsubscribe", $params);
    }

    /**
     * Get all of the list members of a list that are of a particular status
     *
     * @section List Related
     * @example mcapi_listMembers.php
     *
     * @param string $id the list id to connect to
     * @param string $status the status to get members for - one of(subscribed, unsubscribed, or cleaned), defaults to subscribed
     * @param int    $start optional, for large data sets, the page number to start at - defaults to 1st page of data 
     * @param int    $limit optional, for large data sets, the number of results to return - defaults to 100, upper limit set at 15000
     * @return array Array of list member structs (see Returned Fields for details)
     * @returnf string email Member email address
     * @returnf date timestamp timestamp of their associated status(date subscribed, unsubscribed, or cleaned)
     */
    function listMembers($id, $status='subscribed', $start=0, $limit=100) {
        $params = array();
        $params["id"] = $id;
        $params["status"] = $status;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("listMembers", $params);
    }

    /**
     * Get all the information for a particular member of a list
     *
     * @section List Related
     * @example mcapi_listMemberInfo.php
     * @param string $id the list id to connect to
     * @param string $email_address the member email address to get information for
     * @return array array of list member info (see Returned Fields for details)
     * @returnf string email The email address associated with this record
     * @returnf string email_type The type of emails this customer asked to get: html or text
     * @returnf array merges An associative array of all the merge tags and the data for those tags for this email address
     * @returnf string status The subscription status for this email address, either subscribed, unsubscribed or cleaned
     * @returnf date timestamp The time this email address was added to the list
     */
    function listMemberInfo($id, $email_address) {
        $params = array();
        $params["id"] = $id;
        $params["email_address"] = $email_address;
        return $this->callServer("listMemberInfo", $params);
    }

    /**
     * Internal function - proxy method for certain XML-RPC calls | DO NOT CALL
     * @param mixed Method to call, with any parameters to pass along
     * @return mixed the result of the call
     */
    function callMethod() {
        $params = array();
        return $this->callServer("callMethod", $params);
    }
    
    /**
     * Actually connect to the server and call the requested methods, parsing the result
     * You should never have to call this function manually
     */
    function callServer($method, $params) {
    	//Always include the user id if we're not loggin in
    	if($method != "login") {
    		$params["uid"] = $this->uuid;
    	}
        
        $post_vars = $this->httpBuildQuery($params);
        
        $payload = "POST " . $this->apiUrl["path"] . "?" . $this->apiUrl["query"] . "&method=" . $method . " HTTP/1.0\r\n";
        $payload .= "Host: " . $this->apiUrl["host"] . "\r\n";
        $payload .= "Content-type: application/x-www-form-urlencoded\r\n";
        $payload .= "Content-length: " . strlen($post_vars) . "\r\n";
        $payload .= "Connection: close \r\n\r\n";
        $payload .= $post_vars;
        
        ob_start();
        $sock = fsockopen($this->apiUrl["host"], 80, $errno, $errstr, $this->timeout);
        if(!$sock) {
            $this->errorMessage = "Could not connect (ERR $errno: $errstr)";
            $this->errorCode = "SERVER_ERROR";
            ob_end_clean();
            return false;
        }
        
        $response = "";
        fwrite($sock, $payload);
        while(!feof($sock)) {
            $response .= fread($sock, $this->chunkSize);
        }
        fclose($sock);
        ob_end_clean();
        
        list($throw, $response) = explode("\r\n\r\n", $response, 2);
        
        if(ini_get("magic_quotes_runtime")) $response = stripslashes($response);
        
        $serial = unserialize($response);
        if($response && $serial === false) {
        	$response = array("error" => "Bad Response.  Got This: " . $response, "code" => "SERVER_ERROR");
        } else {
        	$response = $serial;
        }
        if(is_array($response) && isset($response["error"])) {
            $this->errorMessage = $response["error"];
            $this->errorCode = $response["code"];
            return false;
        }
        
        return $response;
    }
    
    /**
     * Re-implement http_build_query for systems that do not already have it
     */
    function httpBuildQuery($params, $key=null) {
        $ret = array();
        
        foreach((array) $params as $name => $val) {
            $name = urlencode($name);
            if($key !== null) {
                $name = $key . "[" . $name . "]";
            }
            
            if(is_array($val) || is_object($val)) {
                $ret[] = $this->httpBuildQuery($val, $name);
            } elseif($val !== null) {
                $ret[] = $name . "=" . urlencode($val);
            }
        }
        
        return implode("&", $ret);
    }
}

?>