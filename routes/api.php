<?php

/**
 * API routes are loaded with middleware `api` and URL prefix `api` by Laravel.
 *
 * The Auth module defines its endpoints here so route caching and Scramble discover
 * all routes reliably on every environment (including production).
 */
require base_path('modules/Auth/routes/api.php');
