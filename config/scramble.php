<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [
    'api_path' => 'api',

    'api_domain' => null,

    'export_path' => 'api.json',

    'info' => [
        'version' => env('API_VERSION', '1.0.0'),
        'description' => <<<'MD'
# Jusoor API

Backend API for the Jusoor platform — a modular monolith built with Laravel.

## Authentication

All authenticated endpoints require a **Bearer token** in the `Authorization` header:

```
Authorization: Bearer {token}
```

Tokens are issued on login and student registration.

## Language

Send `Accept-Language: ar` or `Accept-Language: en` to control the response language.
You can also use the `?lang=ar` query parameter. Default: `ar`.

## Roles

| Role | Admin Review | Dashboard |
|------|-------------|-----------|
| Student | No | `/dashboard/student` |
| Company | Yes | `/dashboard/company` |
| Mentor | Yes | `/dashboard/mentor` |
MD,
    ],

    'ui' => [
        'title' => 'Jusoor API Docs',
        'theme' => 'light',
        'hide_try_it' => false,
        'hide_schemas' => false,
        'logo' => '',
        'try_it_credentials_policy' => 'include',
        'layout' => 'responsive',
    ],

    'servers' => null,

    'enum_cases_description_strategy' => 'description',

    'enum_cases_names_strategy' => false,

    'flatten_deep_query_parameters' => true,

    'middleware' => [
        'web',
    ],

    'extensions' => [],
];
