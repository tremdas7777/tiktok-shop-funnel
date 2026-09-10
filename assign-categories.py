#!/usr/bin/env python3
"""Define categorias Masculino, Feminino e Unisex em loja.json e produtos.json."""
from __future__ import annotations

import json
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parent
PRODUTOS_PATH = ROOT / "produtos.json"
LOJA_PATH = ROOT / "loja.json"

CATEGORIAS = [
    {"id": "masculino", "nome": "Masculino"},
    {"id": "feminino", "nome": "Feminino"},
    {"id": "unisex", "nome": "Unisex"},
]


def classificar(titulo: str) -> str:
    t = titulo.lower()
    if re.search(r"\bunissex\b|\bunisex\b", t):
        return "unisex"
    if re.search(r"\bfeminino\b|\bfeminina\b", t):
        return "feminino"
    if re.search(r"\bmasculino\b|\bmasculina\b", t):
        return "masculino"
    if re.search(r"\binfantil\b|\bkids\b|\bjuvenil\b", t):
        return "unisex"
    return "unisex"


def main() -> None:
    produtos = json.loads(PRODUTOS_PATH.read_text(encoding="utf-8"))
    loja = json.loads(LOJA_PATH.read_text(encoding="utf-8"))

    buckets: dict[str, list[int]] = {c["id"]: [] for c in CATEGORIAS}
    nomes = {c["id"]: c["nome"] for c in CATEGORIAS}

    for produto in produtos:
        cat_id = classificar(produto.get("titulo") or "")
        produto["categoria"] = nomes[cat_id]
        buckets[cat_id].append(int(produto["id"]))

    loja["categorias"] = [
        {
            "id": cat["id"],
            "nome": cat["nome"],
            "produto_ids": sorted(buckets[cat["id"]]),
        }
        for cat in CATEGORIAS
    ]

    PRODUTOS_PATH.write_text(
        json.dumps(produtos, ensure_ascii=False, indent=4) + "\n",
        encoding="utf-8",
    )
    LOJA_PATH.write_text(
        json.dumps(loja, ensure_ascii=False, indent=4) + "\n",
        encoding="utf-8",
    )

    for cat in CATEGORIAS:
        print(f"{cat['nome']}: {len(buckets[cat['id']])} produtos")
    print("Atualizado loja.json e produtos.json")


if __name__ == "__main__":
    main()
