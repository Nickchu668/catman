<?php
// 設置時區
date_default_timezone_set('Asia/Taipei');

// 檢查是否為POST請求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// 獲取表單數據
$workAnswer = isset($_POST['work_answer']) ? trim($_POST['work_answer']) : '';

// 驗證數據
if (empty($workAnswer)) {
    header('Location: index.php?error=empty');
    exit;
}

// 限制字數
if (mb_strlen($workAnswer, 'UTF-8') > 500) {
    header('Location: index.php?error=too_long');
    exit;
}

// 清理數據，移除危險字符
$workAnswer = htmlspecialchars($workAnswer, ENT_QUOTES, 'UTF-8');
$workAnswer = str_replace(["\r\n", "\r", "\n"], " ", $workAnswer); // 將換行符替換為空格

// 獲取當前日期
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

// 創建data目錄（如果不存在）
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// 創建以日期命名的文件
$filename = $dataDir . '/' . $currentDate . '.txt';

// 準備要寫入的數據
$dataLine = $currentTime . '|' . $workAnswer . PHP_EOL;

// 寫入文件
if (file_put_contents($filename, $dataLine, FILE_APPEND | LOCK_EX) !== false) {
    // 成功寫入，重定向到統計頁面
    header('Location: stats.php?date=' . $currentDate);
    exit;
} else {
    // 寫入失敗
    header('Location: index.php?error=save_failed');
    exit;
}
?>

