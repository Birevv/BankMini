<?php

return [
    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => 'Lewati ke konten',
    ],

    'actions' => [

        'billing' => [
            'label' => 'Kelola langganan',
        ],

        'logout' => [
            'label' => 'Keluar',
        ],

        'open_database_notifications' => [
            'label' => 'Buka notifikasi',
            'label_with_unread_count' => '{1} Notifikasi, :count notifikasi belum dibaca|[2,*] Notifikasi, :count notifikasi belum dibaca',
        ],

        'open_user_menu' => [
            'label' => 'Menu pengguna',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => 'Persempit menu',
            ],

            'expand' => [
                'label' => 'Perluas menu',
            ],

        ],

        'theme_switcher' => [
            'label' => 'Tema',

            'dark' => [
                'label' => 'Gunakan mode gelap',
            ],

            'light' => [
                'label' => 'Gunakan mode terang',
            ],

            'system' => [
                'label' => 'Ikuti tema perangkat',
            ],
        ],

    ],

    'navigation' => [
        'label' => 'Navigasi sidebar',
    ],

    'topbar' => [
        'label' => 'Bilah atas',
    ],

    'avatar' => [
        'alt' => 'Avatar :name',
    ],

    'logo' => [
        'alt' => 'Logo :name',
    ],

    'tenant_menu' => [

        'search_field' => [
            'label' => 'Pencarian tenant',
            'placeholder' => 'Cari',
        ],

    ],
];
