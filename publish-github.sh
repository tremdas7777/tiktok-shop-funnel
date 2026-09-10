#!/bin/bash
set -euo pipefail

# Publicar tiktok-shop-funnel no GitHub
# Uso: ./publish-github.sh [nome-do-repo] [public|private]

REPO_NAME="${1:-tiktok-shop-funnel}"
VISIBILITY="${2:-private}"
ROOT="$(cd "$(dirname "$0")" && pwd)"

cd "$ROOT"

if [ ! -f legacy-config.example.json ]; then
  echo "Erro: execute este script dentro de tiktok-shop-funnel"
  exit 1
fi

if [ -f legacy-config.json ] && git check-ignore -q legacy-config.json 2>/dev/null; then
  echo "OK: legacy-config.json está no .gitignore (chaves não serão enviadas)"
fi

if [ ! -d .git ]; then
  git init -b main
fi

if ! git rev-parse HEAD >/dev/null 2>&1; then
  git add -A
  git commit -m "$(cat <<'EOF'
Initial commit: TikTok Shop funnel com Legacy Ecom PIX.

Espelho da vitrine Creamy Skincare com checkout, produtos, assets e integração Legacy Ecom.
EOF
)"
fi

if git remote get-url origin >/dev/null 2>&1; then
  echo "Remote origin já existe:"
  git remote -v
  echo "Faça push manualmente: git push -u origin main"
  exit 0
fi

if ! command -v gh >/dev/null 2>&1; then
  echo "Instale GitHub CLI: brew install gh"
  echo "Depois: gh auth login"
  exit 1
fi

gh auth status

gh repo create "$REPO_NAME" \
  --"$VISIBILITY" \
  --source=. \
  --remote=origin \
  --push \
  --description "Funil TikTok Shop com integração Legacy Ecom PIX"

echo ""
echo "Repositório publicado!"
gh repo view --web 2>/dev/null || gh repo view "$REPO_NAME"
