<?php
require_once 'includes/functions.php';

// 設置時區
date_default_timezone_set('Asia/Taipei');

// 獲取選擇的日期
$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// 驗證日期格式
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
    $selectedDate = date('Y-m-d');
}

// 獲取數據
$surveyData = getSurveyData($selectedDate);
$answerStats = getAnswerStats($surveyData);
$availableDates = getAvailableDates();

// 計算最大頻率
$maxCount = 0;
foreach ($answerStats as $stat) {
    if ($stat['count'] > $maxCount) {
        $maxCount = $stat['count'];
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>工作心聲統計 - <?php echo $selectedDate; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        /* 統計頁面專用樣式 */
        .stats-container {
            min-height: 100vh;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stats-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .stats-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .stats-subtitle {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.9);
            margin-bottom: 20px;
        }
        
        .date-selector {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 15px 20px;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .date-selector select {
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 10px;
            padding: 10px 15px;
            font-size: 1rem;
            font-family: inherit;
            cursor: pointer;
        }
        
        .stats-info {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 15px 20px;
            color: white;
            display: inline-block;
            margin-left: 20px;
        }
        
        .bubbles-container {
            position: relative;
            min-height: 600px;
            max-width: 1200px;
            margin: 0 auto;
            overflow: hidden;
        }
        
        .bubble {
            position: absolute;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            animation: bubbleFloat 6s ease-in-out infinite;
            word-wrap: break-word;
            padding: 10px;
            box-sizing: border-box;
        }
        
        .bubble:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        .bubble-text {
            font-size: 0.9rem;
            line-height: 1.2;
            max-width: 90%;
        }
        
        .bubble-count {
            position: absolute;
            top: -10px;
            right: -10px;
            background: rgba(255,255,255,0.9);
            color: #333;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        @keyframes bubbleFloat {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            33% {
                transform: translateY(-10px) rotate(2deg);
            }
            66% {
                transform: translateY(5px) rotate(-1deg);
            }
        }
        
        .no-data {
            text-align: center;
            color: white;
            font-size: 1.2rem;
            margin-top: 100px;
        }
        
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 20px;
            text-decoration: none;
            font-family: inherit;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .stats-title {
                font-size: 2rem;
            }
            
            .date-selector {
                margin-bottom: 10px;
            }
            
            .stats-info {
                margin-left: 0;
                margin-top: 10px;
            }
            
            .bubble-text {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="stats-container">
        <a href="index.php" class="back-btn">← 返回首頁</a>
        
        <div class="stats-header">
            <h1 class="stats-title">工作心聲統計</h1>
            <p class="stats-subtitle">看看大家都在想什麼</p>
            
            <div class="date-selector">
                <select id="dateSelect" onchange="changeDate()">
                    <?php foreach ($availableDates as $date): ?>
                        <option value="<?php echo $date; ?>" <?php echo $date === $selectedDate ? 'selected' : ''; ?>>
                            <?php echo $date; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="stats-info">
                共收到 <?php echo count($surveyData); ?> 份回應
            </div>
        </div>
        
        <div class="bubbles-container" id="bubblesContainer">
            <?php if (empty($answerStats)): ?>
                <div class="no-data">
                    這一天還沒有收到任何回應
                </div>
            <?php else: ?>
                <?php $index = 0; ?>
                <?php foreach ($answerStats as $answer => $stat): ?>
                    <?php
                    $size = calculateBubbleSize($stat['count'], $maxCount);
                    $color = generateBubbleColor($index);
                    $left = rand(5, 85);
                    $top = rand(10, 70);
                    $animationDelay = rand(0, 5);
                    ?>
                    <div class="bubble" 
                         style="width: <?php echo $size; ?>px; 
                                height: <?php echo $size; ?>px; 
                                background: <?php echo $color; ?>; 
                                left: <?php echo $left; ?>%; 
                                top: <?php echo $top; ?>%;
                                animation-delay: <?php echo $animationDelay; ?>s;">
                        <div class="bubble-text"><?php echo htmlspecialchars($stat['original']); ?></div>
                        <div class="bubble-count"><?php echo $stat['count']; ?></div>
                    </div>
                    <?php $index++; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function changeDate() {
            const select = document.getElementById('dateSelect');
            const selectedDate = select.value;
            window.location.href = 'stats.php?date=' + selectedDate;
        }
        
        // 防止泡泡重疊的簡單邏輯
        document.addEventListener('DOMContentLoaded', function() {
            const bubbles = document.querySelectorAll('.bubble');
            const container = document.getElementById('bubblesContainer');
            
            // 重新定位重疊的泡泡
            bubbles.forEach((bubble, index) => {
                setTimeout(() => {
                    repositionBubble(bubble, bubbles);
                }, index * 100);
            });
        });
        
        function repositionBubble(bubble, allBubbles) {
            const containerRect = bubble.parentElement.getBoundingClientRect();
            const bubbleRect = bubble.getBoundingClientRect();
            
            let attempts = 0;
            let overlapping = true;
            
            while (overlapping && attempts < 10) {
                overlapping = false;
                
                for (let other of allBubbles) {
                    if (other === bubble) continue;
                    
                    const otherRect = other.getBoundingClientRect();
                    const distance = Math.sqrt(
                        Math.pow(bubbleRect.left - otherRect.left, 2) + 
                        Math.pow(bubbleRect.top - otherRect.top, 2)
                    );
                    
                    const minDistance = (bubbleRect.width + otherRect.width) / 2 + 20;
                    
                    if (distance < minDistance) {
                        overlapping = true;
                        break;
                    }
                }
                
                if (overlapping) {
                    const newLeft = Math.random() * 80 + 5;
                    const newTop = Math.random() * 60 + 10;
                    bubble.style.left = newLeft + '%';
                    bubble.style.top = newTop + '%';
                    attempts++;
                }
            }
        }
    </script>
</body>
</html>

