<?php

return [
    'threshold_safe' => env('SENSOR_THRESHOLD_SAFE', 50),
    'threshold_warning' => env('SENSOR_THRESHOLD_WARNING', 20),
    'max_distance' => 400,
    'online_timeout_seconds' => 30,
    'alert_check_interval_seconds' => 10,
];
