<?php
// Complete PDF Generator with TCPDF
// Make sure TCPDF is installed: composer require tecnickcom/tcpdf

// Custom PDF class with Persian support
class PersianPDF extends TCPDF {
    private $fontname;

    public function __construct() {
        parent::__construct();
        // Static font (Vazir)
        $this->fontname = TCPDF_FONTS::addTTFfont(__DIR__.'/assets/Vazirmatn-Regular.ttf', 'TrueTypeUnicode', '', 96);
    }
    public function Header() {
        global $config;

        // Set font for header
        $this->SetFont('vazirmatn', 'B', 16);
        
        // Title
        $this->Cell(0, 15, sanitize($config['site_title']), 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(10);
        $this->SetFont('vazirmatn', 'B', 10);
        $this->Cell(0, 15, sanitize($config['site_description']), 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(10);
        
        // Line separator
        $this->SetLineWidth(0.2);
        $this->Line(10, 25, $this->getPageWidth() - 10, 25);
    }
    
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        
        // Set font
        $this->SetFont('vazirmatn', 'I', 8);
        
        // Page number
        $pageText = 'صفحه ' . $this->getAliasNumPage() . ' از ' . $this->getAliasNbPages();
        $this->Cell(0, 10, $pageText, 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
    
    // Method to add Persian text with proper formatting
    public function AddPersianText($text, $fontSize = 12, $fontStyle = '', $align = 'J') {
        $this->SetFont('vazirmatn', $fontStyle, $fontSize);
        $this->writeHTML($text, true, false, true, false, $align);
    }
}

function generateAdvancedPDF() {
    global $config;

    // Category names in Persian
    $categoryNames = $config['categories'];
    
    try {
        // Create new PDF document
        $pdf = new PersianPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('Information Platform');
        $pdf->SetAuthor($config['author']);
        $pdf->SetTitle('مجموعه کامل مطالب - ' . date('Y/m/d'));
        $pdf->SetSubject($config['site_description']);
        $pdf->SetKeywords(implode(',', $config['categories']));

        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set RTL (Right-to-Left) for Persian
        $pdf->setRTL(true);

        // Load all posts
        $posts = loadPosts();
        
        if (empty($posts)) {
            // If no posts found, create a simple message
            $pdf->AddPage();
            $pdf->SetFont('vazirmatn', 'B', 16);
            $pdf->Cell(0, 10, 'هیچ مطلبی یافت نشد', 0, 1, 'C');
            $pdf->Ln(10);
            $pdf->SetFont('vazirmatn', '', 12);
            $pdf->Cell(0, 10, 'لطفاً ابتدا مطالب را در پوشه data/posts اضافه کنید', 0, 1, 'C');
        } else {
            // Add cover page
            $pdf->AddPage();
            $pdf->SetFont('vazirmatn', 'B', 24);
            $pdf->Ln(50);
            $pdf->Cell(0, 20, 'مجموعه کامل مطالب', 0, 1, 'C');
            $pdf->Ln(10);
            $pdf->SetFont('vazirmatn', '', 16);
            $pdf->Cell(0, 15, 'پلتفرم اطلاع‌رسانی', 0, 1, 'C');
            $pdf->Ln(20);
            $pdf->SetFont('vazirmatn', '', 12);
            $pdf->Cell(0, 10, 'تاریخ تولید: ' . date('Y/m/d H:i'), 0, 1, 'C');
            $pdf->Cell(0, 10, 'تعداد مطالب: ' . count($posts), 0, 1, 'C');
            
            // Add table of contents
            $pdf->AddPage();
            $pdf->SetFont('vazirmatn', 'B', 18);
            $pdf->Cell(0, 15, 'فهرست مطالب', 0, 1, 'C');
            $pdf->Ln(10);
            
            // Count posts per category
            $categoryCount = [];
            foreach ($posts as $post) {
                $cat = $post['category'];
                if (!isset($categoryCount[$cat])) {
                    $categoryCount[$cat] = 0;
                }
                $categoryCount[$cat]++;
            }
            
            // Display category counts
            $pdf->SetFont('vazirmatn', '', 12);
            foreach ($categoryNames as $catKey => $catName) {
                if (isset($categoryCount[$catKey])) {
                    $count = $categoryCount[$catKey];
                    $pdf->Cell(0, 8, "• $catName ($count مطلب)", 0, 1, 'R');
                }
            }
            
            // Add posts by category
            foreach ($categoryNames as $catKey => $catName) {
                $categoryPosts = array_filter($posts, function($post) use ($catKey) {
                    return $post['category'] === $catKey;
                });
                
                if (empty($categoryPosts)) {
                    continue;
                }
                
                // Category title page
                $pdf->AddPage();
                $pdf->SetFont('vazirmatn', 'B', 20);
                $pdf->Ln(30);
                
                // Category title with background
                $pdf->SetFillColor(44, 62, 80); // Dark blue background
                $pdf->SetTextColor(255, 255, 255); // White text
                $pdf->Cell(0, 15, $catName, 0, 1, 'C', true);
                $pdf->Ln(10);
                
                // Reset text color
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('vazirmatn', '', 12);
                $pdf->Cell(0, 8, 'تعداد مطالب در این دسته: ' . count($categoryPosts), 0, 1, 'C');
                
                // Add each post in this category
                foreach ($categoryPosts as $index => $post) {
                    $pdf->AddPage();
                    
                    // Post title
                    $pdf->SetFont('vazirmatn', 'B', 16);
                    $pdf->SetFillColor(236, 240, 241); // Light gray background
                    $pdf->Cell(0, 12, $post['title'], 0, 1, 'R', true);
                    $pdf->Ln(5);
                    
                    // Post metadata
                    $pdf->SetFont('vazirmatn', '', 10);
                    $pdf->SetTextColor(127, 140, 141); // Gray text
                    
                    $metaText = 'تاریخ: ' . $post['date'];
                    if (!empty($post['author'])) {
                        $metaText .= ' | نویسنده: ' . $post['author'];
                    }
                    if (!empty($post['tags'])) {
                        $metaText .= ' | برچسب‌ها: ' . implode(', ', $post['tags']);
                    }
                    
                    $pdf->Cell(0, 6, $metaText, 0, 1, 'R');
                    $pdf->Ln(3);
                    
                    // Separator line
                    $pdf->SetLineWidth(0.1);
                    $pdf->SetDrawColor(189, 195, 199);
                    $pdf->Line(10, $pdf->GetY(), $pdf->getPageWidth() - 10, $pdf->GetY());
                    $pdf->Ln(8);
                    
                    // Post content
                    $pdf->SetTextColor(0, 0, 0); // Black text
                    $pdf->SetFont('vazirmatn', '', 11);
                    
                    // Format content for better display
                    $content = $post['content'];
                    $content = str_replace("\n\n", "</p><p>", $content);
                    $content = str_replace("\n", "<br>", $content);
                    $content = "<p>" . $content . "</p>";
                    
                    // Add content using writeHTML for better formatting
                    $pdf->writeHTML($content, true, false, true, false, 'J');
                    
                    $pdf->Ln(10);
                    
                    // Post separator (except for last post)
                    if ($index < count($categoryPosts) - 1) {
                        $pdf->SetLineWidth(0.5);
                        $pdf->SetDrawColor(52, 73, 94);
                        $pdf->Line(30, $pdf->GetY(), $pdf->getPageWidth() - 30, $pdf->GetY());
                    }
                }
            }
            
            // Add summary page
            $pdf->AddPage();
            $pdf->SetFont('vazirmatn', 'B', 18);
            $pdf->Cell(0, 15, 'خلاصه و آمار', 0, 1, 'C');
            $pdf->Ln(10);
            
            $pdf->SetFont('vazirmatn', '', 12);
            $pdf->Cell(0, 8, 'تعداد کل مطالب: ' . count($posts), 0, 1, 'R');
            $pdf->Cell(0, 8, 'تعداد دسته‌بندی‌ها: ' . count($categoryCount), 0, 1, 'R');
            $pdf->Cell(0, 8, 'تاریخ تولید فایل: ' . date('Y/m/d H:i:s'), 0, 1, 'R');
            $pdf->Ln(10);
            
            $pdf->Cell(0, 8, 'این پلتفرم برای اشتراک اطلاعات مهم و آگاه‌سازی مردم ایجاد شده است.', 0, 1, 'C');
            $pdf->Cell(0, 8, 'می‌توانید این فایل را آزادانه توزیع کنید.', 0, 1, 'C');
        }
        
        // Output PDF
        $filename = 'site-content-' . date('Y-m-d-H-i') . '.pdf';
        $pdf->Output($filename, 'D'); // 'D' for download, 'I' for inline display
        
    } catch (Exception $e) {
        // Handle errors
        header('Content-Type: text/html; charset=utf-8');
        echo "<h1>خطا در تولید PDF</h1>";
        echo "<p>پیغام خطا: " . $e->getMessage() . "</p>";
        echo "<p>لطفاً مطمئن شوید که TCPDF به درستی نصب شده است.</p>";
        echo "<p><a href='index.php'>بازگشت به سایت</a></p>";
    }
}