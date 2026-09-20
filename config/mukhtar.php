<?php

return [

    'user' => [
        'name' => env('MUKHTAR_USER_NAME', 'المختار'),
        'email' => env('MUKHTAR_USER_EMAIL', 'mukhtar@muktar.local'),
        'pin' => env('MUKHTAR_PIN', '123456'),
    ],

    'pin' => [
        'length' => 6,
    ],

    'neighborhood' => env('MUKHTAR_NEIGHBORHOOD', 'حي الاسكان العسكري'),

    'pagination' => [
        'families_per_page' => (int) env('MUKHTAR_FAMILIES_PER_PAGE', 20),
    ],

    'data' => [
        'backup_schema' => 'muktar.families.v1',
        'max_upload_kilobytes' => (int) env('MUKHTAR_DATA_UPLOAD_KB', 10240),
        'families_sheet' => 'العوائل',
        'children_sheet' => 'الأولاد',
    ],

];
