<?php

// Create this file.
// file: application/helpers/general_helper.php

if (!function_exists('checkout')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $priceId
     * @param mixed $email
     *
     * @return mixed
     */
    function checkout($priceId, $email = '')
    {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY']);
        
        $checkout_session = \Stripe\Checkout\Session::create([
            'success_url' => ConfigService::getItem('base_url') . '/billing/stripe_checkout_success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => ConfigService::getItem('base_url') . '/billing/stripe_checkout_canceled',
            'customer_email' => $email,
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]]
        ]);
        
        return $checkout_session;
    }
}

if (!function_exists('retrieve_session')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $sessionId
     *
     * @return mixed
     */
    function retrieve_session($sessionId)
    {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY']);
        
        $checkout_session = \Stripe\Checkout\Session::retrieve($sessionId);
        return $checkout_session;
    }
}

if (!function_exists('retrieve_subscription')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $subscriptionId
     *
     * @return mixed
     */
    function retrieve_subscription($subscriptionId)
    {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY']);
        
        $subscription = \Stripe\Subscription::retrieve($subscriptionId);
        return $subscription;
    }
}

if (!function_exists('create_customer')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $email
     *
     * @return mixed
     */
    function create_customer($email)
    {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY']);
        
        $customer = \Stripe\Customer::create([
            'email' => $email
        ]);
        return $customer;
    }
}

if (!function_exists('retrieve_customer')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $customerId
     *
     * @return mixed
     */
    function retrieve_customer($customerId)
    {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY']);
        
        $customer = \Stripe\Customer::retrieve($customerId);
        return $customer;
    }
}

if (!function_exists('create_payment')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $customerId
     * @param mixed $card
     * @param mixed $billing_details
     *
     * @return mixed
     */
    function create_payment($customerId, $card = [], $billing_details = [])
    {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY']);
        
        $payment = \Stripe\PaymentMethod::create(['card' => $card, 'billing_details' => $billing_details, 'type' => 'card']);
        
        if ($payment) {
            $payment->attach(['customer' => $customerId]);
        }
        
        return $payment;
    }
}

if (!function_exists('remove_payment')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $paymentId
     *
     * @return mixed
     */
    function remove_payment($paymentId)
    {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY']);
        
        $payment = \Stripe\PaymentMethod::retrieve($paymentId);
        if ($payment) {
            $payment->detach();
        }
        return $payment;
    }
}

if (!function_exists('retrieve_payment')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $customerId
     *
     * @return mixed
     */
    function retrieve_payments($customerId)
    {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY']);
        
        $payments = \Stripe\PaymentMethod::all(['customer' => $customerId, 'type' => 'card']);
        return $payments;
    }
}

if (!function_exists('change_price')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $subscriptionId
     * @param mixed $priceId
     *
     * @return mixed
     */
    function change_price($subscriptionId, $priceId)
    {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY']);
        
        $subscription = \Stripe\Subscription::retrieve($subscriptionId);
        \Stripe\Subscription::update($subscriptionId, [
            'cancel_at_period_end' => false,
            'proration_behavior' => 'create_prorations',
            'items' => [
                [
                    'id' => $subscription->items->data[0]->id,
                    'price' => $priceId,
                ],
            ],
        ]);
    }
}

if (!function_exists('cancel')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $subscriptionId
     *
     * @return mixed
     */
    function cancel($subscriptionId)
    {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY']);
        
        $subscription = \Stripe\Subscription::retrieve($subscriptionId);
        $subscription->cancel();
    }
}
