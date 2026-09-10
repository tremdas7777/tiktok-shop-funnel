#!/usr/bin/env python3
"""Atualiza cart.php: recomendações dinâmicas ASICS e remove blob Creamy."""
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parent
cart_path = ROOT / "cart.php"
content = cart_path.read_text(encoding="utf-8")

# Substituir seção estática de recomendações
rec_start = content.find('<section id="recomendacoes"')
rec_end = content.find('</section>', rec_start)
if rec_start < 0 or rec_end < 0:
    raise SystemExit("Seção recomendacoes não encontrada")
rec_end += len('</section>')

new_rec_section = """<section id="recomendacoes" class="mt-6">
            <p style="font-size:15px;font-weight:700;color:#111;margin-bottom:12px;padding:0 2px;">Você também pode gostar</p>
            <div id="rec-grid" style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;"></div>
        </section>"""

content = content[:rec_start] + new_rec_section + content[rec_end:]

# Remover _produtosData embutido (Creamy)
content = re.sub(
    r"\n    const _produtosData = \[.*?\];\n",
    "\n    // Produtos carregados via fetch('produtos.json') → produtosSistema\n",
    content,
    count=1,
    flags=re.DOTALL,
)

# Atualizar fetch de produtos
content = content.replace(
    ".then(data => { produtosSistema = data; if (typeof renderCart === 'function') renderCart(); })",
    ".then(data => { produtosSistema = data; if (typeof renderCart === 'function') renderCart(); if (typeof renderRecomendacoes === 'function') renderRecomendacoes(); })",
)

# Inserir renderRecomendacoes antes de openRecModal (mesmo script que normalizarProduto)
insert_marker = "    function openRecModal(id) {"
render_fn = """    function renderRecomendacoes() {
        var grid = document.getElementById('rec-grid');
        if (!grid || !produtosSistema.length) return;
        var idsNoCarrinho = new Set((cart.items || []).map(function (i) {
            return String(i.produtoId || i.id || i.produto_id || '');
        }));
        var lista = produtosSistema.filter(function (p) {
            return !idsNoCarrinho.has(String(p.id));
        }).slice(0, 4);
        grid.innerHTML = '';
        lista.forEach(function (produto) {
            var norm = normalizarProduto(produto);
            var pid = norm.id;
            var pct = norm.precoComparacao > norm.preco
                ? Math.round(((norm.precoComparacao - norm.preco) / norm.precoComparacao) * 100)
                : Math.round(norm.desconto || 0);
            var card = document.createElement('div');
            card.style.cssText = 'background:#fff;border:1px solid #f3f4f6;border-radius:12px;padding:10px;display:flex;flex-direction:column;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.06);min-width:0;';
            card.onclick = function () {
                window.location.href = 'produto.html?produto_id=' + encodeURIComponent(pid);
            };
            card.innerHTML =
                '<div style="width:100%;height:140px;margin-bottom:8px;border-radius:6px;overflow:hidden;background:#f9f9f9;">' +
                '<img src="' + (norm.imagemPrincipal || '') + '" alt="" style="width:100%;height:100%;object-fit:contain;display:block;" loading="lazy" onerror="this.style.opacity=0.3">' +
                '</div>' +
                '<div style="flex:1;display:flex;flex-direction:column;justify-content:space-between;">' +
                '<div>' +
                '<div style="display:flex;align-items:center;gap:4px;margin-bottom:3px;">' +
                '<img src="/uploads/tagoficial.png" style="height:12px;width:auto;display:block;flex-shrink:0;">' +
                '</div>' +
                '<div style="font-size:12px;font-weight:600;color:#111;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:5px;height:32px;">' + (norm.titulo || '') + '</div>' +
                '<div style="display:flex;flex-wrap:nowrap;gap:3px;margin-bottom:4px;overflow:hidden;align-items:center;height:15px;">' +
                '<span style="background:#ffd0d9;color:#d6003a;font-size:9.5px;font-weight:700;padding:0 5px;border-radius:3px;display:inline-flex;align-items:center;gap:2px;white-space:nowrap;flex-shrink:0;height:15px;box-sizing:border-box;">' +
                '<img src=\\'/uploads/bilhete.png?v=2\\' width=\\'9\\' height=\\'9\\' alt=\\'\\' style=\\'display:block;flex-shrink:0;\\'/>' +
                pct + '% OFF</span>' +
                '</div>' +
                '</div>' +
                '<div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px;">' +
                '<div>' +
                '<div style="font-size:16px;font-weight:700;color:#fe2d55;line-height:1.1;margin-bottom:1px;">R$ ' + formatBRL(norm.preco) + '</div>' +
                '<div style="font-size:10px;color:#9ca3af;text-decoration:line-through;">R$ ' + formatBRL(norm.precoComparacao) + '</div>' +
                '</div>' +
                '<button data-rec-add type="button" style="background:#ffe0e6;border:none;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">' +
                '<img src="/uploads/carrinho-rosa.png?v=3" style="width:28px;height:28px;object-fit:contain;">' +
                '</button></div></div>';
            var btn = card.querySelector('[data-rec-add]');
            if (btn) {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    openRecModal(pid);
                });
            }
            grid.appendChild(card);
        });
    }

    function openRecModal(id) {"""

if insert_marker not in content:
    raise SystemExit("Marcador para renderRecomendacoes não encontrado")
content = content.replace(insert_marker, render_fn, 1)

# openRecModal usa produtosSistema
content = content.replace(
    "var produto = _produtosData.find(function(p){ return String(p.id) === String(id); });",
    "var produto = produtosSistema.find(function(p){ return String(p.id) === String(id); });",
)

# Re-render recomendações após alterar carrinho
content = content.replace(
    "_updateCartTotals();\n        }",
    "_updateCartTotals();\n            if (typeof renderRecomendacoes === 'function') renderRecomendacoes();\n        }",
)

cart_path.write_text(content, encoding="utf-8")
print("cart.php atualizado:", cart_path)
