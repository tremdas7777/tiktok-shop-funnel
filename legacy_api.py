#!/usr/bin/env python3
"""Integração Legacy Ecom — PIX Payin."""
import base64
import json
import os
import re
import uuid
from urllib import error, request

ROOT = os.path.dirname(os.path.abspath(__file__))
CONFIG_PATH = os.path.join(ROOT, "legacy-config.json")


def load_config():
    if not os.path.exists(CONFIG_PATH):
        raise FileNotFoundError(
            "Arquivo legacy-config.json não encontrado. "
            "Copie legacy-config.example.json e preencha suas chaves."
        )
    with open(CONFIG_PATH, encoding="utf-8") as f:
        cfg = json.load(f)
    pk = str(cfg.get("public_key", "")).strip()
    sk = str(cfg.get("secret_key", "")).strip()
    if not pk or pk.startswith("pk_live_SUA") or not sk or sk.startswith("sk_live_SUA"):
        raise ValueError(
            "Configure public_key e secret_key em legacy-config.json "
            "(Dashboard Legacy → Integrações)."
        )
    return cfg


def only_digits(value):
    return re.sub(r"\D", "", str(value or ""))


def normalize_phone(phone):
    digits = only_digits(phone)
    if digits.startswith("55"):
        return digits
    if len(digits) in (10, 11):
        return "55" + digits
    return digits or "5511999999999"


def to_cents(value):
    try:
        return max(1, int(round(float(value) * 100)))
    except (TypeError, ValueError):
        return 0


def auth_header(public_key, secret_key):
    token = base64.b64encode(f"{public_key}:{secret_key}".encode()).decode()
    return f"Basic {token}"


def build_payin_payload(order, payer_ip="127.0.0.1"):
    comprador = order.get("comprador") or {}
    entrega = order.get("entrega") or {}
    carrinho = order.get("carrinho") or []
    frete = order.get("frete") or {}
    cfg = load_config()

    total = order.get("valor") or order.get("total") or 0
    amount_cents = to_cents(total)
    if amount_cents <= 0:
        raise ValueError("Valor inválido.")

    reference_id = f"pedido-{uuid.uuid4().hex[:12]}"
    items = []
    for item in carrinho:
        qty = max(1, int(item.get("quantidade") or item.get("qty") or 1))
        unit = to_cents(item.get("preco") or item.get("price") or 0)
        if unit <= 0:
            continue
        items.append({
            "title": str(item.get("titulo") or item.get("title") or "Produto")[:120],
            "quantity": qty,
            "unitPrice": unit,
        })

    frete_cents = to_cents(frete.get("preco") or frete.get("price") or 0)
    if frete_cents > 0:
        items.append({
            "title": str(frete.get("titulo") or "Frete"),
            "quantity": 1,
            "unitPrice": frete_cents,
        })

    if not items:
        items.append({
            "title": "Pedido TikTok Shop",
            "quantity": 1,
            "unitPrice": amount_cents,
        })

    document = only_digits(comprador.get("cpf"))
    if len(document) not in (11, 14):
        raise ValueError("CPF/CNPJ inválido.")

    payload = {
        "paymentMethod": "PIX",
        "amount": amount_cents,
        "referenceId": reference_id,
        "isPhysicalProduct": bool(cfg.get("is_physical_product", True)),
        "payerIp": payer_ip,
        "customer": {
            "name": str(comprador.get("nome") or "Cliente").strip(),
            "document": document,
            "email": str(comprador.get("email") or "cliente@email.com").strip(),
            "phone": normalize_phone(comprador.get("telefone")),
            "address": {
                "street": str(entrega.get("endereco") or "Rua").strip(),
                "number": str(entrega.get("numero") or "S/N").strip(),
                "zipCode": only_digits(entrega.get("cep")) or "01000000",
                "city": str(entrega.get("cidade") or "São Paulo").strip(),
                "state": str(entrega.get("estado") or "SP").strip()[:2].upper(),
            },
        },
        "items": items,
    }

    complemento = str(entrega.get("complemento") or "").strip()
    bairro = str(entrega.get("bairro") or "").strip()
    if complemento:
        payload["customer"]["address"]["complement"] = complemento
    if bairro:
        payload["customer"]["address"]["neighborhood"] = bairro

    webhook = str(cfg.get("webhook_url") or "").strip()
    if webhook and not webhook.startswith("https://SEU-DOMINIO"):
        payload["webhookUrl"] = webhook

    return payload


def create_pix_payin(order, payer_ip="127.0.0.1"):
    cfg = load_config()
    payload = build_payin_payload(order, payer_ip)
    api_url = str(cfg.get("api_url") or "https://api.legacyecombrasil.com").rstrip("/")
    url = f"{api_url}/payin"

    body = json.dumps(payload).encode("utf-8")
    req = request.Request(
        url,
        data=body,
        method="POST",
        headers={
            "Content-Type": "application/json",
            "Authorization": auth_header(cfg["public_key"], cfg["secret_key"]),
            "User-Agent": "tiktok-shop-funnel/1.0",
        },
    )

    try:
        with request.urlopen(req, timeout=45) as resp:
            raw = resp.read().decode("utf-8")
            data = json.loads(raw) if raw else {}
    except error.HTTPError as e:
        detail = e.read().decode("utf-8", errors="replace")
        try:
            err = json.loads(detail)
            message = err.get("message") or err.get("error") or detail
        except json.JSONDecodeError:
            message = detail or str(e)
        raise RuntimeError(f"Legacy API {e.code}: {message}") from e

    qrcode = (
        (data.get("pix") or {}).get("qrcode")
        or data.get("qrcode")
        or data.get("qr_code")
    )
    if not qrcode:
        raise RuntimeError("Legacy não retornou QR Code PIX.")

    return {
        "success": True,
        "qr_code": qrcode,
        "pix_qr_code": qrcode,
        "pixCode": qrcode,
        "copy_and_paste": qrcode,
        "referenceId": data.get("referenceId") or payload["referenceId"],
        "transactionId": data.get("id"),
        "id": data.get("id"),
        "status": data.get("status"),
        "amount": data.get("amount"),
        "pix": data.get("pix") or {"qrcode": qrcode},
        "legacy": data,
    }
