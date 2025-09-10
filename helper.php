<?php
include_once __DIR__ . "/tools/jdf.php";
include_once __DIR__ . "/tools/generate_pdf.php";

// Helper functions

// Load Many Posts
function loadPosts($category = null): array
{
    $posts = [];
    $dataDir = 'data/posts/';

    if ($category && is_dir($dataDir . $category)) {
        $files = glob($dataDir . $category . '/*.json');
    } else {
        $files = glob($dataDir . '*/*.json');
    }

    foreach ($files as $file) {
        $data = json_decode(file_get_contents($file), true);
        if ($data) {
            $data['file'] = basename($file, '.json');
            $data['category'] = basename(dirname($file));
            $posts[] = $data;
        }
    }

    // Sort by date (newest first)
    usort($posts, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });

    return $posts;
}

// Load One Post
function loadPost($category, $id) {
    $file = "data/posts/$category/$id.json";
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        $data['file'] = $id;
        $data['category'] = $category;
        return $data;
    }
    return null;
}

function sanitize($text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function sanitize_date($date, $text=False): string
{
    $data = sanitize($date);
    if (preg_match('/^(\d{4}|\d{2})-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])$/u', $date, $matches)) {
        $year  = (int)$matches[1];
        $month = (int)$matches[2];
        $day   = (int)$matches[3];

        // jalali
        if ($year >= 1300 && $year <= 1499) {
            if ($text) {
                return jdate('d F Y', jmktime(0,0,0, $month, $day, $year));
            }
            return sprintf("%04d-%02d-%02d", $year, $month, $day);
        }
        list($jy, $jm, $jd) = gregorian_to_jalali($year, $month, $day);
        if ($text) {
            return jdate('d F Y', jmktime(0,0,0, $jm, $jd, $jy));
        }
        return sprintf("%04d-%02d-%02d", $jy, $jm, $jd);
    }
    return '';
}

// Download PDF
if (isset($_GET['pdf'])) {
    // header('Content-Type: application/pdf');
    // header('Content-Disposition: attachment; filename="site-content.pdf"');
    generateAdvancedPDF();
    exit;
}