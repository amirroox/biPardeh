<?php

// Configuration
$config = [
    'site_title' => 'بی پرده',
    'site_description' => 'اطلاع رسانی و آگاه سازی مردم برای ساخت کشوری آزاد و زیبا',
    'categories' => [
        #  mkdir data/post/...
        'corruption' => 'فساد و اختلاس ها',
        'mismanagement' => 'سو مدیریت ها',
        'solutions' => 'راهکار ها و راه حل ها',
        'education' => 'اطلاعات آموزشی',
        'news' => 'اخبار مهم'
    ]
];

// Get current page and category
$page = $_GET['page'] ?? 'home';
$category = $_GET['cat'] ?? null;
$post_id = $_GET['id'] ?? null;