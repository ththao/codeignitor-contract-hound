<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$envLocation = dirname(__DIR__, 2);
$dotenv = Dotenv\Dotenv::createImmutable($envLocation);
$dotenv->load();

/*
|--------------------------------------------------------------------------
| Free trial period
|--------------------------------------------------------------------------
|
| How long (in months) should a new paid account have as a free trial?
|
*/
$config['trial_period'] = 14; // 14 Days

$config['default_profile_limit'] = 1;

$config['default_plan_recommendation'] = 1;

if (isset($_ENV['STRIPE_ENV']) && $_ENV['STRIPE_ENV'] == 'production') {
    // Production Stripe Plan IDs here
    $config['plans'] = [
        1 => ['label' => '50', 'price' => 95, 'stripe_plan_id' => 'price_1HZnvMA2NT8huHQNQqekTp3n'],
        2 => ['label' => '100', 'price' => 190, 'stripe_plan_id' => 'price_1HZnRjA2NT8huHQNtaqDGeq2'],
        3 => ['label' => '150', 'price' => 285, 'stripe_plan_id' => 'price_1HZnmuA2NT8huHQNhImnKfOZ'],
        4 => ['label' => '200', 'price' => 380, 'stripe_plan_id' => 'price_1HuD5pA2NT8huHQNc2ROaXvs'],
        5 => ['label' => '250', 'price' => 475, 'stripe_plan_id' => 'price_1HuD6LA2NT8huHQNAeJFG61T'],
        6 => ['label' => '300', 'price' => 570, 'stripe_plan_id' => 'price_1HuD6vA2NT8huHQNR38DMps9'],
        7 => ['label' => '350', 'price' => 665, 'stripe_plan_id' => 'price_1HuD7KA2NT8huHQNpiiZEAx8'],
        8 => ['label' => '400', 'price' => 760, 'stripe_plan_id' => 'price_1HuD7mA2NT8huHQNbRFEpK7n'],
        9 => ['label' => '450', 'price' => 855, 'stripe_plan_id' => 'price_1HuD89A2NT8huHQNpT39F8AW'],
        10 => ['label' => '500', 'price' => 950, 'stripe_plan_id' => 'price_1HuD8iA2NT8huHQNLmohFf9J']
    ];
} else {
    // Testing Stripe Plan IDs here
    $config['plans'] = [
      1 => ['label' => '50', 'price' => 95, 'stripe_plan_id' => 'price_1HduHKA2NT8huHQNwwyTDVsl'],
      2 => ['label' => '100', 'price' => 190, 'stripe_plan_id' => 'price_1Hf3AVA2NT8huHQNjhsqkeGf'],
      3 => ['label' => '150', 'price' => 285, 'stripe_plan_id' => 'price_1Hf3AvA2NT8huHQNY0AgmlR4'],
      4 => ['label' => '200', 'price' => 380, 'stripe_plan_id' => 'price_1Hf3BQA2NT8huHQNHFu95imK'],
      5 => ['label' => '250', 'price' => 475, 'stripe_plan_id' => 'price_1Hf3BkA2NT8huHQN6RH3LMzD'],
      6 => ['label' => '300', 'price' => 570, 'stripe_plan_id' => 'price_1Hf3C8A2NT8huHQN0Y4OECve'],
      7 => ['label' => '350', 'price' => 665, 'stripe_plan_id' => 'price_1Hf3CPA2NT8huHQNlSSXjJeW'],
      8 => ['label' => '400', 'price' => 760, 'stripe_plan_id' => 'price_1Hf3CiA2NT8huHQNjaqNWDDP'],
      9 => ['label' => '450', 'price' => 855, 'stripe_plan_id' => 'price_1Hf3D0A2NT8huHQN2cHp66GL'],
      10 => ['label' => '500', 'price' => 950, 'stripe_plan_id' => 'price_1Hf3DLA2NT8huHQNRlYdcjBu']
    ];
}

$config['coupons'] = array(
	'grandopening' => array(
		'type'    => 'percent'
		,'amount' => '20'
	)
);
