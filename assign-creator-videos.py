#!/usr/bin/env python3
"""Copia videos baixados e associa a secao videos de criadores em produtos.json."""
from __future__ import annotations

import json
import os
import re
import shutil

ROOT = os.path.dirname(os.path.abspath(__file__))
UPLOADS = os.path.join(ROOT, "uploads")
PRODUTOS_PATH = os.path.join(ROOT, "produtos.json")

DOWNLOADS = [
    ("/Users/ulissescardoso/Downloads/Download.mp4", "criador_download.mp4"),
    ("/Users/ulissescardoso/Downloads/asa.mp4", "criador_asa.mp4"),
    ("/Users/ulissescardoso/Downloads/sw.mp4", "criador_sw.mp4"),
]

CREATORS = [
    {"autor": "Carla Maria", "avatar": "/uploads/avatares/avatar_001.jpg"},
    {"autor": "Nandy zorzan", "avatar": "/uploads/avatares/avatar_006.jpg"},
    {"autor": "Califórnices", "avatar": "/uploads/avatares/avatar_012.jpg"},
    {"autor": "Jose Marcos", "avatar": "/uploads/avatares/avatar_018.jpg"},
    {"autor": "Andre Arthur", "avatar": "/uploads/avatares/avatar_024.jpg"},
    {"autor": "Joyce Lima", "avatar": "/uploads/avatares/avatar_030.jpg"},
    {"autor": "Matheus Alberto", "avatar": "/uploads/avatares/avatar_036.jpg"},
    {"autor": "Juan Andrade", "avatar": "/uploads/avatares/avatar_042.jpg"},
    {"autor": "Julia e Rafael", "avatar": "/uploads/avatares/avatar_048.jpg"},
]

CRIADOR_VIDEOS = [
    "criador_download.mp4",
    "criador_asa.mp4",
    "criador_sw.mp4",
]


def is_creamy_video(name: str) -> bool:
    n = name.lower()
    return n.startswith("vcv_video_") or n.startswith("vcv_avatar_")


def exists(path: str) -> bool:
    if path.startswith("/uploads/"):
        path = os.path.join(ROOT, path.lstrip("/"))
    return os.path.isfile(path) and os.path.getsize(path) > 1000


def copy_downloads() -> None:
    os.makedirs(UPLOADS, exist_ok=True)
    for src, name in DOWNLOADS:
        if not os.path.isfile(src):
            print(f"  skip (nao encontrado): {src}")
            continue
        dest = os.path.join(UPLOADS, name)
        shutil.copy2(src, dest)
        print(f"  copiado -> {name} ({os.path.getsize(dest)} bytes)")


def avatar_pool() -> list[str]:
    avatars_dir = os.path.join(UPLOADS, "avatares")
    manifest = os.path.join(avatars_dir, "manifest.json")
    if os.path.isfile(manifest):
        with open(manifest, encoding="utf-8") as fh:
            paths = json.load(fh).get("paths") or []
        return [p for p in paths if exists(p)]
    out = []
    if os.path.isdir(avatars_dir):
        for name in sorted(os.listdir(avatars_dir)):
            if name.lower().endswith((".jpg", ".jpeg", ".png", ".webp")):
                out.append(f"/uploads/avatares/{name}")
    return out


def build_video_pool() -> list[dict]:
    pool: list[dict] = []

    for name in CRIADOR_VIDEOS:
        url = f"/uploads/{name}"
        if exists(url):
            pool.append({"url": url, "autor": "", "avatar": ""})

    avatars = avatar_pool()
    creators = CREATORS[:]
    for i, vid in enumerate(pool):
        if not vid.get("autor"):
            c = creators[i % len(creators)]
            vid["autor"] = c["autor"]
        if not vid.get("avatar"):
            fallback = CREATORS[i % len(CREATORS)]["avatar"]
            if exists(fallback):
                vid["avatar"] = fallback
            elif avatars:
                vid["avatar"] = avatars[i % len(avatars)]
            else:
                vid["avatar"] = ""
    return pool


def remove_creamy_assets() -> int:
    removed = 0
    if not os.path.isdir(UPLOADS):
        return removed
    for name in os.listdir(UPLOADS):
        if is_creamy_video(name):
            os.remove(os.path.join(UPLOADS, name))
            removed += 1
            print(f"  removido: {name}")
    return removed


def assign_to_products(pool: list[dict], per_product: int = 7) -> None:
    with open(PRODUTOS_PATH, encoding="utf-8") as fh:
        produtos = json.load(fh)

    n = len(pool)
    if not n:
        raise RuntimeError("Nenhum video encontrado em uploads/")

    for idx, prod in enumerate(produtos):
        start = idx % n
        selected = []
        for j in range(min(per_product, n)):
            selected.append(dict(pool[(start + j) % n]))
        prod["videos"] = selected

    with open(PRODUTOS_PATH, "w", encoding="utf-8") as fh:
        json.dump(produtos, fh, ensure_ascii=False, indent=4)


def main() -> None:
    print("Copiando videos de Downloads...")
    copy_downloads()
    print("Removendo videos Creamy (vcv_*)...")
    remove_creamy_assets()
    pool = build_video_pool()
    print(f"Pool de videos: {len(pool)}")
    for v in pool:
        print(f"  - {v['url']} ({v['autor']})")
    assign_to_products(pool)
    print(f"Atualizado produtos.json ({len(json.load(open(PRODUTOS_PATH)))} produtos)")


if __name__ == "__main__":
    main()
