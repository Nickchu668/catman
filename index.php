<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>工作心聲調查</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="main-content">
            <h1 class="title">工作心聲</h1>
            <div class="subtitle">分享你的真實感受</div>
            
            <form id="surveyForm" action="submit.php" method="POST" class="survey-form">
                <div class="question-container">
                    <label for="workAnswer" class="question">
                        你最討厭/不願意的日常工作是什麼？
                    </label>
                    <textarea 
                        id="workAnswer" 
                        name="work_answer" 
                        placeholder="請分享你的想法..." 
                        required
                        maxlength="500"
                    ></textarea>
                    <div class="char-count">
                        <span id="charCount">0</span>/500
                    </div>
                </div>
                
                <button type="submit" class="submit-btn">
                    <span>提交</span>
                    <div class="btn-ripple"></div>
                </button>
            </form>
        </div>
        
        <div class="floating-elements">
            <div class="float-element float-1"></div>
            <div class="float-element float-2"></div>
            <div class="float-element float-3"></div>
            <div class="float-element float-4"></div>
            <div class="float-element float-5"></div>
        </div>
    </div>

    <script>
        // 字數統計
        const textarea = document.getElementById('workAnswer');
        const charCount = document.getElementById('charCount');
        
        textarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count;
            
            if (count > 450) {
                charCount.style.color = '#ff6b6b';
            } else {
                charCount.style.color = '#666';
            }
        });

        // 表單提交動畫
        document.getElementById('surveyForm').addEventListener('submit', function(e) {
            const submitBtn = document.querySelector('.submit-btn');
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<span>提交中...</span>';
        });

        // 按鈕點擊波紋效果
        document.querySelector('.submit-btn').addEventListener('click', function(e) {
            const ripple = this.querySelector('.btn-ripple');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('active');
            
            setTimeout(() => {
                ripple.classList.remove('active');
            }, 600);
        });
    </script>
</body>
</html>

