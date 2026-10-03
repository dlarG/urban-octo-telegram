<?php

return [
    // Pilot market: Sogod, Southern Leyte
    'center_lat' => 10.389569,
    'center_lng' => 124.980577,

    // Radius in km for "in pilot area" checks
    'pilot_radius_km' => 15,

    // Trust score rules
    'trust_score' => [
        'initial' => 100.00,
        'min' => 0.00,
        'max' => 100.00,
        'deltas' => [
            'payment_on_time'      =>  2.00,
            'payment_late'         => -5.00,
            'checkout_compliant'   =>  2.00,
            'checkout_violation'   => -10.00,
            'dispute_upheld'       =>  5.00,  // admin overturns a negative event
            'admin_adjustment'     =>  0.00,  // always paired with a note
        ],
    ],

    // Auth lockout
    'login_max_attempts' => 3,
    'login_lockout_minutes' => 15,

    // Upload limits
    'max_upload_kb' => 5120, // 5MB
];