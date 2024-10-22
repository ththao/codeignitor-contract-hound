<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Free trial period
|--------------------------------------------------------------------------
|
| How long (in months) should a new paid account have as a free trial?
|
*/

$config['hc_checks'] = array(
	'send_reminders' => 'https://hc-ping.com/fd5c9df5-74d7-4648-9d72-94e43f863786',
    'check_subscriptions' => 'https://hc-ping.com/267c6d1a-e532-4ff1-b086-8fdf4314cbe1',
    'expire_trials' => 'https://hc-ping.com/39e53b4c-b549-47ea-8726-e5581ac4720c',
    'update_intercom' => 'https://hc-ping.com/60c1d10f-7cb3-4184-a3a1-58b18e5c4de6',
    'docusign_refresh_tokens' => 'https://hc-ping.com/bfae8cab-dd1e-4c84-b41d-a733676b84d4',
    'docusign_check_contracts' => 'https://hc-ping.com/92c79034-1894-4109-b6fb-1c7671231783',
    'docusign_send_contracts' => 'https://hc-ping.com/1dd949bc-3ddc-49b1-8e72-f3d8bd40d98d' 
);
