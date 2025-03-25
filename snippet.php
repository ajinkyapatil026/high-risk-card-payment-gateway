<?php

add_filter('woocommerce_available_payment_gateways', 'show_hide_payment_methods');
add_filter('woocommerce_available_payment_gateways', 'show_payment_gateway_for_us_ip_customers');

function show_hide_payment_methods($available_gateways) {
    if (!is_checkout()) {
        return $available_gateways;
    }
    
    // Get the current WooCommerce currency
    $currency = get_woocommerce_currency();

    // Fetch the exchange rate from the Frankfurter API
    $response = wp_remote_get('https://api.frankfurter.app/latest?from=' . $currency . '&to=USD');
    $rate = 1; // Default rate if API fails

    if (!is_wp_error($response)) {
        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($data['rates']['USD'])) {
            $rate = $data['rates']['USD']; // USD rate
        }
    }

    // Convert cart total to USD
    $cart_total_in_usd = WC()->cart->total * $rate;

    // Payment gateway restrictions based on USD total
    $gateway_conditions = [ 
        'shieldclimb-werteur' => 1.09,
        'shieldclimb-stripe' => 1.09,
        'shieldclimb-coinbase' => 2,
        'shieldclimb-robinhood' => 5,
        'shieldclimb-rampnetwork' => 4.08,
        'shieldclimb-topper' => 10,
        'shieldclimb-unlimit' => 10,
        'shieldclimb-kado' => 16.99,
        'shieldclimb-bitnovo' => 17.99,
        'shieldclimb-guardarian' => 20,
        'shieldclimb-swipelux' => 22.99,
        'shieldclimb-mercuryo' => 30,
        'shieldclimb-sardine' => 30,
        'shieldclimb-transak' => 30,
        'shieldclimb-simplex' => 50,
        'shieldclimb-utorg' => 50,
        'shieldclimb-transfi' => 70
    ];

    // Loop through all conditions to unset gateways
    foreach ($gateway_conditions as $gateway_slug => $min_amount) {
        if ($cart_total_in_usd < $min_amount && isset($available_gateways[$gateway_slug])) {
            unset($available_gateways[$gateway_slug]);
        }
    }

    return $available_gateways;
}

function show_payment_gateway_for_us_ip_customers($available_gateways) {
    if (!is_checkout() || is_admin()) {
        return $available_gateways;
    }
    
    // Get the user's country based on IP address
    $user_country = WC_Geolocation::geolocate_ip();
    
    if (isset($user_country['country']) && $user_country['country'] !== 'US') {
        // List of payment gateways you want to remove for non-US IP addresses
        unset($available_gateways['shieldclimb-stripe']);
        unset($available_gateways['shieldclimb-robinhood']);
        // Add more unset() calls here for additional payment gateways if needed
    }

    return $available_gateways;
}

?>
