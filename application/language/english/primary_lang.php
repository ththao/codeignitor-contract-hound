<?php defined('BASEPATH') OR exit('No direct script access allowed');

$lang['member_email_exists'] = "Email already in use.";
$lang['business_email_required'] = "Please use your business email address to create an account";

$lang['member_email_requires_password'] = 'Enter new or re-enter existing password required to change email.';
$lang['member_incorrect_current_password'] = 'Current password invalid';

/* DEPRICATED */
$lang['member_confirmation_email_subject'] = 'Contract Hound - Confirm Email';
$lang['member_confirmation_email_message_html'] = "%%TOKEN%%";
$lang['member_confirmation_email_message_text'] = "%%TOKEN%%";

$lang['member_reset_password_request_subject'] = 'Contract Hound -  Password Reset';
$lang['member_reset_password_request_html'] = "%%TOKEN%%";
$lang['member_reset_password_request_text'] = "%%TOKEN%%";

$lang['member_welcome_email_subject'] = 'Welcome to Contract Hound';
$lang['member_welcome_email_text'] = 'Hi! Thanks for signing up.';
$lang['member_welcome_email_html'] = 'Hi! Thanks for signing up.';

$lang['member_confirmation_email_subject_new'] = 'Contract Hound Confirm Email';
$lang['member_confirmation_email_message_html_new'] = "<p>Welcome to Contract Hound!</p>\r\n".
	"<p>Before you can get started setting up your website(s) in Contract Hound, you must verify ownership of your e-mail ".
	"account to complete your registration.</p>\r\n".
	"<p>Please copy the following confirmation token:</p>\r\n<br />\r\n".
	"<p>%%TOKEN%%</p>\r\n<br />\r\n".
	"<p>Or go to <a href=\"%%URL%%\">%%URL%%</a></p>\r\n<br />\r\n".
	"<p>And paste it into the information box on the confirmation page that opened when you signed up.</p>\r\n".
	"<p>If you have any trouble accessing your account, please contact support@contracthound.com.</p>\r\n".
	"<p>Have a great day!<br/>Contract Hound Support Team</p>";
$lang['member_confirmation_email_message_text_new'] = "Welcome to Contract Hound!\r\n".
	"Before you can get started setting up your website(s) in Contract Hound, you must verify ownership of your e-mail ".
	"account to complete your registration.\r\n\r\n".
	"Please copy the following confirmation token:\r\n\r\n".
	"%%TOKEN%%\r\n\r\n".
	"Or go to %%URL%%\r\n\r\n".
	"And paste it into the information box on the confirmation page that opened when you signed up.\r\n".
	"If you have any trouble accessing your account, please contact support@contracthound.com.\r\n\r\n".
	"Have a great day!\r\nContract Hound Support Team";

$lang['subscription_status_active']     = 'Active';
$lang['subscription_status_cancelled']  = 'Cancelled';
$lang['subscription_status_suspended']  = 'Suspended';
$lang['subscription_status_expired']    = 'Expired';
$lang['subscription_status_terminated'] = 'Terminated';

$lang['member_confirm_email_text'] = 'You\'re one click away from simplifying your contract management process. Click the link below to confirm your email address and get started with Contract Hound.';

$lang['contract_approval_status_pending'] = 'Waiting';
$lang['contract_approval_status_approved'] = 'Approved';
$lang['contract_approval_status_rejected'] = 'Rejected';
$lang['contract_approval_status_waiting'] = '';
$lang['contract_approval_status_skipped'] = 'Skipped';

$lang['contract_signature_status_waiting'] = '';
$lang['contract_signature_status_pending'] = 'Waiting';
$lang['contract_signature_status_signed']  = 'Signed';
$lang['contract_signature_status_rejected'] = 'Rejected';

$lang['contract_approval_notify_subject_text'] = 'ContractHound - Approval Needed for a Contract';
$lang['contract_approval_notify_subject_reminder_text'] = 'ContractHound - Reminder: Approval Needed for a Contract';
$lang['contract_approval_notify_message_text'] = '%%UPLOADER_NAME%% has uploaded the contract '.
	'%%FILENAME%% on %%UPLOAD_DATE%%. Please go to the url below to view and '.
	'approve or reject this contract. Remember, if you\'d like to collaborate with your team '.
	'on this contract you can post and answer questions within the chat box on the contract details page.'.
	"\n\n%%URLTOCONTRACT%%";

$lang['contract_reject_notify_subject_text'] = 'ContractHound - Contract Rejected';
$lang['contract_reject_notify_message_text'] = 'Your contract, '.
	'%%FILENAME%%, uploaded on %%UPLOAD_DATE%% was rejected by %%REJECTORNAME%%. '.
	'Remember, if you\'d like to collaborate with your team '.
	'on this contract you can post and answer questions within the chat box on the contract details page.'.
	"\n\n%%URLTOCONTRACT%%";

// general
$lang['Close'] = 'Close';
$lang['Cancel'] = 'Cancel';
$lang['Boards help you organize your contracts.'] = 'Folders help you organize your contracts.';
$lang['New Contract or Board'] = 'New Contract or Folder';

// browse boards
$lang['browse_boards'] = 'Browse Folders';
$lang['Browse Boards'] = 'Browse Folders';
$lang['Boards help you organize your contracts. These are all the boards managed in Contract Hound.'] = 'Folders help you organize your contracts. These are all the folders managed in Contract Hound.';
$lang['Boards'] = 'Folders';
$lang['Search Boards...'] = 'Search Folders...';
$lang['That\'s the end of your board list.'] = 'That\'s the end of your folders list.';
$lang['Add a Board'] = 'Add a Folder';
$lang['Add Board'] = 'Add Folder';
$lang['Add a board to get set up!'] = 'Add a folder to get set up!';
$lang['It looks like you\'ve just started an account.'] = 'It looks like you\'ve just started an account.';

// New Board
$lang['New Board'] = 'New Folder';
$lang['Use boards to group contracts and stay organized.  Create boards to organize contracts by client, business goal, contract type, or anything else.'] =
	'Use folders to group contracts and stay organized.  Create folders to organize contracts by client, business goal, contract type, or anything else.';
$lang['Name your Board'] = 'Name your Folder';
$lang['Marketing Board...'] = 'Marketing Folder...';
$lang['Create Board'] = 'Create Folder';
$lang['Enter a name that describes the kind of contracts that will live here, like "Marketing Team Contracts" or "Sell-side Contracts."'] =
	'Enter a name that describes the kind of contracts that will live here, like "Marketing Team Contracts" or "Sell-side Contracts."';

// view contract
$lang['Move this contract to a different board.'] = 'Move this contract to a different folder.';

// upload contract
$lang['Choose a Board'] = 'Choose a Folder';
$lang['Organize these contracts by adding them to a board.'] = 'Organize these contracts by adding them to a folder.';
$lang['I\'ll choose a board later...'] = 'I\'ll choose a folder later...';

// welcome
$lang['Create a Board'] = 'Create a Folder';

// view board
$lang['Board'] = 'Folder';
$lang['Remove from board'] = 'Remove from folder';
$lang['Delete this Board?'] = 'Delete this Folder?';
$lang['Are you sure you want to delete this board?'] = 'Are you sure you want to delete this folder?';
$lang['Rename this board'] = 'Rename this folder';
$lang['Rename Board'] = 'Rename Folder';
$lang['Delete Board'] = 'Delete Folder';
$lang['Add Sub Board'] = 'Add Sub Folder';
$lang['New Sub Board'] = 'New Sub Folder';
$lang['Parent Folder'] = 'Parent Folder';
$lang['Contracts for this board will be unassociated.'] = 'Contracts for this folder will be unassociated.';
$lang['All contracts will be removed from this board and moved to "All Contracts".'] = 'All contracts will be removed from this folder and moved to "All Contracts".';
$lang['Change the name of your board so your team can easily find its contracts later.'] = 'Change the name of your folder so your team can easily find its contracts later.';

$lang['Buy-side'] = 'Vendor';
$lang['buy-side'] = 'vendor';
$lang['Sell-side'] = 'Customer';
$lang['sell-side'] = 'customer';

$lang['docusign_push_contract_page_notification'] = 'Please login to DocuSign to finish the sending process.';
$lang['docusign_push_email_subject'] = 'A document has been sent to DocuSign';
$lang['docusign_push_email'] = 'The document "%%CONTRACT_NAME%%" has been sent to DocuSign. Please log in to DocuSign to access this contract and choose the location of the signature fields.';

$lang['docusign_pull_email_subject'] = 'A document has been signed in DocuSign';
$lang['docusign_pull_email'] = '%%SIGNERS%% %%VERB%% signed the contract "%%CONTRACT_NAME%%".';

$lang['docusign_rejected_email_subject'] = 'A document has been rejected in DocuSign';
$lang['docusign_rejected_email'] = '%%SIGNERS%% %%VERB%% rejected the contract "%%CONTRACT_NAME%%".';

$lang['docusign_token_expired_html'] = 'Hi, it looks like we\'re running into issues communicating to DocuSign from your Contract Hound account. You\'ll need to re-connect your DocuSign account to Contract Hound in order to send and/or receive docs to DocuSign.'.
	'Please click here to login to your Contract Hound account and reconnect DocuSign.';
	
$lang['docusign_expired_token_notification'] = 'It looks like your DocuSign integration has been disconnected and you have contracts waiting to be signed. You will need to re-connect your DocuSign account to sign these contracts.';
$lang['docusign_expired_token_subject'] = 'DocuSign connection issue';

$lang['docusign_contract_pushed_subject'] = 'Document moved to DocuSign';
$lang['docusign_contract_pushed_message'] = 'The document "%%CONTRACT_NAME%%" has been sent to DocuSign. Please log in to DocuSign to access this contract and choose the location of the signature fields.';