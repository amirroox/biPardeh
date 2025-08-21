<?php

// Configuration
$config = [
    'site_title' => 'بی پرده',
    'site_description' => 'اطلاع رسانی و آگاه سازی مردم برای کشوری آزاد و زیبا',
    'categories' => [
        'corruption' => 'فساد و اختلاس ها',
        'mismanagement' => 'سو مدیریت ها',
        'solutions' => 'راهکارها و راه‌حل‌ها',
        'education' => 'اطلاعات آموزشی',
        'news' => 'اخبار مهم'
    ]
];

// Get current page and category
$page = $_GET['page'] ?? 'home';
$category = $_GET['cat'] ?? null;
$post_id = $_GET['id'] ?? null;