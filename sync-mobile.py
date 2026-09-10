#!/usr/bin/env python3
import os, shutil, urllib.request

ROOT = "/Users/ulissescardoso/tiktok-shop-funnel"
BASE = "https://loja.wwtiktokshop.com/landings/store/creamy"
AGENT = "/Users/ulissescardoso/.cursor/projects/Users-ulissescardoso/agent-tools"
UA = "Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)"

def copy(src, dst):
    os.makedirs(os.path.dirname(dst), exist_ok=True)
    shutil.copy2(src, dst)
    print(f"copied {os.path.basename(dst)}")

def dl(path):
    url = f"{BASE}/{path.lstrip('/')}"
    dst = os.path.join(ROOT, path)
    if os.path.exists(dst) and os.path.getsize(dst) > 200:
        return
    os.makedirs(os.path.dirname(dst), exist_ok=True)
    req = urllib.request.Request(url, headers={"User-Agent": UA})
    with urllib.request.urlopen(req, timeout=45) as r:
        data = r.read()
    with open(dst, "wb") as f:
        f.write(data)
    print(f"downloaded {path} ({len(data)}b)")

copy(f"{AGENT}/b39fec15-b299-41c6-8ec8-3d3086b5b50c.txt", f"{ROOT}/stylesss.css")
copy(f"{AGENT}/2586368f-6d1c-4b86-8683-e51185ec540b.txt", f"{ROOT}/js/script.js")

for f in [
    "fonts/TikTokFont-Regular.woff2",
    "fonts/TikTokFont-Semibold.woff2",
    "fonts/TikTokFont-Bold.woff2",
    "fonts/TikTokDisplayFont-Regular.woff2",
    "uploads/togllemodolista.png",
    "uploads/mao-celular.png",
]:
    try:
        dl(f)
    except Exception as e:
        print(f"fail {f}: {e}")

logo = f"{ROOT}/logo.png"
webp = f"{ROOT}/logo.webp"
if os.path.exists(logo):
    shutil.copy2(logo, webp)

new_index = f"{ROOT}/index.html.new"
index = f"{ROOT}/index.html"
if os.path.exists(new_index):
    shutil.copy2(new_index, index)
    with open(index, encoding="utf-8") as f:
        html = f.read()
    if "mobile-fix.css" not in html:
        html = html.replace(
            '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />',
            '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />\n  <link rel="stylesheet" href="mobile-fix.css" />',
        )
    html = html.replace('id="loja-nome" style="font-size:16px;font-weight:600;color:#111;line-height:1.1;">Shop</span>',
                        'id="loja-nome" style="font-size:16px;font-weight:600;color:#111;line-height:1.1;">Creamy Skincare</span>')
    html = html.replace('opacity:0;transition:opacity 0.3s ease;', 'opacity:1;transition:opacity 0.3s ease;')
    with open(index, "w", encoding="utf-8") as f:
        f.write(html)
    print("updated index.html")

produto = f"{ROOT}/produto.php"
if os.path.exists(produto):
    with open(produto, encoding="utf-8") as f:
        p = f.read()
    if "mobile-fix.css" not in p:
        p = p.replace(
            '<link href="stylesss.css?v=1788753804" rel="stylesheet">',
            '<link href="stylesss.css?v=1788753804" rel="stylesheet">\n    <link href="mobile-fix.css" rel="stylesheet">',
        )
    with open(produto, "w", encoding="utf-8") as f:
        f.write(p)
    print("updated produto.php")

print("done")
