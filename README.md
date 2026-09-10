# TikTok Shop Funnel — Cópia Local

Espelho idêntico do funil **loja.wwtiktokshop.com** (loja Creamy Skincare).

## Conteúdo

- Página da loja (`index.html`)
- Página de produto (`produto.php`)
- Carrinho (`cart.php`)
- Checkout (`checkout.php`)
- Todos os produtos, imagens, vídeos e comentários
- Configurações da loja (`loja.json`, `produtos.json`)

## Como rodar

```bash
cd tiktok-shop-funnel
python3 server.py
```

Abra no navegador: **http://localhost:8765**

## Legacy Ecom — PIX

Integração com a [API Legacy Ecom](https://developers.legacyecombrasil.com/docs/inicio-rapido).

### 1. Configure suas chaves

Edite `legacy-config.json`:

```json
{
  "public_key": "pk_live_xxxx",
  "secret_key": "sk_live_yyyy",
  "api_url": "https://api.legacyecombrasil.com",
  "webhook_url": "https://seu-dominio.com/webhooks/legacy",
  "is_physical_product": true
}
```

> KYC aprovado no Dashboard Legacy é obrigatório para produção.

### 2. Fluxo

1. Cliente finaliza checkout → `POST /pix_teste.php`
2. Servidor chama `POST /payin` na Legacy com PIX
3. QR Code é exibido em `payment.php`
4. Confirmação chega via webhook em `/webhooks/legacy`

### 3. Webhook local (teste)

Use [ngrok](https://ngrok.com) ou similar:

```bash
ngrok http 8765
# Cole a URL pública + /webhooks/legacy em webhook_url
```

## Observações

- **Rastreamento**: scripts do Rabbtify foram mantidos; remova se quiser.
- **Personalizar**: edite `loja.json` e `produtos.json`.

## Estrutura

```
tiktok-shop-funnel/
├── index.html          # Vitrine da loja
├── produto.php         # Página de produto
├── cart.php            # Carrinho
├── checkout.php        # Checkout
├── loja.json           # Config da loja
├── produtos.json       # Catálogo completo
├── uploads/            # Imagens e vídeos
├── assets/             # Ícones TikTok Shop
└── server.py           # Servidor local
```
