<?php

// Helper functions
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

// TODO Handle PDF generation
if (isset($_GET['pdf'])) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="site-content.pdf"');

    // Simple PDF generation (you'd need a library like TCPDF for better results)
    echo "PDF generation would go here - need TCPDF library";
    exit;
}