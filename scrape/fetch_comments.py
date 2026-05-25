import sys
import json
import instaloader

def get_shortcode(url):
    try:
        parts = url.split('/')
        if 'p' in parts:
            idx = parts.index('p')
            return parts[idx+1]
        elif 'reel' in parts:
            idx = parts.index('reel')
            return parts[idx+1]
    except Exception:
        pass
    
    # Try to clean query params if they exist in the shortcode
    try:
        shortcode = [p for p in parts if p][-1]
        if '?' in shortcode:
            shortcode = shortcode.split('?')[0]
        return shortcode
    except:
        pass
        
    return None

def main():
    if len(sys.argv) < 2:
        print(json.dumps({"error": "No URL provided"}))
        return

    url = sys.argv[1]
    
    try:
        parts = url.split('/')
        shortcode = None
        for i, part in enumerate(parts):
            if part in ['p', 'reel'] and i + 1 < len(parts):
                shortcode = parts[i+1]
                break
        
        if shortcode and '?' in shortcode:
            shortcode = shortcode.split('?')[0]
            
    except Exception as e:
        shortcode = None
    
    if not shortcode:
        print(json.dumps({"error": "Invalid Instagram URL format"}))
        return

    L = instaloader.Instaloader()
    
    try:
        post = instaloader.Post.from_shortcode(L.context, shortcode)
        comments_data = []
        for comment in post.get_comments():
            comments_data.append({
                "username": comment.owner.username,
                "text": comment.text
            })
            if len(comments_data) >= 100:
                break
                
        print(json.dumps({"success": True, "comments": comments_data}))
    except Exception as e:
        print(json.dumps({"error": str(e)}))

if __name__ == "__main__":
    main()
