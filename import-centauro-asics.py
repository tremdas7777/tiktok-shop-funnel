#!/usr/bin/env python3
"""Importa tênis ASICS (1ª página) da Centauro para produtos.json."""
from __future__ import annotations

import json
import os
import re
import time
import urllib.parse
import urllib.request
from html import unescape

ROOT = os.path.dirname(os.path.abspath(__file__))
UPLOADS = os.path.join(ROOT, "uploads")
SEARCH_URL = (
    "https://apigateway.centauro.com.br/centauro-bff/search"
    "?term=&page=1&pageSize=36&fields=produto%3Atenis&fields=marca%3Aasics"
)
BFF = "https://apigateway.centauro.com.br/centauro-bff/products"
HEADERS = {
    "accept": "*/*",
    "accept-language": "pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7",
    "origin": "https://www.centauro.com.br",
    "referer": "https://www.centauro.com.br/hotsite/casa-da-corrida?fields=produto%3Atenis&fields=marca%3Aasics",
    "sec-ch-ua": '"Google Chrome";v="129", "Not=A?Brand";v="8", "Chromium";v="129"',
    "sec-ch-ua-mobile": "?0",
    "sec-ch-ua-platform": '"macOS"',
    "sec-fetch-dest": "empty",
    "sec-fetch-mode": "cors",
    "sec-fetch-site": "same-site",
    "user-agent": (
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) "
        "AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36"
    ),
}
LISTING_CACHE = os.path.join(ROOT, "centauro-listing-page1.json")
DEFAULT_SIZES = [str(n) for n in range(34, 49)]


def fetch_json(url: str, retries: int = 3) -> dict:
    last_err = None
    for attempt in range(retries):
        try:
            req = urllib.request.Request(url, headers=HEADERS)
            with urllib.request.urlopen(req, timeout=45) as resp:
                return json.loads(resp.read().decode("utf-8"))
        except Exception as exc:  # noqa: BLE001
            last_err = exc
            time.sleep(1.5 * (attempt + 1))
    raise RuntimeError(f"Falha ao buscar {url}: {last_err}")


def extract_pairs(products: list) -> list[tuple[str, str, dict]]:
    pairs: list[tuple[str, str, dict]] = []
    seen: set[str] = set()
    for item in products:
        details = item.get("details") or {}
        seo = item.get("seo") or {}
        variant_id = str(item.get("id") or details.get("modelColor") or "").strip()
        pid = str(seo.get("mpn") or item.get("code") or variant_id or "").strip()
        cid = str(details.get("colorId") or "").strip()
        if not variant_id and not pid:
            continue
        key = variant_id or f"{pid}:{cid}"
        if key in seen:
            continue
        seen.add(key)
        pairs.append((pid, cid, item))
    return pairs


def money(value: float) -> str:
    return f"{value:.2f}"


def discount_pct(original: float, sale: float) -> str:
    if original <= 0:
        return "0.00"
    return money((1 - sale / original) * 100)


def strip_html(text: str) -> str:
    if not text:
        return ""
    text = unescape(text)
    text = re.sub(r"<[^>]+>", " ", text)
    text = re.sub(r"\s+", " ", text).strip()
    return text


def download_image(url: str, prefix: str, idx: int) -> str | None:
    if not url:
        return None
    if url.startswith("//"):
        url = "https:" + url
    ext = ".jpg"
    if ".webp" in url.lower():
        ext = ".webp"
    elif ".png" in url.lower():
        ext = ".png"
    filename = f"{prefix}_{idx:02d}{ext}"
    path = os.path.join(UPLOADS, filename)
    rel = f"/uploads/{filename}"
    if os.path.exists(path) and os.path.getsize(path) > 1000:
        return rel
    try:
        req = urllib.request.Request(url, headers=HEADERS)
        with urllib.request.urlopen(req, timeout=60) as resp:
            data = resp.read()
        if len(data) < 500:
            return None
        with open(path, "wb") as fh:
            fh.write(data)
        return rel
    except Exception:
        return None


def build_description(api: dict) -> str:
    parts: list[str] = []
    prod = api.get("product") or {}
    if prod.get("shortDescription"):
        parts.append(prod["shortDescription"])
    elif prod.get("description"):
        parts.append(prod["description"])
    for attr in api.get("attributes") or []:
        html = attr.get("htmlContent") or ""
        plain = strip_html(html)
        if plain:
            parts.append(plain)
        for val in attr.get("values") or []:
            label = val.get("label") or ""
            values = val.get("values") or []
            if label and values:
                parts.append(f"{label}: {', '.join(values)}")
    return "\n\n".join(parts[:6])


def build_reviews(api: dict, produto_id: int, start_id: int) -> list[dict]:
    reviews = []
    schema = (((api.get("seo") or {}).get("schema") or {}).get("product") or {})
    raw = schema.get("reviews") or []
    for i, rv in enumerate(raw[:5]):
        body = (rv.get("reviewBody") or "").strip()
        if not body:
            continue
        author = (rv.get("author") or {}).get("name") or "Cliente"
        rating = float((rv.get("reviewRating") or {}).get("ratingValue") or 5)
        reviews.append(
            {
                "id": start_id + i,
                "owner_user_id": 316,
                "produto_id": produto_id,
                "nome": author[:12].lower().replace(" ", "*") + "****",
                "foto_perfil": "",
                "descricao": body,
                "nota": int(round(rating)),
                "fotos": "[]",
                "videos": None,
                "created_at": "2026-09-10 00:00:00",
            }
        )
    return reviews


def product_from_api(
    api: dict,
    list_item: dict,
    produto_id: int,
    var_id_start: int,
    comment_id_start: int,
) -> dict:
    prod = api.get("product") or {}
    details = list_item.get("details") or {}
    list_price = float(list_item.get("price") or details.get("price") or 0)
    installments = prod.get("installments") or []
    if not list_price and installments:
        list_price = float(installments[0].get("value") or 0)
    if not list_price:
        offer = (((api.get("seo") or {}).get("schema") or {}).get("product") or {}).get(
            "aggregateOffer"
        ) or {}
        list_price = float(offer.get("lowPrice") or offer.get("highPrice") or 399.9)

    sale_price = round(list_price * 0.5, 2)
    desc_pct = discount_pct(list_price, sale_price)

    prefix = f"asics_{prod.get('code', produto_id)}"
    fotos: list[str] = []
    for idx, media in enumerate(prod.get("visualMedias") or [], start=1):
        rel = download_image(media.get("url") or "", prefix, idx)
        if rel:
            fotos.append(rel)
        time.sleep(0.15)

    if not fotos:
        img = details.get("imageUrl") or details.get("photoUrl") or ""
        rel = download_image(img, prefix, 99)
        if rel:
            fotos.append(rel)

    sizes = prod.get("sizes") or []
    variacoes = []
    vid = var_id_start
    cover = fotos[0] if fotos else ""
    if sizes:
        for sz in sizes:
            size_label = str(sz.get("description") or sz.get("code") or "").strip()
            if not size_label:
                continue
            sz_price = float((sz.get("priceInfos") or {}).get("price") or list_price)
            sz_sale = round(sz_price * 0.5, 2)
            variacoes.append(
                {
                    "id": vid,
                    "owner_user_id": 316,
                    "produto_id": produto_id,
                    "tipo": "tamanho",
                    "titulo": size_label,
                    "preco": money(sz_sale),
                    "preco_comparacao": money(sz_price),
                    "desconto": discount_pct(sz_price, sz_sale),
                    "info": "Atributo: Tamanho",
                    "link_checkout": "",
                    "imagem": cover,
                    "created_at": "2026-09-10 00:00:00",
                }
            )
            vid += 1
    else:
        for size_label in DEFAULT_SIZES:
            variacoes.append(
                {
                    "id": vid,
                    "owner_user_id": 316,
                    "produto_id": produto_id,
                    "tipo": "tamanho",
                    "titulo": size_label,
                    "preco": money(sale_price),
                    "preco_comparacao": money(list_price),
                    "desconto": desc_pct,
                    "info": "Atributo: Tamanho",
                    "link_checkout": "",
                    "imagem": cover,
                    "created_at": "2026-09-10 00:00:00",
                }
            )
            vid += 1

    titulo = prod.get("name") or list_item.get("name") or "Tênis ASICS"
    color = (prod.get("colorInfo") or {}).get("description") or ""
    if color and color.lower() not in titulo.lower():
        titulo = f"{titulo} - {color}"

    comentarios = build_reviews(api, produto_id, comment_id_start)
    rating = float(
        (((api.get("seo") or {}).get("schema") or {}).get("product") or {})
        .get("aggregateRating", {})
        .get("ratingValue")
        or 4.8
    )

    return {
        "id": produto_id,
        "owner_user_id": 316,
        "titulo": titulo,
        "preco": money(sale_price),
        "preco_comparacao": money(list_price),
        "desconto": desc_pct,
        "categoria": "Tênis",
        "order_bump_ativo": 0,
        "order_bump_produto_id": None,
        "promo_ativa": 0,
        "promo_banner": None,
        "notas": f"{rating:.1f}",
        "descricao": build_description(api),
        "especificacoes": "",
        "diferenciais": "",
        "garantia": "",
        "fotos": fotos,
        "videos": [],
        "frete": "",
        "entrega": "",
        "oferta_termina_em": "",
        "recomendacoes": None,
        "modelo_landing": "modelo4",
        "avatar_comentario": None,
        "status": "ativo",
        "meta_title": None,
        "meta_description": None,
        "meta_keywords": None,
        "estoque_atual": 0,
        "estoque_minimo": 0,
        "estoque_maximo": None,
        "sku": prod.get("selectedProduct") or prod.get("code"),
        "nome_comentario": "",
        "quantidade_produtos": 800 + (produto_id % 900),
        "created_at": "2026-09-10 00:00:00",
        "updated_at": "2026-09-10 00:00:00",
        "oferta_relampago": {"ativo": False, "horas": 8, "ultimas": 5},
        "variacoes": variacoes,
        "comentarios": comentarios,
    }


def load_listing() -> dict:
    if os.path.isfile(LISTING_CACHE):
        print(f"Usando cache local: {LISTING_CACHE}")
        with open(LISTING_CACHE, encoding="utf-8") as fh:
            return json.load(fh)
    print("Buscando listagem Centauro (tênis ASICS)...")
    return fetch_json(SEARCH_URL)


def upscale_image(url: str) -> str:
    if not url:
        return ""
    if url.startswith("//"):
        url = "https:" + url
    return re.sub(r"/\d+x\d+/", "/1300x1300/", url)


def guess_gallery_urls(url: str) -> list[str]:
    if not url:
        return []
    url = upscale_image(url)
    base = re.sub(r"A\d(\.\w+)$", "", url.split("/")[-1])
    if not base:
        return [url]
    prefix = url.rsplit("/", 1)[0]
    ext = ".jpg"
    m = re.search(r"(A\d)(\.(\w+))$", url)
    if m:
        ext = m.group(2)
    urls = []
    for n in (4, 1, 2, 3):
        urls.append(f"{prefix}/{base}A{n}{ext}")
    return list(dict.fromkeys(urls))


def listing_images(list_item: dict) -> list[str]:
    urls: list[str] = []
    for media in list_item.get("medias") or []:
        urls.append(upscale_image(media.get("url") or ""))
    seo = list_item.get("seo") or {}
    for img in seo.get("images") or []:
        urls.append(upscale_image(img))
    image_obj = list_item.get("image") or {}
    if isinstance(image_obj, dict):
        urls.append(upscale_image(image_obj.get("default") or ""))
    elif isinstance(image_obj, str):
        urls.append(upscale_image(image_obj))
    return list(dict.fromkeys(u for u in urls if u))


def product_from_listing(list_item: dict, produto_id: int, var_id_start: int) -> dict | None:
    details = list_item.get("details") or {}
    seo = list_item.get("seo") or {}
    name = list_item.get("name") or seo.get("productName") or "Tênis ASICS"
    list_price = float(list_item.get("price") or 0)
    if list_price <= 0:
        offer = seo.get("aggregateOffer") or {}
        list_price = float(offer.get("lowPrice") or offer.get("highPrice") or 0)
    if list_price <= 0:
        return None
    sale_price = round(list_price * 0.5, 2)
    desc_pct = discount_pct(list_price, sale_price)
    fotos = listing_images(list_item)
    if not fotos:
        return None
    cover = fotos[0]
    variacoes = []
    vid = var_id_start
    for size_label in DEFAULT_SIZES:
        variacoes.append(
            {
                "id": vid,
                "owner_user_id": 316,
                "produto_id": produto_id,
                "tipo": "tamanho",
                "titulo": size_label,
                "preco": money(sale_price),
                "preco_comparacao": money(list_price),
                "desconto": desc_pct,
                "info": "Atributo: Tamanho",
                "link_checkout": "",
                "imagem": cover,
                "created_at": "2026-09-10 00:00:00",
            }
        )
        vid += 1
    return {
        "id": produto_id,
        "owner_user_id": 316,
        "titulo": name,
        "preco": money(sale_price),
        "preco_comparacao": money(list_price),
        "desconto": desc_pct,
        "categoria": "Tênis",
        "order_bump_ativo": 0,
        "order_bump_produto_id": None,
        "promo_ativa": 0,
        "promo_banner": None,
        "notas": str(
            float((seo.get("aggregateRating") or {}).get("ratingValue") or 4.8)
        ),
        "descricao": details.get("fullDescription") or details.get("shortDescription") or name,
        "especificacoes": "",
        "diferenciais": "",
        "garantia": "",
        "fotos": fotos,
        "videos": [],
        "frete": "",
        "entrega": "",
        "oferta_termina_em": "",
        "recomendacoes": None,
        "modelo_landing": "modelo4",
        "avatar_comentario": None,
        "status": "ativo",
        "meta_title": None,
        "meta_description": None,
        "meta_keywords": None,
        "estoque_atual": 0,
        "estoque_minimo": 0,
        "estoque_maximo": None,
        "sku": list_item.get("id") or seo.get("sku") or seo.get("mpn"),
        "nome_comentario": "",
        "quantidade_produtos": 800 + (produto_id % 900),
        "created_at": "2026-09-10 00:00:00",
        "updated_at": "2026-09-10 00:00:00",
        "oferta_relampago": {"ativo": False, "horas": 8, "ultimas": 5},
        "variacoes": variacoes,
        "comentarios": [],
    }


def main() -> None:
    import sys

    listing_only = "--listing-only" in sys.argv
    os.makedirs(UPLOADS, exist_ok=True)
    listing = load_listing()
    products = listing.get("products") or []
    print(f"Encontrados {len(products)} produtos na 1ª página")
    pairs = extract_pairs(products)

    out: list[dict] = []
    produto_id = 20001
    var_id = 180001
    comment_id = 190001

    for idx, (pid, cid, list_item) in enumerate(pairs, start=1):
        if listing_only:
            print(f"[{idx}/{len(pairs)}] {pid} (cache)")
            item = product_from_listing(list_item, produto_id, var_id)
            if not item:
                continue
            out.append(item)
            var_id += len(item["variacoes"]) + 1
            produto_id += 1
            continue

        url = f"{BFF}/{pid}"
        if cid:
            url += f"?color={urllib.parse.quote(cid)}"
        print(f"[{idx}/{len(pairs)}] {pid} cor={cid or '-'}")
        try:
            api = fetch_json(url)
            item = product_from_api(api, list_item, produto_id, var_id, comment_id)
            if not item["fotos"]:
                print("  aviso: sem imagens, pulando")
                continue
            out.append(item)
            var_id += len(item["variacoes"]) + 1
            comment_id += 20
            produto_id += 1
            time.sleep(0.35)
        except Exception as exc:  # noqa: BLE001
            print(f"  erro BFF, usando listagem: {exc}")
            item = product_from_listing(list_item, produto_id, var_id)
            if item:
                out.append(item)
                var_id += len(item["variacoes"]) + 1
                produto_id += 1

    if not out:
        raise SystemExit("Nenhum produto importado.")

    dest = os.path.join(ROOT, "produtos.json")
    with open(dest, "w", encoding="utf-8") as fh:
        json.dump(out, fh, ensure_ascii=False, indent=4)
    print(f"Salvo {len(out)} produtos em {dest}")


if __name__ == "__main__":
    main()
