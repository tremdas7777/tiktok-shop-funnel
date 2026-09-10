#!/usr/bin/env python3
"""Mirror loja.wwtiktokshop.com funnel locally."""
import json
import os
import re
import time
import urllib.parse
import urllib.request

BASE = "https://loja.wwtiktokshop.com"
LANDING = f"{BASE}/landings/store/creamy"
OUT = os.path.dirname(os.path.abspath(__file__))
UA = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36"

downloaded = set()


def fetch(url, dest=None, binary=True):
    if url in downloaded:
        return None
    downloaded.add(url)
    try:
        req = urllib.request.Request(url, headers={"User-Agent": UA})
        with urllib.request.urlopen(req, timeout=60) as r:
            data = r.read()
        if dest:
            os.makedirs(os.path.dirname(dest), exist_ok=True)
            mode = "wb" if binary else "w"
            if binary:
                with open(dest, mode) as f:
                    f.write(data)
            else:
                with open(dest, mode, encoding="utf-8") as f:
                    f.write(data.decode("utf-8", errors="replace"))
            print(f"  OK {url} -> {dest}")
        return data
    except Exception as e:
        print(f"  FAIL {url}: {e}")
        return None


def local_path(url_path):
    url_path = url_path.split("?")[0]
    if url_path.startswith("http"):
        parsed = urllib.parse.urlparse(url_path)
        if parsed.netloc == "loja.wwtiktokshop.com":
            url_path = parsed.path
        else:
            return None
    if not url_path.startswith("/"):
        return None
    return os.path.join(OUT, url_path.lstrip("/"))


def mirror_url(url_path):
    if not url_path or url_path.startswith("data:") or url_path.startswith("javascript:"):
        return
    full = url_path if url_path.startswith("http") else BASE + url_path
    dest = local_path(url_path)
    if dest and not os.path.exists(dest):
        fetch(full, dest)


def extract_urls_from_text(text):
    patterns = [
        r'(?:src|href|url|mask-image)\s*[=:]\s*["\']([^"\']+)["\']',
        r'"(?:/uploads/[^"]+)"',
        r'"(?:/assets/[^"]+)"',
    ]
    urls = set()
    for pat in patterns:
        for m in re.finditer(pat, text):
            u = m.group(1) if m.lastindex else m.group(0).strip('"')
            if u.startswith("/") or u.startswith("http"):
                urls.add(u)
    return urls


def main():
    print("=== Mirroring landing pages ===")
    pages = [
        ("index.html", f"{LANDING}/"),
        ("cart.php", f"{LANDING}/cart.php"),
        ("checkout.php", f"{LANDING}/checkout.php"),
        ("produto.php", f"{LANDING}/produto.php?id=19668"),
        ("pix.php", f"{LANDING}/pix.php"),
        ("api.php", f"{LANDING}/api.php"),
        ("frete.php", f"{LANDING}/frete.php"),
    ]
    for name, url in pages:
        dest = os.path.join(OUT, name)
        fetch(url, dest, binary=False)

    print("\n=== Mirroring JSON/JS config ===")
    configs = [
        "# loja.json",
        "# produtos.json",
        "tiktok-config.js",
        "store-config.js",
    ]
    for c in configs:
        fetch(f"{LANDING}/{c}", os.path.join(OUT, c), binary=False)

    print("\n=== Mirroring static assets from HTML ===")
    for name in ["index.html", "cart.php", "checkout.php", "produto.php"]:
        path = os.path.join(OUT, name)
        if os.path.exists(path):
            with open(path, encoding="utf-8", errors="replace") as f:
                for u in extract_urls_from_text(f.read()):
                    mirror_url(u)

    print("\n=== Mirroring product assets ===")
    prod_path = os.path.join(OUT, "produtos.json")
    if os.path.exists(prod_path):
        with open(prod_path, encoding="utf-8") as f:
            products = json.load(f)
        asset_urls = set()
        for p in products:
            for foto in p.get("fotos") or []:
                asset_urls.add(foto)
            for v in p.get("videos") or []:
                if isinstance(v, dict):
                    asset_urls.add(v.get("url", ""))
                    asset_urls.add(v.get("avatar", ""))
            for var in p.get("variacoes") or []:
                asset_urls.add(var.get("imagem", ""))
            for c in p.get("comentarios") or []:
                asset_urls.add(c.get("foto_perfil", ""))
                fotos = c.get("fotos")
                if fotos:
                    try:
                        for fp in json.loads(fotos) if isinstance(fotos, str) else fotos:
                            asset_urls.add(fp)
                    except Exception:
                        pass
        for u in sorted(asset_urls):
            if u:
                mirror_url(u)

    print("\n=== Mirroring loja logo ===")
    loja_path = os.path.join(OUT, "loja.json")
    if os.path.exists(loja_path):
        with open(loja_path, encoding="utf-8") as f:
            loja = json.load(f)
        logo = loja.get("logo")
        if logo:
            mirror_url(f"/landings/store/creamy/{logo}")

    print("\n=== Mirroring common UI assets ===")
    ui_assets = [
        "/uploads/nav-lupa.png",
        "/uploads/nav-compartilhar.png",
        "/uploads/nav-carrinho.png",
        "/uploads/tiktok-logo-bg.png",
        "/uploads/taglojaestrela.png",
        "/uploads/preconovoicon.png",
        "/uploads/togllemodobloco.png",
        "/uploads/bilhete.png",
        "/uploads/oferta-relamapago.png",
        "/uploads/cartinha.png",
        "/uploads/carrinho-rosa.png",
        "/uploads/oficialcomtrevo.png",
        "/uploads/carrinho-de-compras (1).png",
        "/assets/img/tiktok-shop-oficial.png",
    ]
    for u in ui_assets:
        mirror_url(u)

    print(f"\n=== Done! {len(downloaded)} files fetched ===")


if __name__ == "__main__":
    main()
