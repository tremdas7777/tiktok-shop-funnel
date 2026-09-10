#!/usr/bin/env python3
"""Produtos com preço > 400: reduz pela metade (produto + variações)."""
import json
from pathlib import Path

ROOT = Path(__file__).resolve().parent
path = ROOT / "produtos.json"


def money(v: float) -> str:
    return f"{v:.2f}"


def discount_pct(original: float, sale: float) -> str:
    if original <= 0:
        return "0.00"
    return money((1 - sale / original) * 100)


data = json.loads(path.read_text(encoding="utf-8"))
changed = 0

for prod in data:
    preco = float(prod.get("preco") or 0)
    if preco <= 400:
        continue

    comp = float(prod.get("preco_comparacao") or preco)
    novo = round(preco / 2, 2)
    prod["preco"] = money(novo)
    prod["desconto"] = discount_pct(comp, novo)

    for var in prod.get("variacoes") or []:
        vp = float(var.get("preco") or preco)
        if vp > 400:
            vn = round(vp / 2, 2)
        else:
            vn = novo
        vc = float(var.get("preco_comparacao") or comp)
        var["preco"] = money(vn)
        var["desconto"] = discount_pct(vc, vn)

    changed += 1
    print(f"  id={prod['id']} {preco:.2f} -> {novo:.2f} (de {comp:.2f})")

path.write_text(json.dumps(data, ensure_ascii=False, indent=4), encoding="utf-8")
print(f"\nAtualizados: {changed} produtos")
