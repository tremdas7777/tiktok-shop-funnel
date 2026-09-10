#!/usr/bin/env python3
"""Baixa avatares realistas e associa a cada comentario em produtos.json."""
from __future__ import annotations

import hashlib
import json
import os
import time
import urllib.request

ROOT = os.path.dirname(os.path.abspath(__file__))
UPLOADS = os.path.join(ROOT, "uploads", "avatares")
PRODUTOS_PATH = os.path.join(ROOT, "produtos.json")
MANIFEST = os.path.join(UPLOADS, "manifest.json")
POOL_SIZE = 48
UA = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36"


def fetch_json(url: str) -> dict:
    req = urllib.request.Request(url, headers={"User-Agent": UA})
    with urllib.request.urlopen(req, timeout=60) as resp:
        return json.loads(resp.read().decode("utf-8"))


def download(url: str, dest: str) -> bool:
    if os.path.exists(dest) and os.path.getsize(dest) > 2000:
        return True
    try:
        req = urllib.request.Request(url, headers={"User-Agent": UA})
        with urllib.request.urlopen(req, timeout=45) as resp:
            data = resp.read()
        if len(data) < 2000:
            return False
        with open(dest, "wb") as fh:
            fh.write(data)
        return True
    except Exception:
        return False


def build_pool() -> list[str]:
    os.makedirs(UPLOADS, exist_ok=True)
    if os.path.isfile(MANIFEST):
        with open(MANIFEST, encoding="utf-8") as fh:
            cached = json.load(fh)
        paths = cached.get("paths") or []
        if len(paths) >= POOL_SIZE and all(
            os.path.isfile(os.path.join(ROOT, p.lstrip("/"))) for p in paths[:POOL_SIZE]
        ):
            return paths[:POOL_SIZE]

    # Mix de nacionalidades para rostos variados e naturais
    urls = [
        "https://randomuser.me/api/?results=16&nat=br&inc=picture",
        "https://randomuser.me/api/?results=16&nat=us&inc=picture",
        "https://randomuser.me/api/?results=16&nat=gb,fr,es&inc=picture",
    ]
    paths: list[str] = []
    idx = 1
    for api_url in urls:
        data = fetch_json(api_url)
        for person in data.get("results") or []:
            if len(paths) >= POOL_SIZE:
                break
            pic = (person.get("picture") or {}).get("medium") or ""
            if not pic:
                continue
            filename = f"avatar_{idx:03d}.jpg"
            dest = os.path.join(UPLOADS, filename)
            if download(pic, dest):
                paths.append(f"/uploads/avatares/{filename}")
                idx += 1
                time.sleep(0.15)
        time.sleep(0.3)

    if len(paths) < 12:
        raise RuntimeError(f"Pool insuficiente: {len(paths)} avatares")

    with open(MANIFEST, "w", encoding="utf-8") as fh:
        json.dump({"paths": paths, "count": len(paths)}, fh, indent=2)
    return paths


def pick_avatar(pool: list[str], produto_id: int, comment_id: int, slot: int) -> str:
    seed = int(
        hashlib.md5(f"{produto_id}:{comment_id}:{slot}".encode()).hexdigest()[:8],
        16,
    )
    return pool[seed % len(pool)]


def main() -> None:
    pool = build_pool()
    with open(PRODUTOS_PATH, encoding="utf-8") as fh:
        produtos = json.load(fh)

    used_pairs: set[tuple[int, str]] = set()
    updated = 0

    for prod in produtos:
        pid = int(prod.get("id") or 0)
        for slot, com in enumerate(prod.get("comentarios") or []):
            cid = int(com.get("id") or (pid * 100 + slot))
            avatar = pick_avatar(pool, pid, cid, slot)
            # Evita repetir o mesmo rosto no mesmo produto
            tries = 0
            while (pid, avatar) in used_pairs and tries < len(pool):
                avatar = pool[(pool.index(avatar) + 1) % len(pool)]
                tries += 1
            used_pairs.add((pid, avatar))
            com["foto_perfil"] = avatar
            updated += 1

    with open(PRODUTOS_PATH, "w", encoding="utf-8") as fh:
        json.dump(produtos, fh, ensure_ascii=False, indent=4)

    print(f"Pool: {len(pool)} avatares | Comentarios atualizados: {updated}")


if __name__ == "__main__":
    main()
