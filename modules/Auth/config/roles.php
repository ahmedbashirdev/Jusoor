<?php

return [
    'redirects' => [
        'student' => '/dashboard/student',
        'company' => '/dashboard/company',
        'mentor' => '/dashboard/mentor',
    ],

    'requires_review' => [
        'company',
        'mentor',
    ],
];
