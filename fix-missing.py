#!/usr/bin/env python3
"""Baixa arquivos faltantes do funil original."""
import os
import shutil
import urllib.request

BASE = "https://loja.wwtiktokshop.com/landings/store/creamy"
ROOT = os.path.dirname(os.path.abspath(__file__))
UA = "Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)"


def fetch(path):
    url = BASE + path if path.startswith("/") else f"{BASE}/{path}"
    dest = os.path.join(ROOT, path.lstrip("/"))
    if os.path.exists(dest) and os.path.getsize(dest) > 100:
        print(f"  skip {path}")
        return
    os.makedirs(os.path.dirname(dest), exist_ok=True)
    try:
        req = urllib.request.Request(url, headers={"User-Agent": UA})
        with urllib.request.urlopen(req, timeout=60) as r:
            data = r.read()
        with open(dest, "wb") as f:
            f.write(data)
        print(f"  OK {path} ({len(data)} bytes)")
    except Exception as e:
        print(f"  FAIL {path}: {e}")


files = [
    "stylesss.css",
    "js/script.js",
    "js/cart.js",
    "uploads/togllemodolista.png",
    "uploads/mao-celular.png",
    "uploads/carrinho-de-compras (1).png",
    "payment.php",
    "checkout.php",
    "cart.php",
    "produto.php",
    "index.html",
]

print("=== Baixando arquivos faltantes ===")
for f in files:
    fetch(f)

logo = os.path.join(ROOT, "logo.png")
webp = os.path.join(ROOT, "logo.webp")
if os.path.exists(logo) and not os.path.exists(webp):
    shutil.copy(logo, webp)
    print("  OK logo.webp (copiado de logo.png)")

print("=== Concluído ===")
