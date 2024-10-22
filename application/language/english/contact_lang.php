<?php

/**
 * Custom Quote Messages
 */
$lang['custom_quote_email_subject'] = 'Contract Hound Custom Quote';
$lang['custom_quote_email_message_html'] = "<p>Custom Quote</p>\r\n".
    "<p>%%FIRST_NAME%%</p>".
    "<p>%%LAST_NAME%%</p>".
    "<p>%%COMPANY%%</p>".
    "<p>%%CONTRACT_COUNT%%</p>".
    "<p>%%EMAIL%%</p>".
    "<p>%%PHONE%%</p>";

$lang['custom_quote_email_message_text'] = "Custom Quote\r\n".
    "%%FIRST_NAME%% %%LAST_NAME%%\r\n".
    "%%COMPANY%%\r\n".
    "%%CONTRACT_COUNT%%\r\n".
    "%%EMAIL%%\r\n".
    "%%PHONE%%\r\n";

/**
 * Contact Us Message
 */
$lang['contact_us_email_subject'] = 'Contract Hound Contact Us';
$lang['contact_us_email_message_html'] = "<p>Contact Us</p>\r\n".
    "<p>%%FIRST_NAME%% %%LAST_NAME%%</p>\r\n".
    "<p>%%EMAIL%%</p>\r\n<br/>".
    "<p>%%MESSAGE%%</p>\r\n<br />\r\n";
$lang['contact_us_email_message_text'] = "Contact Us\r\n".
    "%%FIRST_NAME%% %%LAST_NAME%%\r\n".
    "%%EMAIL%%\r\n".
    "%%MESSAGE%%\r\n\r\n";