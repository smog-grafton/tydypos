<?php

return [
    'theme' => [
        'primary' => '#2563EB',
        'primary_dark' => '#1D4ED8',
        'accent' => '#F59E0B',
        'success' => '#16A34A',
        'danger' => '#DC2626',
        'background' => '#F8FAFC',
        'surface' => '#FFFFFF',
        'text_primary' => '#0F172A',
        'text_muted' => '#64748B',
        'border' => '#E2E8F0',
    ],
    'branding' => [
        'app_name' => env('APP_NAME', 'Tydy POS'),
        'tagline' => 'Fast cashier workflows with dependable receipts.',
        'company_name' => 'Tydy POS',
        'support_email' => 'support@tydypos.test',
        'default_locale' => env('APP_LOCALE', 'en'),
    ],
    'receipts' => [
        'default_template' => 'thermal-80',
        'widths' => ['58mm', '80mm', 'a4'],
    ],
    'uploads' => [
        'disk' => env('FILESYSTEM_DISK', 'public_uploads'),
        'path' => 'branding',
    ],
];
