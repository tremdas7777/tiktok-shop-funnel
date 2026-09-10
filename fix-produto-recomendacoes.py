#!/usr/bin/env python3
from pathlib import Path
import re

path = Path(__file__).resolve().parent / "produto.php"
text = path.read_text(encoding="utf-8")

pattern = re.compile(
    r'(<section id="recomendacoes"[\s\S]*?)'
    r'<div style="display:grid;grid-template-columns:repeat\(2,1fr\);gap:10px;">[\s\S]*?'
    r'<script>\s*var _recData = [\s\S]*?function _recOpenModal\(id\) \{[\s\S]*?\}\s*</script>',
    re.MULTILINE,
)

replacement = r'''\1<div id="recomendacoes-grid" style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;"></div>
    <script>
    (function () {
      function fmt(v) {
        var n = Number(v || 0);
        return n.toFixed(2).replace('.', ',');
      }
      function renderRecGrid(produtos, atualId) {
        var grid = document.getElementById('recomendacoes-grid');
        if (!grid || !Array.isArray(produtos)) return;
        var lista = produtos.filter(function (p) { return String(p.id) !== String(atualId); }).slice(0, 4);
        grid.innerHTML = '';
        lista.forEach(function (p) {
          var img = (Array.isArray(p.fotos) && p.fotos[0]) ? p.fotos[0] : '';
          var card = document.createElement('div');
          card.style.cssText = 'background:#fff;border:1px solid #f3f4f6;border-radius:12px;padding:10px;display:flex;flex-direction:column;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.06);min-width:0;';
          card.onclick = function () {
            window.location.href = 'produto.php?produto_id=' + encodeURIComponent(p.id);
          };
          card.innerHTML = '<div style="width:100%;height:140px;margin-bottom:8px;border-radius:6px;overflow:hidden;background:#f9f9f9;"><img src="' + img + '" alt="" style="width:100%;height:100%;object-fit:contain;display:block;" loading="lazy"></div><div style="font-size:12px;font-weight:600;color:#111;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:5px;height:32px;">' + (p.titulo || '') + '</div><div style="font-size:16px;font-weight:700;color:#fe2d55;">R$ ' + fmt(p.preco) + '</div>';
          grid.appendChild(card);
        });
      }
      document.addEventListener('DOMContentLoaded', function () {
        var params = new URLSearchParams(window.location.search);
        var atualId = params.get('produto_id');
        fetch('produtos.json?t=' + Date.now(), { cache: 'no-store' })
          .then(function (r) { return r.ok ? r.json() : []; })
          .then(function (produtos) { renderRecGrid(produtos, atualId); })
          .catch(function () {});
      });
    })();
    function _recOpenModal(id) {
        fetch('produtos.json?t=' + Date.now(), { cache: 'no-store' })
          .then(function (r) { return r.ok ? r.json() : []; })
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
    </script>'''

new_text, n = pattern.subn(replacement, text, count=1)
if n != 1:
    raise SystemExit(f"substituição falhou (matches={n})")
path.write_text(new_text, encoding="utf-8")
print("OK: recomendações dinâmicas em produto.php")
