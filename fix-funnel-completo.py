#!/usr/bin/env python3
"""Restaura ASICS + fluxo igual referência loja.wwtiktokshop.com."""
from __future__ import annotations

import json
import re
import subprocess
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent


def run(cmd: list[str]) -> None:
    print("+", " ".join(cmd))
    subprocess.run(cmd, cwd=ROOT, check=True)


def extract_block(src: str, start_marker: str, end_marker: str) -> str:
    s = src.find(start_marker)
    e = src.find(end_marker, s)
    if s == -1 or e == -1:
        raise RuntimeError(f"Marcador não encontrado: {start_marker!r}")
    return src[s:e]


def patch_index() -> None:
    path = ROOT / "index.html"
    ref = (ROOT / "index.html.new").read_text(encoding="utf-8")
    text = path.read_text(encoding="utf-8")

    # Navegação simples
    text = re.sub(
        r"<script>\s*\(function \(\) \{[\s\S]*?\}\)\(\);\s*</script>\s*<script src=\"https://cdn.tailwindcss.com\">",
        '<script>\n  window.irProduto = function (id) {\n    if (id == null || id === \'\') return;\n    window.location.href = \'produto.php?produto_id=\' + encodeURIComponent(id);\n  };\n</script>\n  <script src="https://cdn.tailwindcss.com">',
        text,
        count=1,
    )

    # renderProdutos igual referência (data-action goto-checkout)
    new_render = extract_block(ref, "function renderProdutos() {", "// Renderizações da página inicial")
    text = re.sub(
        r"function renderProdutos\(\) \{[\s\S]*?// Renderizações da página inicial",
        new_render + "// Renderizações da página inicial",
        text,
        count=1,
    )

    # renderHomeSections - handlers goto-checkout
    new_home = extract_block(ref, "function renderHomeSections() {", "function restoreCatFromHash")
    text = re.sub(
        r"function renderHomeSections\(\) \{[\s\S]*?function restoreCatFromHash",
        new_home + "function restoreCatFromHash",
        text,
        count=1,
    )

    # cat panel
    new_cat = extract_block(ref, "function renderCatPanelGrid() {", "function showTab")
    text = re.sub(
        r"function renderCatPanelGrid\(\) \{[\s\S]*?function showTab",
        new_cat + "function showTab",
        text,
        count=1,
    )

    # Remove helpers que não existem na referência
    for fn in ("urlPaginaProduto", "linkImagemProduto", "linkTextoProduto", "configurarCliqueProduto", "carregarProdutoCompleto"):
        text = re.sub(rf"    function {fn}\([\s\S]*?\n    }}\n\n", "", text)

    text = text.replace("async function abrirModalProduto", "function abrirModalProduto")

    path.write_text(text, encoding="utf-8")
    print("index.html: fluxo de clique igual referência")


def patch_produto_recomendacoes() -> None:
    path = ROOT / "produto.php"
    text = path.read_text(encoding="utf-8")
    start = text.find('<section id="recomendacoes"')
    end = text.find("<!-- Modal de tela cheia para imagens -->", start)
    if start == -1 or end == -1:
        raise RuntimeError("Bloco recomendacoes não encontrado em produto.php")

    block = r"""<section id="recomendacoes" style="padding:16px;margin-top:4px;">
        <p style="font-size:15px;font-weight:700;color:#111;margin-bottom:12px;padding:0 2px;border-left:3px solid #fe2d55;padding-left:10px;">Você também pode gostar</p>
        <div id="recomendacoes-grid" style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;"></div>
    </section>
    <script>
    (function () {
      function fmt(v) { return Number(v || 0).toFixed(2).replace('.', ','); }
      function pct(p) {
        var preco = Number(p.preco || 0), comp = Number(p.preco_comparacao || p.precoComparacao || 0);
        return comp > preco ? Math.round(((comp - preco) / comp) * 100) : Math.round(Number(p.desconto || 0));
      }
      function cardHtml(p) {
        var img = (Array.isArray(p.fotos) && p.fotos[0]) ? p.fotos[0] : '';
        var url = 'produto.php?produto_id=' + encodeURIComponent(p.id);
        return '<div onclick="window.location.href=\'' + url + '\'" style="background:#fff;border:1px solid #f3f4f6;border-radius:12px;padding:10px;display:flex;flex-direction:column;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.06);min-width:0;">'
          + '<div style="width:100%;height:140px;margin-bottom:8px;border-radius:6px;overflow:hidden;background:#f9f9f9;"><img src="' + img + '" alt="" style="width:100%;height:100%;object-fit:contain;display:block;" loading="lazy"></div>'
          + '<div style="flex:1;display:flex;flex-direction:column;justify-content:space-between;"><div>'
          + '<div style="display:flex;align-items:center;gap:4px;margin-bottom:3px;"><img src="/uploads/tagoficial.png" style="height:12px;width:auto;"><img src="/uploads/tagtorcer.png" style="height:12px;width:auto;"></div>'
          + '<div style="font-size:12px;font-weight:600;color:#111;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:5px;height:32px;">' + (p.titulo || '') + '</div>'
          + '<div style="display:flex;gap:3px;margin-bottom:4px;"><span style="background:#ffd0d9;color:#d6003a;font-size:9.5px;font-weight:700;padding:0 5px;border-radius:3px;display:inline-flex;align-items:center;gap:2px;height:15px;"><img src=\'/uploads/bilhete.png?v=2\' width=\'9\' height=\'9\'/>' + pct(p) + '% OFF</span></div>'
          + '<div style="margin-bottom:4px;"><span style="background:#b2f0f5;color:#006875;font-size:9.5px;font-weight:700;padding:0 5px;border-radius:3px;height:15px;display:inline-flex;align-items:center;">Frete grátis</span></div>'
          + '<div style="display:flex;align-items:center;gap:3px;margin-bottom:4px;"><span style="color:#f59e0b;font-size:11px;">★</span><span style="font-size:10px;color:#6b7280;">' + (p.notas || '5') + ' | ' + (p.quantidade_produtos || 0) + ' vendido(s)</span></div>'
          + '</div><div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px;"><div><div style="font-size:16px;font-weight:700;color:#fe2d55;">R$ ' + fmt(p.preco) + '</div>'
          + '<div style="font-size:10px;color:#9ca3af;text-decoration:line-through;">R$ ' + fmt(Number(p.preco_comparacao || p.precoComparacao || p.preco)) + '</div></div>'
          + '<button onclick="event.stopPropagation();_recOpenModal(' + p.id + ')" style="background:#ffe0e6;border:none;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;"><img src="/uploads/carrinho-rosa.png?v=3" style="width:28px;height:28px;object-fit:contain;"></button>'
          + '</div></div></div>';
      }
      document.addEventListener('DOMContentLoaded', function () {
        var atualId = new URLSearchParams(location.search).get('produto_id');
        fetch('produtos.json?t=' + Date.now(), { cache: 'no-store' }).then(function (r) { return r.ok ? r.json() : []; })
          .then(function (list) {
            var grid = document.getElementById('recomendacoes-grid');
            if (!grid) return;
            grid.innerHTML = list.filter(function (p) { return String(p.id) !== String(atualId); }).slice(0, 4).map(cardHtml).join('');
          });
      });
    })();
    function _recOpenModal(id) {
      fetch('produtos.json?t=' + Date.now(), { cache: 'no-store' }).then(function (r) { return r.ok ? r.json() : []; })
        .then(function (list) {
          var p = list.find(function (x) { return String(x.id) === String(id); });
          if (!p) return;
          if (typeof normalizarProduto === 'function' && typeof abrirModalProduto === 'function') {
            abrirModalProduto(normalizarProduto(p));
          } else {
            window.location.href = 'produto.php?produto_id=' + encodeURIComponent(id);
          }
        });
    }
    </script>

"""
    path.write_text(text[:start] + block + text[end:], encoding="utf-8")
    print("produto.php: design referência + produtos ASICS dinâmicos")


def patch_mirror() -> None:
    path = ROOT / "mirror.py"
    text = path.read_text(encoding="utf-8")
    text = text.replace('"loja.json",\n        "produtos.json",', '"# loja.json",\n        "# produtos.json",')
    path.write_text(text, encoding="utf-8")
    print("mirror.py: protegido loja.json e produtos.json")


def main() -> None:
    run([sys.executable, str(ROOT / "import-centauro-asics.py"), "--listing-only"])
    if (ROOT / "ajustar-precos-acima-400.py").exists():
        run([sys.executable, str(ROOT / "ajustar-precos-acima-400.py")])
    run([sys.executable, str(ROOT / "gerar-vitrine.py")])
    patch_index()
    patch_produto_recomendacoes()
    patch_mirror()
    data = json.loads((ROOT / "produtos.json").read_text(encoding="utf-8"))
    print(f"Pronto: {len(data)} tênis ASICS | exemplo: produto.php?produto_id={data[0]['id']}")


if __name__ == "__main__":
    main()
