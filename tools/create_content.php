<?php
// Content Creator Tool - ابزار ایجاد محتوای خودکار
// Use: php create_content.php

echo "=== Content Creator Tool ===\n\n";

// Check if data directory exists
$dataDir = '../data/posts';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
    echo "Data/posts folder created.\n";
}

// Categories
$categories = [
    'corruption' => 'corruption',
    'mismanagement' => 'mismanagement', 
    'solutions' => 'solutions',
    'education' => 'education',
    'news' => 'news'
];

// Create category directories
foreach ($categories as $catKey => $catName) {
    $catDir = "$dataDir/$catKey";
    if (!is_dir($catDir)) {
        mkdir($catDir, 0755, true);
        echo "Folder $catName created.\n";
    }
}

// Interactive content creation
function createPost(): bool
{
    global $categories, $dataDir;
    
    echo "\n--- Create new post ---\n";

    // Select category
    echo "Select category:\n";
    $i = 1;
    foreach ($categories as $key => $name) {
        echo "$i. $name ($key)\n";
        $i++;
    }
    
    echo "Enter category number: ";
    $categoryNum = (int)trim(fgets(STDIN));
    $categoryKeys = array_keys($categories);
    
    if ($categoryNum < 1 || $categoryNum > count($categoryKeys)) {
        echo "Invalid number!\n";
        return false;
    }
    
    $selectedCategory = $categoryKeys[$categoryNum - 1];
    
    // Get post details
    echo "Post Title: ";
    $title = trim(fgets(STDIN));

    echo "Author Name (optional): ";
    $author = trim(fgets(STDIN));

    echo "Tags (separate with commas): ";
    $tagsInput = trim(fgets(STDIN));
    $tags = $tagsInput ? array_map('trim', explode(',', $tagsInput)) : [];

    echo "Post Text (type '###' to end a newline):\n";
    $content = '';
    while (true) {
        $line = trim(fgets(STDIN));
        if ($line === '###') break;
        $content .= $line . "\n";
    }
    
    // Generate filename
    $filename = generateFilename($title);
    
    // Create post array
    $post = [
        'title' => $title,
        'content' => trim($content),
        'date' => date('Y-m-d'),
        'author' => $author ?: 'ناشناس',
        'tags' => $tags,
        'category' => $selectedCategory,
        'status' => 'published'
    ];
    
    // Save to file
    $filePath = "$dataDir/$selectedCategory/$filename.json";
    if (file_put_contents($filePath, json_encode($post, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        echo "\n✅ Post created successfully: $filePath\n";
        return true;
    } else {
        echo "\n❌ Error creating post!\n";
        return false;
    }
}

function generateFilename($title) {
    // Convert Persian/Arabic characters to English
    $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    $english = ['0','1','2','3','4','5','6','7','8','9'];
    
    $title = str_replace($persian, $english, $title);
    
    // Remove special characters and replace spaces
    $filename = preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
    $filename = preg_replace('/\s+/', '-', trim($filename));
    $filename = strtolower($filename);
    
    // If filename is empty or too short, generate based on date
    if (strlen($filename) < 3) {
        $filename = 'post-' . date('Y-m-d-H-i-s');
    }
    
    return substr($filename, 0, 50); // Limit length
}

function createSampleContent() {
    global $dataDir;
    
    $samplePosts = [
        [
            'category' => 'corruption',
            'filename' => 'sample-corruption-1',
            'data' => [
                'title' => 'اختلاس در پروژه آزادراه',
                'content' => "بر اساس اسناد به دست آمده، پروژه آزادراه که با بودجه 500 میلیارد تومان آغاز شد، تاکنون بیش از 800 میلیارد تومان هزینه داشته اما تنها 30% پیشرفت داشته است.\n\nنکات مهم:\n- قراردادهای مشکوک با شرکت‌های وابسته\n- افزایش غیرمنطقی قیمت‌ها در مناقصات\n- عدم نظارت کافی بر اجرای پروژه\n\nاین پروژه که قرار بود در 2 سال تمام شود، اکنون 4 سال از شروع آن گذشته و هنوز ناتمام است.",
                'date' => '2024-03-10',
                'author' => 'گروه تحقیق',
                'tags' => ['اختلاس', 'آزادراه', 'مناقصه'],
                'category' => 'corruption',
                'status' => 'published'
            ]
        ],
        [
            'category' => 'mismanagement',
            'filename' => 'sample-management-1',
            'data' => [
                'title' => 'سوءمدیریت در توزیع دارو',
                'content' => "سیستم توزیع دارو در کشور با مشکلات جدی روبرو است که ریشه در سوءمدیریت دارد.\n\nمشکلات اصلی:\n1. عدم برنامه‌ریزی صحیح برای واردات\n2. انحصار در توزیع\n3. قیمت‌گذاری غیرمنطقی\n4. کمبود داروهای حیاتی\n\nاین مسائل موجب رنج مردم و افزایش هزینه‌های درمان شده است.",
                'date' => '2024-03-08',
                'author' => 'کارشناس بهداشت',
                'tags' => ['دارو', 'بهداشت', 'مدیریت'],
                'category' => 'mismanagement',
                'status' => 'published'
            ]
        ],
        [
            'category' => 'solutions',
            'filename' => 'sample-solution-1',
            'data' => [
                'title' => 'راهکارهای مقابله با فساد اداری',
                'content' => "برای مقابله مؤثر با فساد اداری راهکارهای زیر پیشنهاد می‌شود:\n\n1. شفافیت در فرآیندها\n- انتشار عمومی قراردادها\n- گزارش‌دهی منظم عملکرد\n- دسترسی آزاد به اطلاعات\n\n2. نظارت مردمی\n- تشکیل کمیته‌های نظارتی\n- سامانه گزارش تخلفات\n- حمایت از افشاگران\n\n3. اصلاحات ساختاری\n- کاهش بوروکراسی\n- دیجیتال‌سازی خدمات\n- تقویت سیستم قضایی",
                'date' => '2024-03-05',
                'author' => 'متخصص حکمرانی',
                'tags' => ['راهکار', 'شفافیت', 'نظارت'],
                'category' => 'solutions',
                'status' => 'published'
            ]
        ],
        [
            'category' => 'education',
            'filename' => 'sample-education-1',
            'data' => [
                'title' => 'حقوق شهروندی و آگاهی از آن',
                'content' => "هر شهروند حقوق اساسی دارد که باید از آنها آگاه باشد:\n\n🔹 حق دسترسی به اطلاعات\nشما حق دارید از نحوه هزینه بودجه عمومی آگاه باشید.\n\n🔹 حق شکایت و اعتراض\nمی‌توانید از عملکرد مسئولان شکایت کنید.\n\n🔹 حق نظارت\nنظارت بر عملکرد نهادهای عمومی حق همه شهروندان است.\n\n🔹 حق مشارکت\nدر تصمیمات مربوط به محله و شهر حق مشارکت دارید.\n\nآگاهی از این حقوق اولین قدم برای مطالبه آنهاست.",
                'date' => '2024-03-03',
                'author' => 'حقوقدان',
                'tags' => ['حقوق شهروندی', 'آگاهی', 'قانون'],
                'category' => 'education',
                'status' => 'published'
            ]
        ],
        [
            'category' => 'news',
            'filename' => 'sample-news-1',
            'data' => [
                'title' => 'حقوق شهروندی و آگاهی از آن',
                'content' => "هر شهروند حقوق اساسی دارد که باید از آنها آگاه باشد:\n\n🔹 حق دسترسی به اطلاعات\nشما حق دارید از نحوه هزینه بودجه عمومی آگاه باشید.\n\n🔹 حق شکایت و اعتراض\nمی‌توانید از عملکرد مسئولان شکایت کنید.\n\n🔹 حق نظارت\nنظارت بر عملکرد نهادهای عمومی حق همه شهروندان است.\n\n🔹 حق مشارکت\nدر تصمیمات مربوط به محله و شهر حق مشارکت دارید.\n\nآگاهی از این حقوق اولین قدم برای مطالبه آنهاست.",
                'date' => '2024-03-03',
                'author' => 'حقوقدان',
                'tags' => ['حقوق شهروندی', 'آگاهی', 'قانون'],
                'category' => 'education',
                'status' => 'published'
            ]
        ]
    ];
    
    echo "\nCreating sample content...\n";
    
    foreach ($samplePosts as $post) {
        $filePath = "$dataDir/{$post['category']}/{$post['filename']}.json";
        
        if (!file_exists($filePath)) { 
            if (file_put_contents($filePath, json_encode($post['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) { 
                echo " ✅ {$post['data']['title']}\n"; 
            } else { 
                echo "❌ Error creating: {$post['data']['title']}\n"; 
            } 
        } 
        else { 
                echo "⚠️ available: {$post['data']['title']}\n"; 
        }
    }
}

function showMenu() {
    echo "\n=== Main Menu ===\n";
    echo "1. Create a new post\n";
    echo "2. Create sample content\n";
    echo "3. View statistics\n";
    echo "4. Exit\n";
    echo "Select (1-4): ";
}

function showStats() {
    global $categories, $dataDir;
    
    echo "\n=== Content Statistics ===\n";

    $totalPosts = 0;
    foreach ($categories as $catKey => $catName) {
        $catDir = "$dataDir/$catKey";
        $count = 0;

        if (is_dir($catDir)) {
            $files = glob("$catDir/*.json");
            $count = count($files);
        }

        echo "$catName: $count posts\n";
        $totalPosts += $count;
    }

    echo "\nTotal Posts: $totalPosts\n";

    // Show recent posts
    echo "\nRecent Posts:\n";
    $allFiles = glob("$dataDir/*/*.json");
    
    // Sort by modification time
    usort($allFiles, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    
    $recentFiles = array_slice($allFiles, 0, 5);
    
    foreach ($recentFiles as $file) {
        $data = json_decode(file_get_contents($file), true);
        if ($data) {
            $category = basename(dirname($file));
            echo "- {$data['title']} ({$categories[$category]} - {$data['date']})\n";
        }
    }
}

// Main execution
while (true) {
    showMenu();
    $choice = trim(fgets(STDIN));
    
    switch ($choice) {
        case '1':
            createPost();
            break;
            
        case '2':
            createSampleContent();
            break;
            
        case '3':
            showStats();
            break;
            
        case '4':
            echo "bye bye!\n";
            exit(0);
            
        default:
            echo "Invalid selection! Please select one of options 1 to 4.\n";
    }
    
    echo "\nPress Enter to continue...";
    fgets(STDIN);
}