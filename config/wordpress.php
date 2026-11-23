<?php

return [
    'dump_prefix' => env('WORDPRESS_DB_PREFIX', 'wp_'),
    'media_url' => env('WORDPRESS_MEDIA_URL', 'https://pta-papuabarat.go.id/wp-content/uploads'),
    'slider_id' => (int) env('WORDPRESS_SLIDER_ID', 0),
];
