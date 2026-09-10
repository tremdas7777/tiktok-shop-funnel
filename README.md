# TikTok Shop Funnel

Loja ASICS idêntica (HTML/CSS/JS originais) no stack do **Lovable** (Vite + TanStack Start).

Repositório: https://github.com/tremdas7777/tiktok-shop-funnel

## Rodar local

```bash
cd tiktok-shop-funnel
npm install
npm run dev
```

Abra a URL que o Vite mostrar (geralmente `http://localhost:8080`). Home, produto, carrinho, checkout e PIX usam as mesmas páginas.

## Lovable

O Lovable **não importa** este repositório. O fluxo que funciona:

1. Crie um projeto novo em [lovable.dev](https://lovable.dev).
2. **Project settings → Git → GitHub** (o Lovable cria um repositório **novo**).
3. Envie este código para o repositório que o Lovable criou.
4. Em **Secrets**:
   - `LEGACY_PUBLIC_KEY`
   - `LEGACY_SECRET_KEY`
   - `LEGACY_API_URL` = `https://api.legacyecombrasil.com`
5. Clique em **Publish**.

O visual não foi reescrito em React: as páginas em `public/` são as mesmas da loja.

## PIX

No Lovable, as chaves ficam nos Secrets — nunca no código.

Localmente, o PIX ainda lê `legacy-config.json` (arquivo ignorado pelo git).
