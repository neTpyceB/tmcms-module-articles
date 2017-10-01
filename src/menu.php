<?php

use TMCms\Modules\Settings\ModuleSettings;

$module_articles_menu = [
    'title' => 'Articles',
    'icon' => 'feed',
    'items' => [
        '_default' => [
            'title' => 'Main',
        ],
        'categories' => [
            'title' => 'Categories',
        ],
    ]
];

if (ModuleSettings::getCustomSettingValue('articles', 'enabled_tags')) {
    $module_articles_menu['items']['tags'] = [
        'title' => 'Tags',
    ];
}

$module_articles_menu['items']['settings'] = [
    'title' => 'Settings',
];

return $module_articles_menu;