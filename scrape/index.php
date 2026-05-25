<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram Comment Scraper - Ashwini Reloaded</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        .scraper-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .comments-list {
            margin-top: 30px;
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: #ffffff;
        }
        .comment-item {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        .comment-item:last-child {
            border-bottom: none;
        }
        .comment-user {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 5px;
        }
        .comment-text {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .loader {
            display: none;
            text-align: center;
            margin: 20px 0;
            font-weight: 600;
            color: var(--text-secondary);
        }
        .error-box {
            display: none;
            background: #fee2e2;
            color: #b91c1c;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .info-alert {
            background: #e0f2fe;
            color: #0369a1;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="scraper-container">
        <!-- Logo -->
        <div class="logo-container" style="margin-top:0;">
            <img src="../assets/images/logo_Black.png" alt="Logo" class="main-logo">
        </div>

        <h1 class="serif-heading" style="font-size: 2.5rem; text-align: center;">Comment Scraper</h1>
        <p class="subtitle" style="text-align: center;">Extract comments from a public Instagram post.</p>
        
        <div class="info-alert">
            <strong>Note:</strong> Instagram restricts automated scraping. This tool attempts to fetch comments anonymously. If a post is heavily rate-limited, private, or has too many comments, Instagram may block the request.
        </div>

        <div class="form-fields" style="max-width: 100%; margin-top: 20px;">
            <div class="input-group">
                <label for="igUrl">Instagram Post URL</label>
                <input type="url" id="igUrl" value="https://www.instagram.com/p/DYrPZYtocRC/?utm_source=ig_web_copy_link&igsh=NTc4MTIwNjQ2YQ==" placeholder="https://www.instagram.com/p/...">
            </div>
            
            <button id="scrapeBtn" class="btn btn-primary" style="width: 100%;">Scrape Comments</button>
        </div>

        <div class="loader" id="loader">Scraping in progress... this might take a minute ⏳</div>
        <div class="error-box" id="errorBox"></div>

        <div class="comments-list" id="commentsList" style="display: none;">
            <!-- Comments will be injected here -->
        </div>
    </div>

    <script>
        document.getElementById('scrapeBtn').addEventListener('click', async () => {
            const url = document.getElementById('igUrl').value;
            const loader = document.getElementById('loader');
            const errorBox = document.getElementById('errorBox');
            const commentsList = document.getElementById('commentsList');
            const btn = document.getElementById('scrapeBtn');

            if (!url) return alert('Please enter a URL');

            btn.disabled = true;
            loader.style.display = 'block';
            errorBox.style.display = 'none';
            commentsList.style.display = 'none';
            commentsList.innerHTML = '';

            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ url })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    if (data.comments.length === 0) {
                        commentsList.innerHTML = '<div style="padding:20px;text-align:center;">No comments found or post is private.</div>';
                    } else {
                        data.comments.forEach(c => {
                            const item = document.createElement('div');
                            item.className = 'comment-item';
                            item.innerHTML = `<div class="comment-user">@${c.username}</div><div class="comment-text">${c.text}</div>`;
                            commentsList.appendChild(item);
                        });
                    }
                    commentsList.style.display = 'block';
                } else {
                    errorBox.innerText = 'Error: ' + data.message;
                    errorBox.style.display = 'block';
                }
            } catch (err) {
                errorBox.innerText = 'Request failed. See console.';
                errorBox.style.display = 'block';
                console.error(err);
            } finally {
                loader.style.display = 'none';
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>
