#!/usr/bin/env python3
"""Servidor local do funil TikTok Shop com Legacy Ecom PIX."""
import http.server
import json
import os
import socketserver
import traceback
from urllib.parse import urlparse

import legacy_api

PORT = 8765
ROOT = os.path.dirname(os.path.abspath(__file__))
WEBHOOK_LOG = os.path.join(ROOT, "webhook-events.log")


class Handler(http.server.SimpleHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, directory=ROOT, **kwargs)

    def end_headers(self):
        self.send_header("Cache-Control", "no-cache, no-store, must-revalidate")
        super().end_headers()

    def guess_type(self, path):
        if path.endswith(".php"):
            return "text/html; charset=utf-8"
        return super().guess_type(path)

    def do_POST(self):
        path = urlparse(self.path).path

        if path in ("/pix_teste.php", "/api/pix"):
            self.handle_pix()
            return
        if path == "/webhooks/legacy":
            self.handle_webhook()
            return

        self.send_error(404, "Not Found")

    def handle_pix(self):
        try:
            length = int(self.headers.get("Content-Length", 0))
            raw = self.rfile.read(length).decode("utf-8") if length else "{}"
            order = json.loads(raw) if raw else {}
            payer_ip = self.client_address[0] or "127.0.0.1"
            result = legacy_api.create_pix_payin(order, payer_ip)
            self.send_json(200, result)
        except FileNotFoundError as e:
            self.send_json(500, {"success": False, "message": str(e)})
        except ValueError as e:
            self.send_json(400, {"success": False, "message": str(e)})
        except Exception as e:
            print("Erro PIX:", traceback.format_exc())
            self.send_json(502, {"success": False, "message": str(e)})

    def handle_webhook(self):
        try:
            length = int(self.headers.get("Content-Length", 0))
            raw = self.rfile.read(length).decode("utf-8") if length else ""
            with open(WEBHOOK_LOG, "a", encoding="utf-8") as f:
                f.write(raw + "\n---\n")
            self.send_json(200, {"received": True})
        except Exception:
            self.send_json(200, {"received": True})

    def send_json(self, status, payload):
        body = json.dumps(payload, ensure_ascii=False).encode("utf-8")
        self.send_response(status)
        self.send_header("Content-Type", "application/json; charset=utf-8")
        self.send_header("Content-Length", str(len(body)))
        self.end_headers()
        self.wfile.write(body)


if __name__ == "__main__":
    os.chdir(ROOT)
    with socketserver.TCPServer(("", PORT), Handler) as httpd:
        print(f"Funil + Legacy Ecom em http://localhost:{PORT}")
        print("Configure suas chaves em legacy-config.json")
        print("Pressione Ctrl+C para parar")
        httpd.serve_forever()
