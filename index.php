<?php
// Simple Information Website - Main File (index.php)
// Hoping for a happy and prosperous Iran :)
/**
 * @var array{
 *     site_title: string,
 *     site_description: string
 * } $config
 */
/** @var string $page  */
/** @var string $category  */
/** @var string $post_id  */
include_once "config.php";
include_once "helper.php";
?>


<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($config['site_title']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lalezar&family=Vazirmatn:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/style.css">
</head>
<body>
<header>
    <div class="container">
        <h1><?= sanitize($config['site_title']) ?></h1>
        <p><?= sanitize($config['site_description']) ?></p>
    </div>
</header>

<!--Navbar Header-->
<nav>
    <div class="container">
        <ul>
            <li><a href="index.php" class="active-home" >صفحه اصلی</a></li>
            <?php foreach ($config['categories'] as $cat_key => $cat_name): ?>
                <li><a href="index.php?cat=<?= $cat_key ?>" <?= $category == $cat_key ? 'class="active"' : '' ?>><?= sanitize($cat_name) ?></a></li>
            <?php endforeach; ?>
            <li><a href="index.php?pdf=1" class="pdf-download">دانلود PDF کل سایت</a></li>
        </ul>
    </div>
</nav>

<!--Main Content-->
<main>
    <div class="container">
        <?php if ($post_id && $category): ?>
            <!-- Single Post View -->
            <?php $post = loadPost($category, $post_id); ?>
            <?php if ($post and $post['status'] == 'published'): ?>
                <a href="index.php?cat=<?= $category ?>" class="back-link">← بازگشت به <?= sanitize($config['categories'][$category]) ?></a>
                <div class="single-post">
                    <h1><?= sanitize($post['title']) ?></h1>
                    <div class="post-meta">
                        <span class="category-badge"><?= sanitize($config['categories'][$category]) ?></span>
                        <span class="tags-badge">تگ ها : <?= implode(', ', $post['tags']) ?> </span>
                        <div style="margin-top: 10px;direction: ltr">
                        تاریخ: <?= sanitize($post['date']) ?>
                        <?php if (isset($post['author'])): ?>
                            | نویسنده: <?= sanitize($post['author']) ?>
                        <?php endif; ?>
                        </div>
                    </div>
                    <div class="content">
                        <?= nl2br(sanitize($post['content'])) ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="single-post">
                    <h1>پست یافت نشد</h1>
                    <p>متاسفانه پست مورد نظر یافت نشد.</p>
                </div>
            <?php endif; ?>

        <?php elseif ($category): ?>
            <!-- Category View -->
            <?php $posts = loadPosts($category); ?>
            <h2 style="margin-bottom: 20px; color: #2c3e50;"><?= sanitize($config['categories'][$category]) ?></h2>

            <?php if (empty($posts)): ?>
                <div class="home-intro">
                    <h3>هنوز پستی در این دسته منتشر نشده</h3>
                    <p>به زودی محتوای جدید اضافه خواهد شد.</p>
                </div>
            <?php else: ?>
                <div class="posts-grid">
                    <?php foreach ($posts as $post): ?>
                        <div class="post-card">
                            <h3 class="post-title"><?= sanitize($post['title']) ?></h3>
                            <div class="post-meta">
                                <span class="category-badge"><?= sanitize($config['categories'][$post['category']]) ?></span>
                                <?= sanitize($post['date']) ?>
                            </div>
                            <div class="post-excerpt">
                                <?= sanitize(substr($post['content'], 0, 200)) ?>...
                            </div>
                            <a href="index.php?cat=<?= $post['category'] ?>&id=<?= $post['file'] ?>" class="read-more">ادامه مطلب</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Home Page -->
            <div class="home-intro">
                <h2>خوش آمدید</h2>
                <p>این پلتفرم برای اشتراک اطلاعات مهم و آگاه‌سازی مردم ایجاد شده است. محتوای سایت به صورت آزاد و رایگان در دسترس همه قرار دارد.</p>
                <p>می‌توانید از منوی بالا دسته‌بندی مورد نظرتان را انتخاب کنید یا کل محتوای سایت را به صورت PDF دانلود کنید.</p>
            </div>

            <h2 style="color: #2c3e50; margin-bottom: 20px;">آخرین مطالب</h2>

            <?php $recentPosts = array_slice(loadPosts(), 0, 6); ?>
            <?php if (empty($recentPosts)): ?>
                <div class="home-intro">
                    <h3>به زودی...</h3>
                    <p>محتوای سایت در حال آماده‌سازی است.</p>
                </div>
            <?php else: ?>
                <div class="posts-grid">
                    <?php foreach ($recentPosts as $post): ?>
                        <div class="post-card">
                            <h3 class="post-title"><?= sanitize($post['title']) ?></h3>
                            <div class="post-meta">
                                <span class="category-badge"><?= sanitize($config['categories'][$post['category']]) ?></span>
                                <?= sanitize($post['date']) ?>
                            </div>
                            <div class="post-excerpt">
                                <?= sanitize(substr($post['content'], 0, 200)) ?>...
                            </div>
                            <a href="index.php?cat=<?= $post['category'] ?>&id=<?= $post['file'] ?>" class="read-more">ادامه مطلب</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; <?= date('Y') ?> - <?= sanitize($config['site_title']) ?> | این سایت اپن‌سورس است و برای همه آزاد</p>
    </div>
</footer>
</body>
</html>