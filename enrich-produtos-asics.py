#!/usr/bin/env python3
"""Enriquece produtos.json com descricao e comentarios unicos por produto."""
from __future__ import annotations

import hashlib
import json
import os
import re
import sys
import time
import urllib.parse

ROOT = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, ROOT)

from import_centauro_asics import (  # noqa: E402
    BFF,
    build_description,
    build_reviews,
    extract_pairs,
    fetch_json,
    load_listing,
    strip_html,
)

PRODUTOS_PATH = os.path.join(ROOT, "produtos.json")

NOMES = [
    "ca****o", "ma***a", "jo**o", "fe****a", "lu***a",
    "ri****o", "pa***a", "an***a", "le***o", "bi***a",
    "ra***l", "ju***a", "ti**o", "ka***n", "de**a",
]

REVIEW_TEMPLATES = [
    "Comprei o {modelo} para corrida de rua e a entrega foi rápida. O tênis veio bem embalado, numeração correta e confortável desde o primeiro uso.",
    "Estou usando o {modelo} há duas semanas nos treinos. Amortecimento excelente e boa estabilidade no pisada. Recomendo para quem treina 3x por semana.",
    "Achei o {modelo} leve e responsivo. Usei em intervalados e longos; não machucou o joelho. Custo-benefício muito bom na promoção.",
    "Tênis {modelo} lindo e confortável. Uso no dia a day e para caminhada. Solado segura bem em piso seco e úmido.",
    "Primeira compra desse modelo {modelo}. Coube certinho, material de qualidade e acabamento impecável. Voltarei a comprar.",
    "O {modelo} superou expectativa: macio, ventilado e com bom retorno de energia. Ideal para treinos de 5 a 10 km.",
    "Pedido chegou antes do prazo. O {modelo} é estável na corrida e não aperta o peito do pé. Muito satisfeito.",
    "Comprei para maratona de preparação. O {modelo} tem excelente absorção de impacto e não esquenta demais o pé.",
    "Qualidade ASICS como sempre. O {modelo} veio original, caixa intacta e etiqueta. Nota 10.",
    "Troquei meu tênis antigo pelo {modelo} e senti diferença na amortecia. Ótimo para asfalto.",
    "Uso {modelo} no trabalho e na academia. Confortável o dia inteiro, sem bolhas ou desconforto.",
    "Meu marido amou o {modelo}. Disse que é o mais confortável que já teve para corrida matinal.",
]

DESC_EXTRAS = [
    "Cabedal respirável que ajuda a manter os pés secos durante treinos intensos.",
    "Entressola com tecnologia de amortecimento ASICS para maior conforto a cada passada.",
    "Solado de borracha com tração reforçada para corrida em asfalto e esteira.",
    "Design leve pensado para corridas diárias, caminhadas e uso casual.",
    "Construção reforçada na região do calcanhar para maior estabilidade.",
    "Ideal para corredores que buscam equilíbrio entre conforto, performance e durabilidade.",
]


def model_name(titulo: str) -> str:
    t = titulo.strip()
    t = re.sub(r"^Tênis\s+(Masculino|Feminino|Unissex)\s+", "", t, flags=re.I)
    t = re.sub(r"^Tênis\s+", "", t, flags=re.I)
    return t or titulo


def listing_description(list_item: dict, titulo: str) -> str:
    details = list_item.get("details") or {}
    seo = list_item.get("seo") or {}
    parts: list[str] = []
    for key in ("fullDescription", "shortDescription"):
        val = details.get(key) or seo.get(key)
        if val:
            plain = strip_html(str(val))
            if plain and plain not in parts:
                parts.append(plain)
    name = model_name(titulo)
    if not parts:
        parts.append(
            f"O {name} combina conforto, tecnologia ASICS e design moderno para treinos e uso diário."
        )
    seed = int(hashlib.md5(titulo.encode()).hexdigest()[:8], 16)
    extras = []
    for i in range(3):
        extras.append(DESC_EXTRAS[(seed + i) % len(DESC_EXTRAS)])
    body = parts[0]
    if len(body) < 120:
        body = body + "\n\n" + "\n\n".join(extras)
    elif extras:
        body = body + "\n\n" + extras[0]
    return body.strip()


def synthetic_reviews(produto_id: int, titulo: str, start_id: int, count: int = 5) -> list[dict]:
    modelo = model_name(titulo)
    seed = int(hashlib.md5(f"{produto_id}:{titulo}".encode()).hexdigest()[:8], 16)
    reviews = []
    for i in range(count):
        tpl = REVIEW_TEMPLATES[(seed + i * 3) % len(REVIEW_TEMPLATES)]
        nome = NOMES[(seed + i) % len(NOMES)]
        nota = 5 if (seed + i) % 5 else 4
        reviews.append(
            {
                "id": start_id + i,
                "owner_user_id": 316,
                "produto_id": produto_id,
                "nome": nome,
                "foto_perfil": "",
                "descricao": tpl.format(modelo=modelo),
                "nota": nota,
                "fotos": "[]",
                "videos": None,
                "created_at": "2026-09-10 00:00:00",
            }
        )
    return reviews


def main() -> None:
    with open(PRODUTOS_PATH, encoding="utf-8") as fh:
        produtos = json.load(fh)

    listing = load_listing()
    pairs = extract_pairs(listing.get("products") or [])
    by_sku: dict[str, tuple[str, str, dict]] = {}
    for pid, cid, item in pairs:
        seo = item.get("seo") or {}
        sku = str(seo.get("mpn") or item.get("code") or pid or "").strip()
        if sku:
            by_sku[sku] = (pid, cid, item)

    comment_id = 190001
    updated_desc = 0
    updated_rev = 0

    for idx, prod in enumerate(produtos, start=1):
        produto_id = int(prod["id"])
        titulo = prod.get("titulo") or "Tênis ASICS"
        sku = str(prod.get("sku") or "").strip()
        print(f"[{idx}/{len(produtos)}] {titulo[:55]}")

        desc = ""
        reviews: list[dict] = []

        if sku and sku in by_sku:
            bff_pid, cid, list_item = by_sku[sku]
            url = f"{BFF}/{bff_pid}"
            if cid:
                url += f"?color={urllib.parse.quote(cid)}"
            try:
                api = fetch_json(url)
                desc = build_description(api)
                reviews = build_reviews(api, produto_id, comment_id)
                time.sleep(0.3)
            except Exception as exc:  # noqa: BLE001
                print(f"  BFF erro: {exc}")
                desc = listing_description(list_item, titulo)
        else:
            print(f"  sku {sku or '-'} fora da listagem")

        if not desc or desc.strip() == titulo.strip():
            item = by_sku.get(sku, (None, None, {}))[2] if sku in by_sku else {}
            desc = listing_description(item, titulo)

        prod["descricao"] = desc
        updated_desc += 1

        if not reviews:
            reviews = synthetic_reviews(produto_id, titulo, comment_id, 5)
        prod["comentarios"] = reviews
        comment_id += len(reviews) + 5
        updated_rev += 1

    with open(PRODUTOS_PATH, "w", encoding="utf-8") as fh:
        json.dump(produtos, fh, ensure_ascii=False, indent=4)

    print(f"Salvo: {updated_desc} descricoes, {updated_rev} produtos com comentarios -> {PRODUTOS_PATH}")


if __name__ == "__main__":
    main()
