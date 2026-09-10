#!/usr/bin/env python3
"""Catálogo leve para a vitrine (sem variações — carrega rápido)."""
import json
from pathlib import Path

ROOT = Path(__file__).resolve().parent
data = json.loads((ROOT / "produtos.json").read_text(encoding="utf-8"))
vitrine = []
for p in data:
    vitrine.append({
        "id": p.get("id"),
        "titulo": p.get("titulo", ""),
        "preco": p.get("preco"),
        "preco_comparacao": p.get("preco_comparacao"),
        "desconto": p.get("desconto"),
        "notas": p.get("notas"),
        "quantidade_produtos": p.get("quantidade_produtos"),
        "fotos": (p.get("fotos") or [])[:1],
        "status": p.get("status", "ativo"),
        "promo_ativa": p.get("promo_ativa", 0),
    })
out = ROOT / "produtos-vitrine.json"
out.write_text(json.dumps(vitrine, ensure_ascii=False, indent=2), encoding="utf-8")
print(f"OK: {len(vitrine)} produtos -> {out.name} ({out.stat().st_size // 1024} KB)")
