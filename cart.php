<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <title>Carrinho de Compras</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="mobile-fix.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        /* Cart item card — estilo marketplace */
        .cart-store-section { background:#fff; border-radius:16px; margin-bottom:12px; overflow:hidden; box-shadow:0 1px 6px rgba(0,0,0,0.07); }
        .cart-store-header { display:flex; align-items:center; gap:8px; padding:12px 14px 10px; border-bottom:1px solid #f3f4f6; }
        .cart-store-name { font-size:14px; font-weight:700; color:#111; flex:1; }
        .cart-store-chevron { color:#bbb; font-size:13px; }
        .cart-shipping-box { display:flex; align-items:center; gap:10px; background:#e8faf8; border-radius:14px; margin:10px 14px; padding:11px 14px; border:1.5px solid #b2e4df; }
        .cart-shipping-box-text { font-size:12px; font-weight:500; color:#007c6e; flex:1; line-height:1.4; }
        .cart-shipping-box-action { font-size:12px; font-weight:700; color:#009a85; white-space:nowrap; }
        .cart-item-row { display:flex; gap:10px; padding:10px 14px; align-items:flex-start; border-top:1px solid #f7f7f7; }
        .cart-item-img { width:80px; height:80px; min-width:80px; border-radius:8px; object-fit:contain; background:#f5f5f5; }
        .cart-item-body { flex:1; min-width:0; display:flex; flex-direction:column; gap:3px; }
        .cart-item-title { font-size:13px; color:#222; font-weight:500; line-height:1.35; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .cart-item-price-row { display:flex; align-items:center; gap:5px; flex-wrap:wrap; margin-top:2px; }
        .cart-item-price { font-size:18px; font-weight:800; color:#fe2d55; line-height:1; }
        .cart-item-price-frac { font-size:13px; font-weight:600; }
        .cart-item-ticket { width:14px; height:14px; object-fit:contain; vertical-align:middle; margin-left:1px; }
        .cart-item-oldprice { font-size:11px; color:#aaa; text-decoration:line-through; }
        .cart-item-badge { font-size:10px; font-weight:700; color:#fff; background:#fe2d55; border-radius:3px; padding:1px 5px; }
        .cart-item-sub { font-size:11px; color:#999; margin-top:1px; }
        .cart-qty { display:flex; align-items:center; border:1px solid #e5e7eb; border-radius:8px; overflow:hidden; margin-top:6px; align-self:flex-end; }
        .cart-qty button { width:30px; height:30px; border:none; background:#fff; font-size:18px; color:#333; cursor:pointer; display:flex; align-items:center; justify-content:center; font-weight:300; }
        .cart-qty span { width:30px; text-align:center; font-size:13px; font-weight:700; color:#111; border-left:1px solid #e5e7eb; border-right:1px solid #e5e7eb; height:30px; display:flex; align-items:center; justify-content:center; }
        .cart-item-remove { color:#ccc; font-size:13px; cursor:pointer; padding:2px 0 0 4px; }
        /* Banner topo */
        .cart-top-banner { display:flex; align-items:center; gap:10px; background:#e0f7f4; border-radius:0; padding:11px 16px; margin:0 -12px 14px -12px; border:1.5px solid #7cccc5; border-left:none; border-right:none; }
        .cart-top-banner-text { font-size:13px; font-weight:600; color:#007c6e; }
        /* Bloco Proteção do cliente (mesmo estilo do produto) */
        .protecao-cliente {
            background: #fdf8f0;
            border: 1px solid #e8dcc8;
            border-radius: 14px;
            padding: 13px 16px;
            margin: 10px 0 0 0;
            cursor: pointer;
        }
        .protecao-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .protecao-header .header-left {
            display: flex;
            align-items: center;
            gap: 7px;
            font-weight: 700;
            color: #7a5c1e;
            font-size: 14px;
        }
        .protecao-header .header-right { display: flex; align-items: center; }
        .protecao-lista {
            list-style: none;
            padding: 0; margin: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 7px 8px;
        }
        .protecao-lista li {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 500;
            color: #555;
        }
        .protecao-lista li .pcheck {
            color: #7a5c1e;
            font-size: 13px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .toast-center {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(.98);
            min-width: 240px;
            max-width: 90vw;
            background: #3a3a3a;
            color: #fff;
            padding: 16px 20px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .28);
            z-index: 9999;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            opacity: 0;
            transition: opacity .25s ease, transform .25s ease;
            pointer-events: none;
        }

        .toast-center.show {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }

        .toast-icon {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: inline-grid;
            place-items: center;
        }

        .toast-icon.success {
            background: #2ecc71;
        }

        .toast-icon.error {
            background: #e74c3c;
        }

        .toast-icon.info {
            background: #db3450;
        }

        .toast-text {
            font-weight: 600;
        }

        .toast-icon svg {
            width: 14px;
            height: 14px;
            stroke: #fff;
        }

    </style>
</head>

<body class="bg-gray-50">
    <header class="bg-white shadow-sm fixed top-0 left-0 right-0 z-50">
        <div class="max-w-3xl mx-auto px-4">
            <div class="flex justify-between items-center h-14">
                <a href="javascript:history.back()" class="text-gray-700 hover:text-gray-900 text-lg"><i class="fas fa-arrow-left"></i></a>
                <h1 class="text-base font-semibold text-gray-900">Carrinho (<span id="cart-count-header">0</span>)</h1>
                <div style="width:24px;"></div>
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-3 pt-16 pb-32">
        <div id="cart-items" class="space-y-3" style="display:none"></div>

        <div id="empty-cart" class="hidden flex flex-col items-center text-center py-12 gap-3">
            <img src="/uploads/icone_carrinhovazio.png" alt="Carrinho vazio" style="width:110px;height:auto;opacity:0.13;filter:grayscale(1);">
            <p style="font-size:17px;font-weight:700;color:#111;margin-top:4px;">Seu carrinho está vazio</p>
            <p style="font-size:13px;color:#888;margin-top:-4px;">Explore nossos produtos e adicione ao carrinho</p>
            <a href="index.php" style="margin-top:8px;padding:12px 32px;background:#e84565;color:#fff;border-radius:50px;font-size:14px;font-weight:700;text-decoration:none;display:inline-block;letter-spacing:.01em;">Começar a comprar</a>
        </div>

        <section id="secao-protecao" class="mt-6" style="display:none">
            <div class="protecao-cliente" onclick="abrirModalProtecao()">
                <div class="protecao-header">
                    <div class="header-left">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#7a5c1e"><path d="M12 2L4 5v6c0 5.25 3.5 10.15 8 11.35C16.5 21.15 20 16.25 20 11V5l-8-3z"/><path d="M9 12l2 2 4-4" stroke="#fff" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Proteção do cliente</span>
                    </div>
                    <div class="header-right">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="#7a5c1e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg>
                    </div>
                </div>
                <ul class="protecao-lista">
                    <li><span class="pcheck">✓</span>Devolução gratuita</li>
                    <li><span class="pcheck">✓</span>Reembolso se algo der errado</li>
                    <li><span class="pcheck">✓</span>Pagamento seguro</li>
                    <li><span class="pcheck">✓</span>Se o pedido não for enviado no prazo</li>
                </ul>
            </div>
        </section>

                <section id="recomendacoes" class="mt-6">
            <p style="font-size:15px;font-weight:700;color:#111;margin-bottom:12px;padding:0 2px;">Você também pode gostar</p>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">
                                <div onclick="window.location.href='produto.php?produto_id=19668'" style="background:#fff;border:1px solid #f3f4f6;border-radius:12px;padding:10px;display:flex;flex-direction:column;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.06);min-width:0;">
                    <div style="width:100%;height:140px;margin-bottom:8px;border-radius:6px;overflow:hidden;background:#f9f9f9;">
                        <img src="/uploads/produto_6a9e3ee356a262.89325799.webp" alt="Kit Rotina Pele Oleosa Creamy - Gel de Limpeza, Tônico Antioleosidade Ácido Salicílico, Gel Creme Hidratante Calmante Calming Cream e Protetor Solar FPS 60" style="width:100%;height:100%;object-fit:contain;display:block;" loading="lazy">
                    </div>
                    <div style="flex:1;display:flex;flex-direction:column;justify-content:space-between;">
                        <div>
                                                        <div style="display:flex;align-items:center;gap:4px;margin-bottom:3px;">
                                <img src="/uploads/tagoficial.png" style="height:12px;width:auto;display:block;flex-shrink:0;">                                <img src="/uploads/tagtorcer.png" style="height:12px;width:auto;display:block;flex-shrink:0;">                            </div>
                                                        <div style="font-size:12px;font-weight:600;color:#111;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:5px;height:32px;">Kit Rotina Pele Oleosa Creamy - Gel de Limpeza, Tônico Antioleosidade Ácido Salicílico, Gel Creme Hidratante Calmante Calming Cream e Protetor Solar FPS 60</div>
                            <div style="display:flex;flex-wrap:nowrap;gap:3px;margin-bottom:4px;overflow:hidden;align-items:center;height:15px;">
                                                                                                <span style="background:#ffd0d9;color:#d6003a;font-size:9.5px;font-weight:700;padding:0 5px;border-radius:3px;display:inline-flex;align-items:center;gap:2px;white-space:nowrap;flex-shrink:0;height:15px;box-sizing:border-box;">
                                    <img src='/uploads/bilhete.png?v=2' width='9' height='9' alt='' style='display:block;flex-shrink:0;'/>
                                    22% OFF
                                </span>
                                                            </div>
                            <div style="margin-top:3px;"><span style="background:#b2f0f5;color:#006875;font-size:9.5px;font-weight:700;padding:0 5px;border-radius:3px;display:inline-flex;align-items:center;height:15px;">Frete grátis</span></div>
                            <div style="display:flex;align-items:center;gap:3px;margin-bottom:4px;">
                                <span style="color:#f59e0b;font-size:11px;">★</span>
                                <span style="font-size:10px;color:#6b7280;">5 | 0 vendido(s)</span>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px;">
                            <div>
                                <div style="font-size:16px;font-weight:700;color:#fe2d55;line-height:1.1;margin-bottom:1px;">R$ 228,00</div>
                                <div style="font-size:10px;color:#9ca3af;text-decoration:line-through;">R$ 292,81</div>                            </div>
                            <button onclick="event.stopPropagation();openRecModal(19668)" style="background:#ffe0e6;border:none;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">
                                <img src="/uploads/carrinho-rosa.png?v=3" style="width:28px;height:28px;object-fit:contain;">
                            </button>
                        </div>
                    </div>
                </div>
                                <div onclick="window.location.href='produto.php?produto_id=19670'" style="background:#fff;border:1px solid #f3f4f6;border-radius:12px;padding:10px;display:flex;flex-direction:column;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.06);min-width:0;">
                    <div style="width:100%;height:140px;margin-bottom:8px;border-radius:6px;overflow:hidden;background:#f9f9f9;">
                        <img src="/uploads/produto_6a9e8c0e2ad5b6.66997392.webp" alt="Kit Antioleosidade - Limpador Fiacial, Gel Creme Hidratante Facil Calming Cream, Protetor Solar FPS 60" style="width:100%;height:100%;object-fit:contain;display:block;" loading="lazy">
                    </div>
                    <div style="flex:1;display:flex;flex-direction:column;justify-content:space-between;">
                        <div>
                                                        <div style="display:flex;align-items:center;gap:4px;margin-bottom:3px;">
                                <img src="/uploads/tagoficial.png" style="height:12px;width:auto;display:block;flex-shrink:0;">                                <img src="/uploads/tagtorcer.png" style="height:12px;width:auto;display:block;flex-shrink:0;">                            </div>
                                                        <div style="font-size:12px;font-weight:600;color:#111;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:5px;height:32px;">Kit Antioleosidade - Limpador Fiacial, Gel Creme Hidratante Facil Calming Cream, Protetor Solar FPS 60</div>
                            <div style="display:flex;flex-wrap:nowrap;gap:3px;margin-bottom:4px;overflow:hidden;align-items:center;height:15px;">
                                                                                                <span style="background:#ffd0d9;color:#d6003a;font-size:9.5px;font-weight:700;padding:0 5px;border-radius:3px;display:inline-flex;align-items:center;gap:2px;white-space:nowrap;flex-shrink:0;height:15px;box-sizing:border-box;">
                                    <img src='/uploads/bilhete.png?v=2' width='9' height='9' alt='' style='display:block;flex-shrink:0;'/>
                                    10% OFF
                                </span>
                                                            </div>
                            <div style="margin-top:3px;"><span style="background:#b2f0f5;color:#006875;font-size:9.5px;font-weight:700;padding:0 5px;border-radius:3px;display:inline-flex;align-items:center;height:15px;">Frete grátis</span></div>
                            <div style="display:flex;align-items:center;gap:3px;margin-bottom:4px;">
                                <span style="color:#f59e0b;font-size:11px;">★</span>
                                <span style="font-size:10px;color:#6b7280;">5 | 0 vendido(s)</span>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px;">
                            <div>
                                <div style="font-size:16px;font-weight:700;color:#fe2d55;line-height:1.1;margin-bottom:1px;">R$ 162,00</div>
                                <div style="font-size:10px;color:#9ca3af;text-decoration:line-through;">R$ 180,18</div>                            </div>
                            <button onclick="event.stopPropagation();openRecModal(19670)" style="background:#ffe0e6;border:none;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">
                                <img src="/uploads/carrinho-rosa.png?v=3" style="width:28px;height:28px;object-fit:contain;">
                            </button>
                        </div>
                    </div>
                </div>
                                <div onclick="window.location.href='produto.php?produto_id=19671'" style="background:#fff;border:1px solid #f3f4f6;border-radius:12px;padding:10px;display:flex;flex-direction:column;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.06);min-width:0;">
                    <div style="width:100%;height:140px;margin-bottom:8px;border-radius:6px;overflow:hidden;background:#f9f9f9;">
                        <img src="/uploads/produto_6a9e8f5c10d874.40721804.png" alt="Duo Antiacne - Hidrata &amp; Controla" style="width:100%;height:100%;object-fit:contain;display:block;" loading="lazy">
                    </div>
                    <div style="flex:1;display:flex;flex-direction:column;justify-content:space-between;">
                        <div>
                                                        <div style="display:flex;align-items:center;gap:4px;margin-bottom:3px;">
                                <img src="/uploads/tagoficial.png" style="height:12px;width:auto;display:block;flex-shrink:0;">                                <img src="/uploads/tagtorcer.png" style="height:12px;width:auto;display:block;flex-shrink:0;">                            </div>
                                                        <div style="font-size:12px;font-weight:600;color:#111;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:5px;height:32px;">Duo Antiacne - Hidrata &amp; Controla</div>
                            <div style="display:flex;flex-wrap:nowrap;gap:3px;margin-bottom:4px;overflow:hidden;align-items:center;height:15px;">
                                                                                                <span style="background:#ffd0d9;color:#d6003a;font-size:9.5px;font-weight:700;padding:0 5px;border-radius:3px;display:inline-flex;align-items:center;gap:2px;white-space:nowrap;flex-shrink:0;height:15px;box-sizing:border-box;">
                                    <img src='/uploads/bilhete.png?v=2' width='9' height='9' alt='' style='display:block;flex-shrink:0;'/>
                                    10% OFF
                                </span>
                                                            </div>
                            <div style="margin-top:3px;"><span style="background:#b2f0f5;color:#006875;font-size:9.5px;font-weight:700;padding:0 5px;border-radius:3px;display:inline-flex;align-items:center;height:15px;">Frete grátis</span></div>
                            <div style="display:flex;align-items:center;gap:3px;margin-bottom:4px;">
                                <span style="color:#f59e0b;font-size:11px;">★</span>
                                <span style="font-size:10px;color:#6b7280;">5 | 0 vendido(s)</span>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px;">
                            <div>
                                <div style="font-size:16px;font-weight:700;color:#fe2d55;line-height:1.1;margin-bottom:1px;">R$ 162,00</div>
                                <div style="font-size:10px;color:#9ca3af;text-decoration:line-through;">R$ 180,18</div>                            </div>
                            <button onclick="event.stopPropagation();openRecModal(19671)" style="background:#ffe0e6;border:none;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">
                                <img src="/uploads/carrinho-rosa.png?v=3" style="width:28px;height:28px;object-fit:contain;">
                            </button>
                        </div>
                    </div>
                </div>
                                <div onclick="window.location.href='produto.php?produto_id=19672'" style="background:#fff;border:1px solid #f3f4f6;border-radius:12px;padding:10px;display:flex;flex-direction:column;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.06);min-width:0;">
                    <div style="width:100%;height:140px;margin-bottom:8px;border-radius:6px;overflow:hidden;background:#f9f9f9;">
                        <img src="/uploads/produto_6a9e921c331e10.41003358.webp" alt="Duo para Poros - Ácido Glicólico + Ácido Mandélico" style="width:100%;height:100%;object-fit:contain;display:block;" loading="lazy">
                    </div>
                    <div style="flex:1;display:flex;flex-direction:column;justify-content:space-between;">
                        <div>
                                                        <div style="display:flex;align-items:center;gap:4px;margin-bottom:3px;">
                                <img src="/uploads/tagoficial.png" style="height:12px;width:auto;display:block;flex-shrink:0;">                                <img src="/uploads/tagtorcer.png" style="height:12px;width:auto;display:block;flex-shrink:0;">                            </div>
                                                        <div style="font-size:12px;font-weight:600;color:#111;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:5px;height:32px;">Duo para Poros - Ácido Glicólico + Ácido Mandélico</div>
                            <div style="display:flex;flex-wrap:nowrap;gap:3px;margin-bottom:4px;overflow:hidden;align-items:center;height:15px;">
                                                                                                <span style="background:#ffd0d9;color:#d6003a;font-size:9.5px;font-weight:700;padding:0 5px;border-radius:3px;display:inline-flex;align-items:center;gap:2px;white-space:nowrap;flex-shrink:0;height:15px;box-sizing:border-box;">
                                    <img src='/uploads/bilhete.png?v=2' width='9' height='9' alt='' style='display:block;flex-shrink:0;'/>
                                    19% OFF
                                </span>
                                                            </div>
                            <div style="margin-top:3px;"><span style="background:#b2f0f5;color:#006875;font-size:9.5px;font-weight:700;padding:0 5px;border-radius:3px;display:inline-flex;align-items:center;height:15px;">Frete grátis</span></div>
                            <div style="display:flex;align-items:center;gap:3px;margin-bottom:4px;">
                                <span style="color:#f59e0b;font-size:11px;">★</span>
                                <span style="font-size:10px;color:#6b7280;">5 | 0 vendido(s)</span>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px;">
                            <div>
                                <div style="font-size:16px;font-weight:700;color:#fe2d55;line-height:1.1;margin-bottom:1px;">R$ 135,98</div>
                                <div style="font-size:10px;color:#9ca3af;text-decoration:line-through;">R$ 168,40</div>                            </div>
                            <button onclick="event.stopPropagation();openRecModal(19672)" style="background:#ffe0e6;border:none;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">
                                <img src="/uploads/carrinho-rosa.png?v=3" style="width:28px;height:28px;object-fit:contain;">
                            </button>
                        </div>
                    </div>
                </div>
                                <div onclick="window.location.href='produto.php?produto_id=19673'" style="background:#fff;border:1px solid #f3f4f6;border-radius:12px;padding:10px;display:flex;flex-direction:column;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.06);min-width:0;">
                    <div style="width:100%;height:140px;margin-bottom:8px;border-radius:6px;overflow:hidden;background:#f9f9f9;">
                        <img src="/uploads/produto_6a9e93cf071171.50000371.webp" alt="Kit Completo Antioleosidade - Limpador Antioleosidade + Ácido Salicilico + Ácido Mandélico + Calming Cream + Protetor Solar" style="width:100%;height:100%;object-fit:contain;display:block;" loading="lazy">
                    </div>
                    <div style="flex:1;display:flex;flex-direction:column;justify-content:space-between;">
                        <div>
                                                        <div style="display:flex;align-items:center;gap:4px;margin-bottom:3px;">
                                <img src="/uploads/tagoficial.png" style="height:12px;width:auto;display:block;flex-shrink:0;">                                <img src="/uploads/tagtorcer.png" style="height:12px;width:auto;display:block;flex-shrink:0;">                            </div>
                                                        <div style="font-size:12px;font-weight:600;color:#111;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:5px;height:32px;">Kit Completo Antioleosidade - Limpador Antioleosidade + Ácido Salicilico + Ácido Mandélico + Calming Cream + Protetor Solar</div>
                            <div style="display:flex;flex-wrap:nowrap;gap:3px;margin-bottom:4px;overflow:hidden;align-items:center;height:15px;">
                                                                                                <span style="background:#ffd0d9;color:#d6003a;font-size:9.5px;font-weight:700;padding:0 5px;border-radius:3px;display:inline-flex;align-items:center;gap:2px;white-space:nowrap;flex-shrink:0;height:15px;box-sizing:border-box;">
                                    <img src='/uploads/bilhete.png?v=2' width='9' height='9' alt='' style='display:block;flex-shrink:0;'/>
                                    83% OFF
                                </span>
                                                            </div>
                            <div style="margin-top:3px;"><span style="background:#b2f0f5;color:#006875;font-size:9.5px;font-weight:700;padding:0 5px;border-radius:3px;display:inline-flex;align-items:center;height:15px;">Frete grátis</span></div>
                            <div style="display:flex;align-items:center;gap:3px;margin-bottom:4px;">
                                <span style="color:#f59e0b;font-size:11px;">★</span>
                                <span style="font-size:10px;color:#6b7280;">5 | 0 vendido(s)</span>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px;">
                            <div>
                                <div style="font-size:16px;font-weight:700;color:#fe2d55;line-height:1.1;margin-bottom:1px;">R$ 64,90</div>
                                <div style="font-size:10px;color:#9ca3af;text-decoration:line-through;">R$ 371,63</div>                            </div>
                            <button onclick="event.stopPropagation();openRecModal(19673)" style="background:#ffe0e6;border:none;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">
                                <img src="/uploads/carrinho-rosa.png?v=3" style="width:28px;height:28px;object-fit:contain;">
                            </button>
                        </div>
                    </div>
                </div>
                            </div>
        </section>
            </main>

    <footer id="rodape-carrinho" class="fixed bottom-0 left-0 right-0 bg-white" style="display:none;box-shadow:0 -2px 16px rgba(0,0,0,0.09);padding:10px 14px calc(10px + env(safe-area-inset-bottom));">
        <div class="max-w-3xl mx-auto" style="display:flex;align-items:center;gap:10px;">
            <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;cursor:pointer;" onclick="_toggleAll()">
                <span id="cart-cb-all" style="width:22px;height:22px;border-radius:50%;background:#fe2d55;border:2px solid #fe2d55;display:inline-flex;align-items:center;justify-content:center;transition:background .15s,border .15s;">
                    <svg width="11" height="11" viewBox="0 0 12 10" fill="none"><path d="M1 5l3.5 3.5L11 1" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span style="font-size:12px;font-weight:600;color:#333;">Tudo</span>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:baseline;gap:2px;">
                    <span style="font-size:12px;color:#fe2d55;font-weight:600;">R$</span>
                    <span id="cart-total" style="font-size:20px;font-weight:800;color:#fe2d55;line-height:1;">0,00</span>
                </div>
                <div style="font-size:11px;color:#009a85;font-weight:600;">Frete grátis</div>
            </div>
            <button onclick="finalizarCompra()" style="background:#fe2d55;color:#fff;border:none;border-radius:999px;padding:12px 20px;font-size:14px;font-weight:700;white-space:nowrap;cursor:pointer;flex-shrink:0;">
                Finalizar compra (<span id="cart-count-footer">0</span>)
            </button>
        </div>
    </footer>

    <script src="js/cart.js"></script>
    <script>
    // Carrega produtos do sistema para mapear detalhes no carrinho
    let produtosSistema = [];
    fetch('produtos.json')
        .then(resp => resp.json())
        .then(data => { produtosSistema = data; if (typeof renderCart === 'function') renderCart(); })
        .catch(() => { produtosSistema = []; });

    function parseValor(val) {
        if (typeof val === 'number') return val;
        if (typeof val === 'string') {
            const n = Number(val.replace(',', '.'));
            return isNaN(n) ? 0 : n;
        }
        return 0;
    }

    // Busca detalhes do produto pelo id/variação e inclui desconto/preço de comparação
    function getProdutoDetalhado(item) {
        if (!produtosSistema.length) return { ...item, variacaoInfo: item.variacaoInfo || '' };
        const prod = produtosSistema.find(p => String(p.id) === String(item.produtoId));
        if (!prod) return { ...item, variacaoInfo: item.variacaoInfo || '' };
        let variacao = null;
        if (item.variacaoId && prod.variacoes && prod.variacoes.length) {
            variacao = prod.variacoes.find(v => String(v.id) === String(item.variacaoId));
        }
        const tituloPreferido = (item.titulo && item.titulo.trim())
            ? item.titulo
            : (variacao ? (variacao.titulo || '') : (prod.titulo || ''));
        const precoPreferido = (typeof item.preco === 'number' && !isNaN(item.preco))
            ? item.preco
            : (variacao ? parseValor(variacao.preco) : parseValor(prod.preco));
        const precoComparacao = item.precoComparacao || item.preco_comparacao || (variacao ? parseValor(variacao.preco_comparacao) : parseValor(prod.preco_comparacao));
        const imagemPreferida = item.imagem || (variacao && variacao.imagem) || (prod.fotos && prod.fotos[0]) || '';
        const descontoInformado = item.desconto || item.desconto_percentual || (variacao ? variacao.desconto : prod.desconto);
        const descontoNumero = descontoInformado ? Number(String(descontoInformado).replace('%','').replace(',','.')) : null;
        const descontoCalc = (precoComparacao && precoComparacao > precoPreferido)
            ? Math.round((1 - (precoPreferido / precoComparacao)) * 100)
            : null;
        const descontoFinal = (descontoNumero && !isNaN(descontoNumero)) ? Math.round(descontoNumero) : descontoCalc;
        return {
            ...item,
            produtoTitulo: prod.titulo || item.produtoTitulo || '',
            titulo: tituloPreferido,
            preco: precoPreferido,
            precoComparacao: precoComparacao || null,
            desconto: descontoFinal,
            imagem: imagemPreferida,
            variacaoInfo: variacao ? (variacao.info || variacao.titulo || '') : (item.variacaoInfo || ''),
        };
    }
    </script>
    <script>
        function showCenterToast(message, type = 'success', duration = 2000) {
            const toast = document.createElement('div');
            toast.className = 'toast-center';
            const icons = {
                success: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="white"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`,
                error: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="white"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`,
                info: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="white"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>`
            };
            const iconEl = `<span class="toast-icon ${type}">${icons[type] || icons.info}</span>`;
            toast.innerHTML = `${iconEl}<span class="toast-text">${message}</span>`;
            document.body.appendChild(toast);
            requestAnimationFrame(() => toast.classList.add('show'));
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 250);
            }, duration);
        }

        function formatBRL(value) {
            const number = Number(value || 0);
            return number.toFixed(2).replace('.', ',');
        }

        // Set dos índices selecionados (por padrão todos)
        let _cartChecked = new Set();

        function _checkboxSVG() {
            return `<svg width="11" height="11" viewBox="0 0 12 10" fill="none"><path d="M1 5l3.5 3.5L11 1" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
        }
        function _cbEl(checked) {
            return `<span class="cart-cb" style="width:22px;height:22px;border-radius:50%;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:background .15s,border .15s;${checked ? 'background:#fe2d55;border:2px solid #fe2d55;' : 'background:#fff;border:2px solid #ccc;'}">${checked ? _checkboxSVG() : ''}</span>`;
        }
        function _updateCartTotals() {
            const totalEl = document.getElementById('cart-total');
            const countEl = document.getElementById('cart-count-footer');
            const allCbEl = document.getElementById('cart-cb-all');
            let total = 0, count = 0;
            cart.items.forEach((item, idx) => {
                if (_cartChecked.has(idx)) {
                    const d = getProdutoDetalhado(item);
                    total += Number(d.preco || 0) * Number(d.quantidade || 1);
                    count++;
                }
            });
            if (totalEl) totalEl.textContent = 'R$ ' + formatBRL(total);
            if (countEl) countEl.textContent = String(count);
            if (allCbEl) {
                const allChecked = cart.items.length > 0 && _cartChecked.size === cart.items.length;
                allCbEl.style.background = allChecked ? '#fe2d55' : '#fff';
                allCbEl.style.border = allChecked ? '2px solid #fe2d55' : '2px solid #ccc';
                allCbEl.innerHTML = allChecked ? _checkboxSVG() : '';
            }
        }

        function renderCart() {
            const container = document.getElementById('cart-items');
            const emptyCart = document.getElementById('empty-cart');
            const totalEl = document.getElementById('cart-total');
            const countEl = document.getElementById('cart-count-footer');

            if (window.cart && typeof window.cart.updateCartCount === 'function') {
                window.cart.updateCartCount();
            }

            if (!container || !emptyCart || !totalEl) return;

            container.innerHTML = '';

            if (cart.items.length === 0) {
                _cartChecked = new Set();
                container.style.display = 'none';
                emptyCart.classList.remove('hidden');
                totalEl.textContent = 'R$ 0,00';
                if (countEl) countEl.textContent = '0';
                const secProt = document.getElementById('secao-protecao');
                if (secProt) secProt.style.display = 'none';
                const rodape = document.getElementById('rodape-carrinho');
                if (rodape) rodape.style.display = 'none';
                return;
            }

            // Garante que novos itens entrem selecionados
            cart.items.forEach((_, idx) => { if (!_cartChecked.has(idx)) _cartChecked.add(idx); });
            // Remove índices que não existem mais
            for (const idx of _cartChecked) { if (idx >= cart.items.length) _cartChecked.delete(idx); }

            container.style.display = '';
            emptyCart.classList.add('hidden');
            const secProt2 = document.getElementById('secao-protecao');
            if (secProt2) secProt2.style.display = '';
            const rodape2 = document.getElementById('rodape-carrinho');
            if (rodape2) rodape2.style.display = '';

            // Banner topo frete
            const banner = document.createElement('div');
            banner.className = 'cart-top-banner';
            banner.innerHTML = `<img src="/uploads/carrofreteazul.png?v=2" style="width:30px;height:auto;object-fit:contain;flex-shrink:0;"><span class="cart-top-banner-text">Frete grátis em todos os produtos</span>`;
            container.appendChild(banner);

            // Uma seção por item
            cart.items.forEach((item, index) => {
                const d = getProdutoDetalhado(item);
                const tituloCompleto = (() => {
                    const base = d.produtoTitulo || '';
                    const varTit = d.titulo || '';
                    if (base && varTit && base !== varTit) return base + ' - ' + varTit;
                    return base || varTit || '';
                })();

                const precoNum = Number(d.preco || 0);
                const precoInt = Math.floor(precoNum);
                const precoCents = String(Math.round((precoNum - precoInt) * 100)).padStart(2, '0');
                const checked = _cartChecked.has(index);

                const section = document.createElement('div');
                section.className = 'cart-store-section';

                // Cabeçalho com checkbox funcional
                const storeHeader = document.createElement('div');
                storeHeader.className = 'cart-store-header';
                storeHeader.innerHTML = `
                    ${_cbEl(checked)}
                    <span class="cart-store-name">${tituloCompleto.split(' ').slice(0,3).join(' ')}</span>
                    <span class="cart-store-chevron"><i class="fas fa-chevron-right"></i></span>`;
                const cb = storeHeader.querySelector('.cart-cb');
                cb.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (_cartChecked.has(index)) { _cartChecked.delete(index); } else { _cartChecked.add(index); }
                    cb.style.background = _cartChecked.has(index) ? '#fe2d55' : '#fff';
                    cb.style.border = _cartChecked.has(index) ? '2px solid #fe2d55' : '2px solid #ccc';
                    cb.innerHTML = _cartChecked.has(index) ? _checkboxSVG() : '';
                    _updateCartTotals();
                });
                section.appendChild(storeHeader);

                // Box frete
                const shippingBox = document.createElement('div');
                shippingBox.className = 'cart-shipping-box';
                shippingBox.innerHTML = `<img src="/uploads/carrofreteazul.png?v=2" style="width:26px;height:auto;object-fit:contain;flex-shrink:0;"><span class="cart-shipping-box-text">Você economizou no frete com frete grátis!</span>`;
                section.appendChild(shippingBox);

                // Produto
                const row = document.createElement('div');
                row.className = 'cart-item-row';
                row.innerHTML = `
                    <img src="${d.imagem || ''}" alt="${tituloCompleto}" class="cart-item-img" loading="lazy" onerror="this.src=''">
                    <div class="cart-item-body">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:6px;">
                            <span class="cart-item-title">${tituloCompleto}</span>
                            <button onclick="cart.removeItem(${index})" class="cart-item-remove"><i class="fas fa-times"></i></button>
                        </div>
                        ${d.variacaoInfo ? `<span class="cart-item-sub">${d.variacaoInfo}</span>` : ''}
                        <div class="cart-item-price-row">
                            <span style="font-size:13px;font-weight:700;color:#fe2d55;">R$</span>
                            <span class="cart-item-price">${precoInt}<span class="cart-item-price-frac">,${precoCents}</span></span>
                            <img src="/uploads/bilhete.png?v=2" class="cart-item-ticket" alt="">
                            ${d.precoComparacao && d.precoComparacao > d.preco ? `<span class="cart-item-oldprice">R$ ${formatBRL(d.precoComparacao)}</span>` : ''}
                            ${d.desconto ? `<span class="cart-item-badge">-${d.desconto}%</span>` : ''}
                        </div>
                        <div class="cart-qty">
                            <button onclick="cart.updateQuantity(${index}, ${d.quantidade - 1})">−</button>
                            <span>${d.quantidade}</span>
                            <button onclick="cart.updateQuantity(${index}, ${d.quantidade + 1})">+</button>
                        </div>
                    </div>`;
                section.appendChild(row);
                container.appendChild(section);
            });

            _updateCartTotals();
        }

        function _toggleAll() {
            if (_cartChecked.size === cart.items.length) {
                _cartChecked.clear();
            } else {
                cart.items.forEach((_, idx) => _cartChecked.add(idx));
            }
            renderCart();
        }

        function finalizarCompra() {
            if (cart.items.length === 0) {
                showCenterToast('Adicione itens ao carrinho primeiro', 'error');
                return;
            }
            // Salva o carrinho na sessão para o checkout
            sessionStorage.setItem('checkoutCarrinho', JSON.stringify(cart.items));
            window.location.href = 'checkout.php';
        }

        // Inicializa o carrinho e renderiza
        document.addEventListener('DOMContentLoaded', () => {
            cart.init();
            renderCart();
        });

        // Caso outro script/aba altere o localStorage, atualiza a UI
        window.addEventListener('storage', (e) => {
            if (e.key === 'carrinho') {
                try { cart.items = JSON.parse(e.newValue || '[]'); } catch(_) { cart.items = []; }
                renderCart();
            }
        });
    </script>
    <script>
        // Bloqueia zoom por gesto ou ctrl + scroll para manter layout do carrinho
        document.addEventListener('wheel', function (e) {
            if (e.ctrlKey) {
                e.preventDefault();
            }
        }, { passive: false });

        document.addEventListener('gesturestart', function (e) {
            e.preventDefault();
        }, { passive: false });

        document.addEventListener('touchmove', function (e) {
            if (e.touches && e.touches.length > 1) {
                e.preventDefault();
            }
        }, { passive: false });
    </script>
    <script>
    let _countdownSecs = 20 * 60;
    function _fmtCountdown(s) {
        const m = String(Math.floor(s/60)).padStart(2,'0');
        const ss = String(s%60).padStart(2,'0');
        return '00:' + m + ':' + ss;
    }
    setInterval(function() {
        if (_countdownSecs > 0) _countdownSecs--;
        const t = _fmtCountdown(_countdownSecs);
        document.querySelectorAll('.oferta-timer').forEach(function(el){ el.textContent = t; });
    }, 1000);
    </script>
    <!-- meuModal completo com variações — idêntico ao index.php -->
    <style>
      .dots-line { position: relative; width: 44px; height: 12px; }
      .dots-line .dot { position: absolute; top: 2px; width: 10px; height: 10px; border-radius: 50%; opacity: .9; }
      .dots-line .dot.dot-red { left: 0; background: #fe2d55; animation: slide-right .9s ease-in-out infinite; }
      .dots-line .dot.dot-cyan { right: 0; background: #00f2ea; animation: slide-left .9s ease-in-out infinite; }
      @keyframes slide-right { 0% { transform: translateX(0); opacity:.6;} 50% { transform: translateX(18px); opacity:1;} 100% { transform: translateX(0); opacity:.6;} }
      @keyframes slide-left  { 0% { transform: translateX(0); opacity:.6;} 50% { transform: translateX(-18px); opacity:1;} 100% { transform: translateX(0); opacity:.6;} }
      .scrollbar-hide::-webkit-scrollbar { display: none; }
      .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
      #buy-positions {
        position: -webkit-sticky;
        position: sticky;
        bottom: 0; left: 0; right: 0;
        z-index: 30;
        background: transparent;
        box-shadow: none;
        padding-top: 18px;
        padding-right: 16px;
        padding-left: 16px;
        padding-bottom: 12px;
        padding-bottom: calc(12px + env(safe-area-inset-bottom));
        margin-top: 0;
        border-radius: 0px !important;
        text-transform: uppercase;
      }
      .variation-card {
        display: flex; flex-direction: column; align-items: center; gap: 0;
        width: 120px; flex: 0 0 auto; padding: 0;
        border: 1px solid #d1d5db; border-radius: 16px;
        background: #f3f4f6; box-shadow: none; position: relative;
        cursor: pointer;
        transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease;
        overflow: hidden;
      }
      .variation-card.is-size { width: auto; min-width: 48px; border-radius: 8px; background: #fff; border: 1.5px solid #d1d5db; }
      .variation-card.is-size .variation-image-wrap { display: none; }
      .variation-card.is-size .variation-label-wrap { min-height: unset; padding: 8px 14px; background: transparent; border-top: none; }
      .variation-card.is-size .variation-label { font-size: 13px; font-weight: 600; text-align: center; line-height: 1.3; display: block; overflow: visible; color: #111; }
      .variation-card.is-size.is-selected { border-color: #111; border-width: 2px; }
      .variation-card.is-size.is-selected::after { display: none; }
      .variation-row-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; }
      .variation-row-grid .variation-card { width: 100%; flex: none; }
      .variation-card:hover { box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
      .variation-card.is-selected { box-shadow: 0 3px 8px rgba(0,0,0,0.12); }
      .variation-card.is-selected::after { content:''; position:absolute; inset:0; border:2px solid #fb7185; border-radius:16px; pointer-events:none; }
      .variation-row { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 6px; margin-bottom: 8px; scroll-snap-type: x proximity; }
      .variation-row::-webkit-scrollbar { display: none; }
      .variation-row { -ms-overflow-style: none; scrollbar-width: none; }
      .variation-image-wrap { width:100%; height:100px; border-radius:0; background:transparent; border:none; display:flex; align-items:center; justify-content:center; overflow:hidden; position:relative; padding:6px; box-sizing:border-box; }
      .variation-image { width:100%; height:100%; object-fit:contain; }
      .variation-label-wrap { width:100%; background:#fff; border-top:1px solid #e5e7eb; padding:6px 4px 8px; display:flex; align-items:center; justify-content:center; min-height:32px; box-sizing:border-box; }
      .variation-label { font-size:12px; font-weight:600; color:#111827; }
      .variation-zoom { position:absolute; top:8px; left:8px; width:22px; height:22px; border-radius:999px; border:none; background:rgba(156,163,175,0.9); color:#fff; font-size:10px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; }
      .image-viewer { position:fixed; inset:0; display:none; align-items:center; justify-content:center; z-index:99999; }
      .image-viewer.show { display:flex; }
      .image-viewer-backdrop { position:absolute; inset:0; background:#000; }
      .image-viewer-content { position:relative; width:100%; height:100%; display:flex; align-items:center; justify-content:center; z-index:1; touch-action:pan-y; }
      .image-viewer-close { position:absolute; top:20px; left:16px; width:44px; height:44px; border:none; background:transparent; color:#fff; font-size:34px; cursor:pointer; display:flex; align-items:center; justify-content:center; line-height:1; }
      .image-viewer-nav { display:none !important; }
      .image-viewer-counter { position:absolute; top:20px; right:20px; color:#fff; font-size:16px; font-weight:600; }
      .image-viewer-img-wrap { background:#fff; display:flex; align-items:center; justify-content:center; width:88vw; max-width:480px; padding:20px; box-sizing:border-box; }
      .image-viewer-img-wrap img { max-width:100%; max-height:72vh; object-fit:contain; display:block; }
      .image-viewer-title { position:absolute; bottom:28px; left:0; right:0; text-align:center; color:#fff; font-size:15px; font-weight:400; }
    </style>

    <div id="imageViewer" class="image-viewer" aria-hidden="true">
      <div class="image-viewer-backdrop" id="imageViewerBackdrop"></div>
      <div class="image-viewer-content" id="imageViewerContent" role="dialog" aria-modal="true">
        <button type="button" class="image-viewer-close" id="imageViewerClose" aria-label="Fechar">&times;</button>
        <div id="imageViewerCounter" class="image-viewer-counter"></div>
        <div class="image-viewer-img-wrap">
          <img id="imageViewerImg" src="" alt="Imagem do produto">
        </div>
        <div id="imageViewerTitle" class="image-viewer-title"></div>
        <button type="button" class="image-viewer-nav prev" id="imageViewerPrev" aria-label="Anterior">&#10094;</button>
        <button type="button" class="image-viewer-nav next" id="imageViewerNext" aria-label="Proximo">&#10095;</button>
      </div>
    </div>

    <div id="meuModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-end justify-center hidden" style="z-index:9500;">
      <div class="bg-white p-4 w-full max-w-lg relative" style="position:fixed; bottom:0; left:0; right:0; overflow-y: auto; -webkit-overflow-scrolling: touch; max-height: 85vh; box-shadow: none; border-radius:0; margin:0 auto; max-width:480px; padding-bottom: calc(16px + env(safe-area-inset-bottom));">
        <button onclick="fecharModal()" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-xl font-bold" aria-label="Fechar">×</button>
        <div id="modal-loader" class="w-full flex items-center justify-center py-14">
          <div class="dots-line" aria-label="Carregando">
            <span class="dot dot-red"></span>
            <span class="dot dot-cyan"></span>
          </div>
        </div>
        <div id="modal-conteudo" class="hidden" style="padding-bottom:0;">
          <div style="display:flex;gap:12px;padding:4px 0 10px;">
            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==" alt="Produto" id="img-solts" style="width:80px;height:80px;min-width:80px;object-fit:contain;border-radius:8px;background:#f5f5f5;display:block;" loading="lazy">
            <div style="flex:1;min-width:0;">
              <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;flex-wrap:wrap;">
                <span id="div_ors_badge" style="background:#fe2d55;color:#fff;font-weight:700;padding:2px 7px;border-radius:6px;font-size:13px;flex-shrink:0;"></span>
                <span style="color:#fe2d55;font-size:13px;white-space:nowrap;">A partir de R$</span>
                <span id="div_ors_3" style="color:#fe2d55;font-size:22px;font-weight:800;line-height:1;"></span>
              </div>
              <div style="margin-bottom:6px;">
                <span id="div_ors_2" style="color:#aaa;font-size:13px;text-decoration:line-through;"></span>
              </div>
              <div id="modal-bilhete-chip" style="display:inline-flex;align-items:center;gap:4px;background:#fff0f3;border-radius:4px;padding:3px 8px;">
                <img src="/uploads/bilhete.png?v=2" style="height:11px;width:auto;display:block;">
                <span id="modal-bilhete-text" style="color:#fe2d55;font-size:11px;font-weight:700;">Desconto exclusivo</span>
              </div>
            </div>
          </div>
          <div id="modal-oferta-banner" style="display:none;margin-bottom:12px;">
            <span class="oferta-badge-wrap" style="display:inline-flex;align-items:center;border-radius:4px;overflow:hidden;flex-shrink:0;height:20px;background:#e8562a;">
              <img src="/uploads/oferta-relamapago.png?v=2" style="height:20px;width:auto;display:block;">
              <span class="oferta-timer" style="background:#fff0e8;color:#e8562a;font-size:11px;font-weight:800;padding:0 8px;height:100%;display:flex;align-items:center;">00:20:00</span>
            </span>
          </div>
          <div id="chatsw-variacoes" style="margin-top:4px;">
            <div id="titulo-variacoes"></div>
            <div id="grid-variacoes"></div>
          </div>
          <div id="buy-positions">
            <a href="javascript:void(0);" onclick="comprarAgora()" id="div_ors_4" class="w-full text-white text-center block" style="font-size:16px;font-weight:700;border-radius:999px;padding:14px;background:#fe2d55;outline:none;-webkit-tap-highlight-color:transparent;box-shadow:none;">Adicionar ao carrinho</a>
          </div>
        </div>
      </div>
    </div>

    <script>
    const _produtosData = [{"id":19668,"owner_user_id":316,"titulo":"Kit Rotina Pele Oleosa Creamy - Gel de Limpeza, Tônico Antioleosidade Ácido Salicílico, Gel Creme Hidratante Calmante Calming Cream e Protetor Solar FPS 60","preco":"228.00","preco_comparacao":"292.81","desconto":"20.00","categoria":null,"order_bump_ativo":0,"order_bump_produto_id":null,"promo_ativa":0,"promo_banner":null,"notas":"5","descricao":"O combo Antiacne para peles oleosas ou mistas conta com 5 fórmulas inteligentes que agem em sinergia para reduzir a formação de cravos e espinhas, melhorar a aparência dos poros e controlar a oleosidade.\r\n\r\nLimpeza da pele: O Gel de limpeza deve ser usado na rotina diurna e noturna. Aplique 1 pump sobre a pele úmida e massageie até obter uma espuma leve, enxaguando em seguida.\r\n\r\nTonificação: O Ácido Salicílico pode ser usado de dia e à noite. Logo após a limpeza, aplique de 5 a 10 gotas do produto sobre a pele do rosto, pescoço e\/ou colo, se desejar. Espalhe com as mãos.\r\n\r\nHidratação: O Sérum Hidratante pode ser usado na rotina diurna e noturna. Aplique sobre a pele seca sempre que desejar, espalhando até a absorção completa\r\nProteção: O Protetor Solar Watery Lotion é de uso diurno. Agite o produto e aplique abundantemente antes da exposição ao sol sobre a pele seca. Reaplique após sudorese intensa, nadar ou banhar-se, secar-se com toalha e durante a exposição ao sol. Se a quantidade aplicada não for adequada, o nível de proteção será significativamente reduzido. É necessária a reaplicação do produto para manter a sua efetividade.\r\n\r\nTratamento profundo: O Ácido Mandélico é de uso noturno. Aplique sobre a pele seca e preferencialmente hidratada, evitando a região dos olhos, os cantos do nariz e da boca. Espalhe 1 ou 2 pumps sobre a pele do rosto, pescoço e\/ou do colo, se desejar. No início do uso, recomenda-se usar em noites alternadas até que a pele não apresente nenhum sinal de irritação","especificacoes":"","diferenciais":"","garantia":"","fotos":["\/uploads\/produto_6a9e3ee356a262.89325799.webp","\/uploads\/produto_6a9e4a9038f9b5.76534093.webp","\/uploads\/produto_6a9e4b26bacb41.85227697.webp","\/uploads\/produto_6a9e4b9ab624f3.07150051.webp"],"videos":[{"url":"\/uploads\/vcv_video_6a9e3d4f467e73.17805414.mp4","autor":"Goxtosa Consumista","avatar":"\/uploads\/vcv_avatar_6a9e3de9ae0be8.32565341.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d7c3b7e45.76448650.mp4","autor":"By.marianam","avatar":"\/uploads\/vcv_avatar_6a9e3dff4cd365.40410118.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d5c029727.78264602.mp4","autor":"Wallessa Gabriela","avatar":"\/uploads\/vcv_avatar_6a9e3e1df12286.00552065.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d8030e9b7.04861517.mp4","autor":"Top Ofertas","avatar":"\/uploads\/vcv_avatar_6a9e3e409212a0.26461684.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d83131dd9.64249525.mp4","autor":"Leticia Nunes","avatar":"\/uploads\/vcv_avatar_6a9e3e5a427c91.93094118.jpg"},{"url":"\/uploads\/vcv_video_6a9e3e97ba4339.21268858.mp4","autor":"Top Ofertas","avatar":"\/uploads\/vcv_avatar_6a9e3e6e94b566.37428737.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d8d35ee83.89280456.mp4","autor":"Goxtosa Consumista","avatar":"\/uploads\/vcv_avatar_6a9e3e8814b904.24768369.jpeg"}],"frete":"","entrega":"","oferta_termina_em":"","recomendacoes":null,"modelo_landing":"modelo4","avatar_comentario":null,"status":"ativo","meta_title":null,"meta_description":null,"meta_keywords":null,"estoque_atual":0,"estoque_minimo":0,"estoque_maximo":null,"sku":null,"nome_comentario":"","quantidade_produtos":3141,"created_at":"2026-09-07 01:34:36","updated_at":"2026-09-07 02:28:58","oferta_relampago":{"ativo":false,"horas":8,"ultimas":5},"variacoes":[{"id":171429,"owner_user_id":316,"produto_id":19668,"tipo":"cor","titulo":"Kit Rotina Pele Oleosa Creamy - Gel de Limpeza, Tônico Antioleosidade Ácido Salicílico, Gel Creme Hidratante Calmante Calming Cream e Protetor Solar F","preco":"228.00","preco_comparacao":"292.81","desconto":"22.13","info":"Atributo: Cor","link_checkout":"","imagem":"\/uploads\/variacao_6a9e949de04c75.48899749.webp","created_at":"2026-09-07 07:40:29"}],"comentarios":[{"id":165944,"owner_user_id":316,"produto_id":19668,"nome":"fe****a","foto_perfil":"\/uploads\/comentario_perfil_6a9e84ae6a3224.00614875.jpg","descricao":"Tipo de pele: Meu tipo de pele é oleoso Então, vou começar a testar os produtos e ver como ele se comporta na minha Pele, Eu não consegui Colocar fotos do meu rosto pra mostrar como está, Mas eu volto pra contar! E gente chegou em apenas três dias. Foi muito rápido.. Muito rápido mesmo, vale a pena!!","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e84ae6a6563.08212860.webp\",\"\\\/uploads\\\/comentario_foto_6a9e84ae6a7246.77443428.webp\",\"\\\/uploads\\\/comentario_foto_6a9e84ae6a7cb0.88161447.webp\",\"\\\/uploads\\\/comentario_foto_6a9e84ae6a8693.32741701.webp\",\"\\\/uploads\\\/comentario_foto_6a9e84ae6a8fc2.90094354.webp\"]","videos":null,"created_at":"2026-09-07 06:32:30"},{"id":165945,"owner_user_id":316,"produto_id":19668,"nome":"ju***a","foto_perfil":"\/uploads\/comentario_perfil_6a9e8519aa0ab8.86200565.png","descricao":"Amei, peguei em uma promoção maravilhosa, chegou super rápido ❤️","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8519aa2337.01566849.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8519aa3039.33599754.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8519aa3bc6.75152318.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8519aa46c6.97679951.webp\"]","videos":null,"created_at":"2026-09-07 06:34:17"},{"id":165947,"owner_user_id":316,"produto_id":19668,"nome":"A**e B**a","foto_perfil":"\/uploads\/comentario_perfil_6a9e8591c85570.42191727.jpg","descricao":"Chegou super rápido! Só achei pouca a proteção para os produtos como está na foto A caixa do hidratante veio danificada e até parece que foi usado e tem pouco produto. Mas tirando isso veio tudo certinho e estou bem feliz com minhas compras e voltarei para comprar mais!!!","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8591c884f2.95460003.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8591c893a6.49829463.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8591c8a156.20148896.webp\"]","videos":null,"created_at":"2026-09-07 06:36:17"},{"id":165948,"owner_user_id":316,"produto_id":19668,"nome":"e**","foto_perfil":"\/uploads\/comentario_perfil_6a9e860ccd1743.22688436.jpg","descricao":"Aí na foto ele está junto com mais algumas coisas q ganhei no dia do meu aniversário, mas muito bons todos os produtos recomendo!","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e860ccd4c06.04757907.webp\",\"\\\/uploads\\\/comentario_foto_6a9e860ccd5970.32020349.webp\",\"\\\/uploads\\\/comentario_foto_6a9e860ccd63a8.82607151.webp\"]","videos":null,"created_at":"2026-09-07 06:38:20"},{"id":165949,"owner_user_id":316,"produto_id":19668,"nome":"is**a","foto_perfil":"\/uploads\/comentario_perfil_6a9e869bd157c2.41549342.jpeg","descricao":"Vamos ver se vai trazer melhorias para meu rosto, eu espero que sim de verdade. Porque essa marca só ouvi elogios.","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e869bd21130.94920426.webp\",\"\\\/uploads\\\/comentario_foto_6a9e869bd22465.97725887.webp\",\"\\\/uploads\\\/comentario_foto_6a9e869bd22f82.01040402.webp\"]","videos":null,"created_at":"2026-09-07 06:40:43"},{"id":165950,"owner_user_id":316,"produto_id":19668,"nome":"A**✨","foto_perfil":"\/uploads\/comentario_perfil_6a9e871470ad26.45018858.jpg","descricao":"Gostei bastante estou fazendo tratamento facial minha pele já deu uma melhorada","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e871470ecf0.16531689.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8714710758.66931748.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8714711885.62609333.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8714712726.82375940.webp\",\"\\\/uploads\\\/comentario_foto_6a9e87147132f8.79374677.webp\"]","videos":null,"created_at":"2026-09-07 06:42:44"},{"id":165951,"owner_user_id":316,"produto_id":19668,"nome":"s**_","foto_perfil":"\/uploads\/comentario_perfil_6a9e8763c15081.51998402.jpg","descricao":"O kit completo venho certinho qualidade maravilhos","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8763c16ee9.70837119.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8763c17ee9.77657336.webp\"]","videos":null,"created_at":"2026-09-07 06:44:03"},{"id":165952,"owner_user_id":316,"produto_id":19668,"nome":"K**n","foto_perfil":"\/uploads\/comentario_perfil_6a9e87f07ef260.84538063.jpg","descricao":"Amei, chegou super rapidinho comprei na promoção, produtos maravilhos bem embalados...🥰","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e87f07fb9b3.56455563.webp\",\"\\\/uploads\\\/comentario_foto_6a9e87f07fc956.36635884.webp\",\"\\\/uploads\\\/comentario_foto_6a9e87f07fd582.47776929.webp\",\"\\\/uploads\\\/comentario_foto_6a9e87f07fe357.31429306.webp\"]","videos":null,"created_at":"2026-09-07 06:46:24"},{"id":165953,"owner_user_id":316,"produto_id":19668,"nome":"T**a P**s","foto_perfil":"\/uploads\/comentario_perfil_6a9e885f53da61.10306569.jpg","descricao":"Entrega super rápida, amei os produtos, são pequeno mas cabem em qualquer lugar","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e885f53f531.15256770.webp\",\"\\\/uploads\\\/comentario_foto_6a9e885f540331.18107088.webp\",\"\\\/uploads\\\/comentario_foto_6a9e885f540ec2.04803303.webp\",\"\\\/uploads\\\/comentario_foto_6a9e885f541879.60292925.webp\"]","videos":null,"created_at":"2026-09-07 06:48:15"},{"id":165954,"owner_user_id":316,"produto_id":19668,"nome":"N**a D**e","foto_perfil":"\/uploads\/comentario_perfil_6a9e88c64f1fe3.22255701.jpg","descricao":"Muito bom amei muito! Comprem sem medo, vou comprar de novo","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e88c64f38d1.07432108.webp\",\"\\\/uploads\\\/comentario_foto_6a9e88c64f4813.95153809.webp\"]","videos":null,"created_at":"2026-09-07 06:49:58"},{"id":165955,"owner_user_id":316,"produto_id":19668,"nome":"n**a d** l**ê","foto_perfil":"\/uploads\/comentario_perfil_6a9e893c32a663.19822258.jpg","descricao":"Eu ainda não usei,mas gostei muito, vieram bem embalados pode. O hidratante veio com a caixinha aberta um pouco amassada mas fora isso adorei","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e893c32c0b9.87780021.webp\",\"\\\/uploads\\\/comentario_foto_6a9e893c32ce19.82292311.webp\",\"\\\/uploads\\\/comentario_foto_6a9e893c32d8f3.47205547.webp\",\"\\\/uploads\\\/comentario_foto_6a9e893c32e223.42702770.webp\"]","videos":null,"created_at":"2026-09-07 06:51:56"},{"id":165956,"owner_user_id":316,"produto_id":19668,"nome":"D**a G**s","foto_perfil":"\/uploads\/comentario_perfil_6a9e89bb2b42a2.16385386.jpg","descricao":"Chegou super rápido, são meus primeiros produtos da marca,so deu pra comprar nessa promoção de 59,90 kkkkkkkkk","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e89bb2b5d53.54464098.webp\",\"\\\/uploads\\\/comentario_foto_6a9e89bb2b69a0.18411538.webp\",\"\\\/uploads\\\/comentario_foto_6a9e89bb2b7460.82215424.webp\",\"\\\/uploads\\\/comentario_foto_6a9e89bb2b7dc4.48423523.webp\"]","videos":null,"created_at":"2026-09-07 06:54:03"},{"id":165957,"owner_user_id":316,"produto_id":19668,"nome":"B**s","foto_perfil":"\/uploads\/comentario_perfil_6a9e8a343dc095.97386883.jpg","descricao":"promoção Maravilhosa,e o kit é perfeito uma semana já mudou minha pele Tipo de pele: Mista, oleosa no nariz e testa","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8a343e5e19.85946963.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8a343e70b7.97904087.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8a343e7d31.31591884.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8a343e88b4.52874266.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8a343e9300.85479131.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8a343e9c88.37331281.webp\"]","videos":null,"created_at":"2026-09-07 06:56:04"}]},{"id":19670,"owner_user_id":316,"titulo":"Kit Antioleosidade - Limpador Fiacial, Gel Creme Hidratante Facil Calming Cream, Protetor Solar FPS 60","preco":"162.00","preco_comparacao":"180.18","desconto":"10.00","categoria":null,"order_bump_ativo":0,"order_bump_produto_id":null,"promo_ativa":0,"promo_banner":null,"notas":"5","descricao":"O combo Antiacne para peles oleosas ou mistas conta com 5 fórmulas inteligentes que agem em sinergia para reduzir a formação de cravos e espinhas, melhorar a aparência dos poros e controlar a oleosidade.\r\n\r\nLimpeza da pele: O Gel de limpeza deve ser usado na rotina diurna e noturna. Aplique 1 pump sobre a pele úmida e massageie até obter uma espuma leve, enxaguando em seguida.\r\n\r\nTonificação: O Ácido Salicílico pode ser usado de dia e à noite. Logo após a limpeza, aplique de 5 a 10 gotas do produto sobre a pele do rosto, pescoço e\/ou colo, se desejar. Espalhe com as mãos.\r\n\r\nHidratação: O Sérum Hidratante pode ser usado na rotina diurna e noturna. Aplique sobre a pele seca sempre que desejar, espalhando até a absorção completa\r\nProteção: O Protetor Solar Watery Lotion é de uso diurno. Agite o produto e aplique abundantemente antes da exposição ao sol sobre a pele seca. Reaplique após sudorese intensa, nadar ou banhar-se, secar-se com toalha e durante a exposição ao sol. Se a quantidade aplicada não for adequada, o nível de proteção será significativamente reduzido. É necessária a reaplicação do produto para manter a sua efetividade.\r\n\r\nTratamento profundo: O Ácido Mandélico é de uso noturno. Aplique sobre a pele seca e preferencialmente hidratada, evitando a região dos olhos, os cantos do nariz e da boca. Espalhe 1 ou 2 pumps sobre a pele do rosto, pescoço e\/ou do colo, se desejar. No início do uso, recomenda-se usar em noites alternadas até que a pele não apresente nenhum sinal de irritação","especificacoes":"","diferenciais":"","garantia":"","fotos":["\/uploads\/produto_6a9e8c0e2ad5b6.66997392.webp","\/uploads\/produto_6a9e8c15376e34.74283237.webp","\/uploads\/produto_6a9e8c1b382993.92687442.webp","\/uploads\/produto_6a9e8c1f87a9b8.11035837.webp"],"videos":[{"url":"\/uploads\/vcv_video_6a9e3d4f467e73.17805414.mp4","autor":"Goxtosa Consumista","avatar":"\/uploads\/vcv_avatar_6a9e3de9ae0be8.32565341.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d7c3b7e45.76448650.mp4","autor":"By.marianam","avatar":"\/uploads\/vcv_avatar_6a9e3dff4cd365.40410118.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d5c029727.78264602.mp4","autor":"Wallessa Gabriela","avatar":"\/uploads\/vcv_avatar_6a9e3e1df12286.00552065.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d8030e9b7.04861517.mp4","autor":"Top Ofertas","avatar":"\/uploads\/vcv_avatar_6a9e3e409212a0.26461684.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d83131dd9.64249525.mp4","autor":"Leticia Nunes","avatar":"\/uploads\/vcv_avatar_6a9e3e5a427c91.93094118.jpg"},{"url":"\/uploads\/vcv_video_6a9e3e97ba4339.21268858.mp4","autor":"Top Ofertas","avatar":"\/uploads\/vcv_avatar_6a9e3e6e94b566.37428737.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d8d35ee83.89280456.mp4","autor":"Goxtosa Consumista","avatar":"\/uploads\/vcv_avatar_6a9e3e8814b904.24768369.jpeg"}],"frete":"","entrega":"","oferta_termina_em":"","recomendacoes":null,"modelo_landing":"modelo4","avatar_comentario":null,"status":"ativo","meta_title":null,"meta_description":null,"meta_keywords":null,"estoque_atual":0,"estoque_minimo":0,"estoque_maximo":null,"sku":null,"nome_comentario":"","quantidade_produtos":3141,"created_at":"2026-09-07 06:59:36","updated_at":"2026-09-07 07:23:06","oferta_relampago":{"ativo":false,"horas":8,"ultimas":5},"variacoes":[{"id":171423,"owner_user_id":316,"produto_id":19670,"tipo":"cor","titulo":"Kit Antioleosidade","preco":"162.00","preco_comparacao":"180.18","desconto":"10.09","info":"Atributo: Cor","link_checkout":"","imagem":"\/uploads\/variacao_6a9e8dfb6dd899.11767210.webp","created_at":"2026-09-07 07:12:11"}],"comentarios":[{"id":165958,"owner_user_id":316,"produto_id":19670,"nome":"fe****a","foto_perfil":"\/uploads\/comentario_perfil_6a9e8b080e4908.81328383.jpg","descricao":"Tipo de pele: Meu tipo de pele é oleoso Então, vou começar a testar os produtos e ver como ele se comporta na minha Pele, Eu não consegui Colocar fotos do meu rosto pra mostrar como está, Mas eu volto pra contar! E gente chegou em apenas três dias. Foi muito rápido.. Muito rápido mesmo, vale a pena!!","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8b080e5893.41650819.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b080e5ff6.67223241.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b080e6717.81566671.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b080e6da4.48371710.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b080e7867.14865918.webp\"]","videos":null,"created_at":"2026-09-07 06:59:36"},{"id":165959,"owner_user_id":316,"produto_id":19670,"nome":"ju***a","foto_perfil":"\/uploads\/comentario_perfil_6a9e8b080ebe04.09251149.png","descricao":"Amei, peguei em uma promoção maravilhosa, chegou super rápido ❤️","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8b080efc84.69690526.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b080f0361.10836691.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b080f0a61.96673711.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b080f12c6.68340938.webp\"]","videos":null,"created_at":"2026-09-07 06:59:36"},{"id":165960,"owner_user_id":316,"produto_id":19670,"nome":"A**e B**a","foto_perfil":"\/uploads\/comentario_perfil_6a9e8b080f55f2.46590306.jpg","descricao":"Chegou super rápido! Só achei pouca a proteção para os produtos como está na foto A caixa do hidratante veio danificada e até parece que foi usado e tem pouco produto. Mas tirando isso veio tudo certinho e estou bem feliz com minhas compras e voltarei para comprar mais!!!","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8b080f5fb1.38145737.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b080f6653.17359775.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b080f6d21.24326583.webp\"]","videos":null,"created_at":"2026-09-07 06:59:36"},{"id":165961,"owner_user_id":316,"produto_id":19670,"nome":"e**","foto_perfil":"\/uploads\/comentario_perfil_6a9e8b080fa4c1.00098932.jpg","descricao":"Aí na foto ele está junto com mais algumas coisas q ganhei no dia do meu aniversário, mas muito bons todos os produtos recomendo!","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8b080fafd7.97393883.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b080fb810.37997190.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b080fbde7.07218148.webp\"]","videos":null,"created_at":"2026-09-07 06:59:36"},{"id":165962,"owner_user_id":316,"produto_id":19670,"nome":"is**a","foto_perfil":"\/uploads\/comentario_perfil_6a9e8b080ffef2.16126303.jpeg","descricao":"Vamos ver se vai trazer melhorias para meu rosto, eu espero que sim de verdade. Porque essa marca só ouvi elogios.","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8b08104c13.84568860.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b08105400.03985801.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b08105a73.32872884.webp\"]","videos":null,"created_at":"2026-09-07 06:59:36"},{"id":165963,"owner_user_id":316,"produto_id":19670,"nome":"A**✨","foto_perfil":"\/uploads\/comentario_perfil_6a9e8b081099c5.20029975.jpg","descricao":"Gostei bastante estou fazendo tratamento facial minha pele já deu uma melhorada","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8b0810a339.23214860.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b0810aa24.78112624.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b0810b070.72772844.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b0810b696.59473428.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b0810be84.05443887.webp\"]","videos":null,"created_at":"2026-09-07 06:59:36"},{"id":165964,"owner_user_id":316,"produto_id":19670,"nome":"s**_","foto_perfil":"\/uploads\/comentario_perfil_6a9e8b0810fdf8.46535214.jpg","descricao":"O kit completo venho certinho qualidade maravilhos","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8b08110829.72011667.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b08111304.26841604.webp\"]","videos":null,"created_at":"2026-09-07 06:59:36"},{"id":165965,"owner_user_id":316,"produto_id":19670,"nome":"K**n","foto_perfil":"\/uploads\/comentario_perfil_6a9e8b08115081.95200785.jpg","descricao":"Amei, chegou super rapidinho comprei na promoção, produtos maravilhos bem embalados...🥰","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8b08115956.50830411.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b08115fd0.55890590.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b08116cc8.21487096.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b08117408.69586831.webp\"]","videos":null,"created_at":"2026-09-07 06:59:36"},{"id":165966,"owner_user_id":316,"produto_id":19670,"nome":"T**a P**s","foto_perfil":"\/uploads\/comentario_perfil_6a9e8b0811c382.77671794.jpg","descricao":"Entrega super rápida, amei os produtos, são pequeno mas cabem em qualquer lugar","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8b0811cee2.29477255.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b0811d868.59631034.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b0811dff7.65698472.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b0811e935.99910111.webp\"]","videos":null,"created_at":"2026-09-07 06:59:36"},{"id":165967,"owner_user_id":316,"produto_id":19670,"nome":"N**a D**e","foto_perfil":"\/uploads\/comentario_perfil_6a9e8b08122bf2.04632615.jpg","descricao":"Muito bom amei muito! Comprem sem medo, vou comprar de novo","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8b081235b3.75162554.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b08123f43.30767232.webp\"]","videos":null,"created_at":"2026-09-07 06:59:36"},{"id":165968,"owner_user_id":316,"produto_id":19670,"nome":"n**a d** l**ê","foto_perfil":"\/uploads\/comentario_perfil_6a9e8b08127ee9.20425873.jpg","descricao":"Eu ainda não usei,mas gostei muito, vieram bem embalados pode. O hidratante veio com a caixinha aberta um pouco amassada mas fora isso adorei","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8b08128a11.88070157.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b08129363.71906626.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b08129aa9.73546009.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b0812a182.96149758.webp\"]","videos":null,"created_at":"2026-09-07 06:59:36"},{"id":165969,"owner_user_id":316,"produto_id":19670,"nome":"D**a G**s","foto_perfil":"\/uploads\/comentario_perfil_6a9e8b0812ef06.46405169.jpg","descricao":"Chegou super rápido, são meus primeiros produtos da marca,so deu pra comprar nessa promoção de 59,90 kkkkkkkkk","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8b0812faa7.86333479.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b081304e7.01026889.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b08130ed4.65500555.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b081316a8.97955522.webp\"]","videos":null,"created_at":"2026-09-07 06:59:36"},{"id":165970,"owner_user_id":316,"produto_id":19670,"nome":"B**s","foto_perfil":"\/uploads\/comentario_perfil_6a9e8b08135961.20826534.jpg","descricao":"promoção Maravilhosa,e o kit é perfeito uma semana já mudou minha pele Tipo de pele: Mista, oleosa no nariz e testa","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8b08136352.47111674.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b08136ce8.63598637.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b08137ab8.05269187.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b08138298.34480094.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b08138919.80242420.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8b08138e90.85888379.webp\"]","videos":null,"created_at":"2026-09-07 06:59:36"}]},{"id":19671,"owner_user_id":316,"titulo":"Duo Antiacne - Hidrata & Controla","preco":"162.00","preco_comparacao":"180.18","desconto":"10.00","categoria":null,"order_bump_ativo":0,"order_bump_produto_id":null,"promo_ativa":0,"promo_banner":null,"notas":"5","descricao":"O combo Antiacne para peles oleosas ou mistas conta com 5 fórmulas inteligentes que agem em sinergia para reduzir a formação de cravos e espinhas, melhorar a aparência dos poros e controlar a oleosidade.\r\n\r\nLimpeza da pele: O Gel de limpeza deve ser usado na rotina diurna e noturna. Aplique 1 pump sobre a pele úmida e massageie até obter uma espuma leve, enxaguando em seguida.\r\n\r\nTonificação: O Ácido Salicílico pode ser usado de dia e à noite. Logo após a limpeza, aplique de 5 a 10 gotas do produto sobre a pele do rosto, pescoço e\/ou colo, se desejar. Espalhe com as mãos.\r\n\r\nHidratação: O Sérum Hidratante pode ser usado na rotina diurna e noturna. Aplique sobre a pele seca sempre que desejar, espalhando até a absorção completa\r\nProteção: O Protetor Solar Watery Lotion é de uso diurno. Agite o produto e aplique abundantemente antes da exposição ao sol sobre a pele seca. Reaplique após sudorese intensa, nadar ou banhar-se, secar-se com toalha e durante a exposição ao sol. Se a quantidade aplicada não for adequada, o nível de proteção será significativamente reduzido. É necessária a reaplicação do produto para manter a sua efetividade.\r\n\r\nTratamento profundo: O Ácido Mandélico é de uso noturno. Aplique sobre a pele seca e preferencialmente hidratada, evitando a região dos olhos, os cantos do nariz e da boca. Espalhe 1 ou 2 pumps sobre a pele do rosto, pescoço e\/ou do colo, se desejar. No início do uso, recomenda-se usar em noites alternadas até que a pele não apresente nenhum sinal de irritação","especificacoes":"","diferenciais":"","garantia":"","fotos":["\/uploads\/produto_6a9e8f5c10d874.40721804.png","\/uploads\/produto_6a9e8f61dc6203.64821065.png","\/uploads\/produto_6a9e8f66bf6d19.63947023.png"],"videos":[{"url":"\/uploads\/vcv_video_6a9e3d4f467e73.17805414.mp4","autor":"Goxtosa Consumista","avatar":"\/uploads\/vcv_avatar_6a9e3de9ae0be8.32565341.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d7c3b7e45.76448650.mp4","autor":"By.marianam","avatar":"\/uploads\/vcv_avatar_6a9e3dff4cd365.40410118.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d5c029727.78264602.mp4","autor":"Wallessa Gabriela","avatar":"\/uploads\/vcv_avatar_6a9e3e1df12286.00552065.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d8030e9b7.04861517.mp4","autor":"Top Ofertas","avatar":"\/uploads\/vcv_avatar_6a9e3e409212a0.26461684.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d83131dd9.64249525.mp4","autor":"Leticia Nunes","avatar":"\/uploads\/vcv_avatar_6a9e3e5a427c91.93094118.jpg"},{"url":"\/uploads\/vcv_video_6a9e3e97ba4339.21268858.mp4","autor":"Top Ofertas","avatar":"\/uploads\/vcv_avatar_6a9e3e6e94b566.37428737.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d8d35ee83.89280456.mp4","autor":"Goxtosa Consumista","avatar":"\/uploads\/vcv_avatar_6a9e3e8814b904.24768369.jpeg"}],"frete":"","entrega":"","oferta_termina_em":"","recomendacoes":null,"modelo_landing":"modelo4","avatar_comentario":null,"status":"ativo","meta_title":null,"meta_description":null,"meta_keywords":null,"estoque_atual":0,"estoque_minimo":0,"estoque_maximo":null,"sku":null,"nome_comentario":"","quantidade_produtos":3141,"created_at":"2026-09-07 07:14:38","updated_at":"2026-09-07 07:24:20","oferta_relampago":{"ativo":false,"horas":8,"ultimas":5},"variacoes":[{"id":171425,"owner_user_id":316,"produto_id":19671,"tipo":"cor","titulo":"Kit Completo Antioleosidade","preco":"162.00","preco_comparacao":"180.18","desconto":"10.09","info":"Atributo: Cor","link_checkout":"","imagem":"\/uploads\/variacao_6a9e8fb59774c0.23238217.png","created_at":"2026-09-07 07:19:33"}],"comentarios":[{"id":165971,"owner_user_id":316,"produto_id":19671,"nome":"fe****a","foto_perfil":"\/uploads\/comentario_perfil_6a9e8e8eb9a1d4.98844518.jpg","descricao":"Tipo de pele: Meu tipo de pele é oleoso Então, vou começar a testar os produtos e ver como ele se comporta na minha Pele, Eu não consegui Colocar fotos do meu rosto pra mostrar como está, Mas eu volto pra contar! E gente chegou em apenas três dias. Foi muito rápido.. Muito rápido mesmo, vale a pena!!","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8e8eb9b1c9.21964502.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8eb9b9d2.71507472.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8eb9c0f8.76660223.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8eb9c753.76572432.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8eb9ce07.38144588.webp\"]","videos":null,"created_at":"2026-09-07 07:14:38"},{"id":165972,"owner_user_id":316,"produto_id":19671,"nome":"ju***a","foto_perfil":"\/uploads\/comentario_perfil_6a9e8e8eba7e09.82930111.png","descricao":"Amei, peguei em uma promoção maravilhosa, chegou super rápido ❤️","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8e8ebae964.25583413.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebaf2f3.59420317.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebafae9.64858620.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebb01b8.31299519.webp\"]","videos":null,"created_at":"2026-09-07 07:14:38"},{"id":165973,"owner_user_id":316,"produto_id":19671,"nome":"A**e B**a","foto_perfil":"\/uploads\/comentario_perfil_6a9e8e8ebc6be7.53792123.jpg","descricao":"Chegou super rápido! Só achei pouca a proteção para os produtos como está na foto A caixa do hidratante veio danificada e até parece que foi usado e tem pouco produto. Mas tirando isso veio tudo certinho e estou bem feliz com minhas compras e voltarei para comprar mais!!!","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8e8ebc7786.91413398.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebc7e88.57178137.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebc88f1.00254656.webp\"]","videos":null,"created_at":"2026-09-07 07:14:38"},{"id":165974,"owner_user_id":316,"produto_id":19671,"nome":"e**","foto_perfil":"\/uploads\/comentario_perfil_6a9e8e8ebce678.06577859.jpg","descricao":"Aí na foto ele está junto com mais algumas coisas q ganhei no dia do meu aniversário, mas muito bons todos os produtos recomendo!","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8e8ebcedc3.28027182.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebcf466.17104262.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebcfb85.38065423.webp\"]","videos":null,"created_at":"2026-09-07 07:14:38"},{"id":165975,"owner_user_id":316,"produto_id":19671,"nome":"is**a","foto_perfil":"\/uploads\/comentario_perfil_6a9e8e8ebe2fe1.28051600.jpeg","descricao":"Vamos ver se vai trazer melhorias para meu rosto, eu espero que sim de verdade. Porque essa marca só ouvi elogios.","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8e8ebe5972.26437110.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebe62c5.38654672.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebe6a63.85670616.webp\"]","videos":null,"created_at":"2026-09-07 07:14:38"},{"id":165976,"owner_user_id":316,"produto_id":19671,"nome":"A**✨","foto_perfil":"\/uploads\/comentario_perfil_6a9e8e8ebed018.89681698.jpg","descricao":"Gostei bastante estou fazendo tratamento facial minha pele já deu uma melhorada","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8e8ebed8d9.27338396.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebedfc4.78954485.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebee834.39165951.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebeef15.74134779.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebef714.67511008.webp\"]","videos":null,"created_at":"2026-09-07 07:14:38"},{"id":165977,"owner_user_id":316,"produto_id":19671,"nome":"s**_","foto_perfil":"\/uploads\/comentario_perfil_6a9e8e8ebf4ea8.38140849.jpg","descricao":"O kit completo venho certinho qualidade maravilhos","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8e8ebf5709.23318851.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebf5d62.79881566.webp\"]","videos":null,"created_at":"2026-09-07 07:14:38"},{"id":165978,"owner_user_id":316,"produto_id":19671,"nome":"K**n","foto_perfil":"\/uploads\/comentario_perfil_6a9e8e8ebfa950.20898505.jpg","descricao":"Amei, chegou super rapidinho comprei na promoção, produtos maravilhos bem embalados...🥰","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8e8ebfb449.19605931.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebfbb18.18445642.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebfc142.18029873.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ebfc804.14202619.webp\"]","videos":null,"created_at":"2026-09-07 07:14:38"},{"id":165979,"owner_user_id":316,"produto_id":19671,"nome":"T**a P**s","foto_perfil":"\/uploads\/comentario_perfil_6a9e8e8ec02067.80253767.jpg","descricao":"Entrega super rápida, amei os produtos, são pequeno mas cabem em qualquer lugar","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8e8ec02bd6.80584488.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ec03590.50062808.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ec04079.42828629.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ec04a09.58934329.webp\"]","videos":null,"created_at":"2026-09-07 07:14:38"},{"id":165980,"owner_user_id":316,"produto_id":19671,"nome":"N**a D**e","foto_perfil":"\/uploads\/comentario_perfil_6a9e8e8ec0a0a6.57516472.jpg","descricao":"Muito bom amei muito! Comprem sem medo, vou comprar de novo","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8e8ec0b193.53792112.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ec0b9f8.76093605.webp\"]","videos":null,"created_at":"2026-09-07 07:14:38"},{"id":165981,"owner_user_id":316,"produto_id":19671,"nome":"n**a d** l**ê","foto_perfil":"\/uploads\/comentario_perfil_6a9e8e8ec108c1.83185360.jpg","descricao":"Eu ainda não usei,mas gostei muito, vieram bem embalados pode. O hidratante veio com a caixinha aberta um pouco amassada mas fora isso adorei","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8e8ec113f7.72188416.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ec11e29.51866558.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ec129c6.92168465.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ec133b6.05990820.webp\"]","videos":null,"created_at":"2026-09-07 07:14:38"},{"id":165982,"owner_user_id":316,"produto_id":19671,"nome":"D**a G**s","foto_perfil":"\/uploads\/comentario_perfil_6a9e8e8ec17645.98783271.jpg","descricao":"Chegou super rápido, são meus primeiros produtos da marca,so deu pra comprar nessa promoção de 59,90 kkkkkkkkk","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8e8ec17d81.82201426.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ec184b4.87598113.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ec18cf4.55457994.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ec196e9.85602112.webp\"]","videos":null,"created_at":"2026-09-07 07:14:38"},{"id":165983,"owner_user_id":316,"produto_id":19671,"nome":"B**s","foto_perfil":"\/uploads\/comentario_perfil_6a9e8e8ec1d827.97563273.jpg","descricao":"promoção Maravilhosa,e o kit é perfeito uma semana já mudou minha pele Tipo de pele: Mista, oleosa no nariz e testa","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e8e8ec1e1e4.86133962.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ec1e894.11732235.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ec1ee02.74766335.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ec1f4e0.64166382.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ec1fb17.42290470.webp\",\"\\\/uploads\\\/comentario_foto_6a9e8e8ec20103.22031227.webp\"]","videos":null,"created_at":"2026-09-07 07:14:38"}]},{"id":19672,"owner_user_id":316,"titulo":"Duo para Poros - Ácido Glicólico + Ácido Mandélico","preco":"135.98","preco_comparacao":"168.40","desconto":"19.00","categoria":null,"order_bump_ativo":0,"order_bump_produto_id":null,"promo_ativa":0,"promo_banner":null,"notas":"5","descricao":"O combo Antiacne para peles oleosas ou mistas conta com 5 fórmulas inteligentes que agem em sinergia para reduzir a formação de cravos e espinhas, melhorar a aparência dos poros e controlar a oleosidade.\r\n\r\nLimpeza da pele: O Gel de limpeza deve ser usado na rotina diurna e noturna. Aplique 1 pump sobre a pele úmida e massageie até obter uma espuma leve, enxaguando em seguida.\r\n\r\nTonificação: O Ácido Salicílico pode ser usado de dia e à noite. Logo após a limpeza, aplique de 5 a 10 gotas do produto sobre a pele do rosto, pescoço e\/ou colo, se desejar. Espalhe com as mãos.\r\n\r\nHidratação: O Sérum Hidratante pode ser usado na rotina diurna e noturna. Aplique sobre a pele seca sempre que desejar, espalhando até a absorção completa\r\nProteção: O Protetor Solar Watery Lotion é de uso diurno. Agite o produto e aplique abundantemente antes da exposição ao sol sobre a pele seca. Reaplique após sudorese intensa, nadar ou banhar-se, secar-se com toalha e durante a exposição ao sol. Se a quantidade aplicada não for adequada, o nível de proteção será significativamente reduzido. É necessária a reaplicação do produto para manter a sua efetividade.\r\n\r\nTratamento profundo: O Ácido Mandélico é de uso noturno. Aplique sobre a pele seca e preferencialmente hidratada, evitando a região dos olhos, os cantos do nariz e da boca. Espalhe 1 ou 2 pumps sobre a pele do rosto, pescoço e\/ou do colo, se desejar. No início do uso, recomenda-se usar em noites alternadas até que a pele não apresente nenhum sinal de irritação","especificacoes":"","diferenciais":"","garantia":"","fotos":["\/uploads\/produto_6a9e921c331e10.41003358.webp","\/uploads\/produto_6a9e92217fd558.72913881.webp","\/uploads\/produto_6a9e92269faca6.53996884.webp"],"videos":[{"url":"\/uploads\/vcv_video_6a9e3d4f467e73.17805414.mp4","autor":"Goxtosa Consumista","avatar":"\/uploads\/vcv_avatar_6a9e3de9ae0be8.32565341.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d7c3b7e45.76448650.mp4","autor":"By.marianam","avatar":"\/uploads\/vcv_avatar_6a9e3dff4cd365.40410118.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d5c029727.78264602.mp4","autor":"Wallessa Gabriela","avatar":"\/uploads\/vcv_avatar_6a9e3e1df12286.00552065.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d8030e9b7.04861517.mp4","autor":"Top Ofertas","avatar":"\/uploads\/vcv_avatar_6a9e3e409212a0.26461684.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d83131dd9.64249525.mp4","autor":"Leticia Nunes","avatar":"\/uploads\/vcv_avatar_6a9e3e5a427c91.93094118.jpg"},{"url":"\/uploads\/vcv_video_6a9e3e97ba4339.21268858.mp4","autor":"Top Ofertas","avatar":"\/uploads\/vcv_avatar_6a9e3e6e94b566.37428737.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d8d35ee83.89280456.mp4","autor":"Goxtosa Consumista","avatar":"\/uploads\/vcv_avatar_6a9e3e8814b904.24768369.jpeg"}],"frete":"","entrega":"","oferta_termina_em":"","recomendacoes":null,"modelo_landing":"modelo4","avatar_comentario":null,"status":"ativo","meta_title":null,"meta_description":null,"meta_keywords":null,"estoque_atual":0,"estoque_minimo":0,"estoque_maximo":null,"sku":null,"nome_comentario":"","quantidade_produtos":3141,"created_at":"2026-09-07 07:26:40","updated_at":"2026-09-07 07:30:44","oferta_relampago":{"ativo":false,"horas":8,"ultimas":5},"variacoes":[{"id":171428,"owner_user_id":316,"produto_id":19672,"tipo":"cor","titulo":"Kit Completo Antioleosidade","preco":"135.98","preco_comparacao":"168.40","desconto":"19.25","info":"Atributo: Cor","link_checkout":"","imagem":"\/uploads\/variacao_6a9e945a731481.72367095.webp","created_at":"2026-09-07 07:39:22"}],"comentarios":[{"id":165984,"owner_user_id":316,"produto_id":19672,"nome":"fe****a","foto_perfil":"\/uploads\/comentario_perfil_6a9e916033b8a7.29889837.jpg","descricao":"Tipo de pele: Meu tipo de pele é oleoso Então, vou começar a testar os produtos e ver como ele se comporta na minha Pele, Eu não consegui Colocar fotos do meu rosto pra mostrar como está, Mas eu volto pra contar! E gente chegou em apenas três dias. Foi muito rápido.. Muito rápido mesmo, vale a pena!!","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e916033c124.50652600.webp\",\"\\\/uploads\\\/comentario_foto_6a9e916033c793.89499219.webp\",\"\\\/uploads\\\/comentario_foto_6a9e916033cf63.69464900.webp\",\"\\\/uploads\\\/comentario_foto_6a9e916033d5a5.31893001.webp\",\"\\\/uploads\\\/comentario_foto_6a9e916033dc49.96956576.webp\"]","videos":null,"created_at":"2026-09-07 07:26:40"},{"id":165985,"owner_user_id":316,"produto_id":19672,"nome":"ju***a","foto_perfil":"\/uploads\/comentario_perfil_6a9e9160342107.50995079.png","descricao":"Amei, peguei em uma promoção maravilhosa, chegou super rápido ❤️","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e916034a996.39303039.webp\",\"\\\/uploads\\\/comentario_foto_6a9e916034b247.75583579.webp\",\"\\\/uploads\\\/comentario_foto_6a9e916034bba2.19505627.webp\",\"\\\/uploads\\\/comentario_foto_6a9e916034c973.25538986.webp\"]","videos":null,"created_at":"2026-09-07 07:26:40"},{"id":165986,"owner_user_id":316,"produto_id":19672,"nome":"A**e B**a","foto_perfil":"\/uploads\/comentario_perfil_6a9e9160350b14.93973623.jpg","descricao":"Chegou super rápido! Só achei pouca a proteção para os produtos como está na foto A caixa do hidratante veio danificada e até parece que foi usado e tem pouco produto. Mas tirando isso veio tudo certinho e estou bem feliz com minhas compras e voltarei para comprar mais!!!","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e91603513b5.10118904.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160351ac1.92311893.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160352262.42464146.webp\"]","videos":null,"created_at":"2026-09-07 07:26:40"},{"id":165987,"owner_user_id":316,"produto_id":19672,"nome":"e**","foto_perfil":"\/uploads\/comentario_perfil_6a9e9160355c72.50149186.jpg","descricao":"Aí na foto ele está junto com mais algumas coisas q ganhei no dia do meu aniversário, mas muito bons todos os produtos recomendo!","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e9160356872.55792474.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160357350.79303611.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160357b44.22679719.webp\"]","videos":null,"created_at":"2026-09-07 07:26:40"},{"id":165988,"owner_user_id":316,"produto_id":19672,"nome":"is**a","foto_perfil":"\/uploads\/comentario_perfil_6a9e916035b974.67378390.jpeg","descricao":"Vamos ver se vai trazer melhorias para meu rosto, eu espero que sim de verdade. Porque essa marca só ouvi elogios.","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e916035e719.45131792.webp\",\"\\\/uploads\\\/comentario_foto_6a9e916035ed89.45022880.webp\",\"\\\/uploads\\\/comentario_foto_6a9e916035f3a9.51767196.webp\"]","videos":null,"created_at":"2026-09-07 07:26:40"},{"id":165989,"owner_user_id":316,"produto_id":19672,"nome":"A**✨","foto_perfil":"\/uploads\/comentario_perfil_6a9e9160364c06.86077406.jpg","descricao":"Gostei bastante estou fazendo tratamento facial minha pele já deu uma melhorada","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e9160365cc4.43618202.webp\",\"\\\/uploads\\\/comentario_foto_6a9e91603666d9.29079340.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160366e49.18790746.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160367466.55770966.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160367bf9.44151643.webp\"]","videos":null,"created_at":"2026-09-07 07:26:40"},{"id":165990,"owner_user_id":316,"produto_id":19672,"nome":"s**_","foto_perfil":"\/uploads\/comentario_perfil_6a9e916036c8c7.51279062.jpg","descricao":"O kit completo venho certinho qualidade maravilhos","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e916036d331.66641040.webp\",\"\\\/uploads\\\/comentario_foto_6a9e916036db09.89893020.webp\"]","videos":null,"created_at":"2026-09-07 07:26:40"},{"id":165991,"owner_user_id":316,"produto_id":19672,"nome":"K**n","foto_perfil":"\/uploads\/comentario_perfil_6a9e9160371854.96774867.jpg","descricao":"Amei, chegou super rapidinho comprei na promoção, produtos maravilhos bem embalados...🥰","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e9160372308.03044503.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160372ac6.43894900.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160373193.67236928.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160373950.56340436.webp\"]","videos":null,"created_at":"2026-09-07 07:26:40"},{"id":165992,"owner_user_id":316,"produto_id":19672,"nome":"T**a P**s","foto_perfil":"\/uploads\/comentario_perfil_6a9e91603772d2.17653916.jpg","descricao":"Entrega super rápida, amei os produtos, são pequeno mas cabem em qualquer lugar","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e9160377c76.80633366.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160378326.74856212.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160378915.51029012.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160379083.09105981.webp\"]","videos":null,"created_at":"2026-09-07 07:26:40"},{"id":165993,"owner_user_id":316,"produto_id":19672,"nome":"N**a D**e","foto_perfil":"\/uploads\/comentario_perfil_6a9e916037cf68.81204989.jpg","descricao":"Muito bom amei muito! Comprem sem medo, vou comprar de novo","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e916037d807.88704803.webp\",\"\\\/uploads\\\/comentario_foto_6a9e916037de24.10325218.webp\"]","videos":null,"created_at":"2026-09-07 07:26:40"},{"id":165994,"owner_user_id":316,"produto_id":19672,"nome":"n**a d** l**ê","foto_perfil":"\/uploads\/comentario_perfil_6a9e9160381415.44580821.jpg","descricao":"Eu ainda não usei,mas gostei muito, vieram bem embalados pode. O hidratante veio com a caixinha aberta um pouco amassada mas fora isso adorei","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e9160381f67.85597983.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160382790.58267894.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160382e63.94353355.webp\",\"\\\/uploads\\\/comentario_foto_6a9e91603836e1.56495231.webp\"]","videos":null,"created_at":"2026-09-07 07:26:40"},{"id":165995,"owner_user_id":316,"produto_id":19672,"nome":"D**a G**s","foto_perfil":"\/uploads\/comentario_perfil_6a9e916038a3d0.43785368.jpg","descricao":"Chegou super rápido, são meus primeiros produtos da marca,so deu pra comprar nessa promoção de 59,90 kkkkkkkkk","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e916038ace6.63221079.webp\",\"\\\/uploads\\\/comentario_foto_6a9e916038b4f5.02669465.webp\",\"\\\/uploads\\\/comentario_foto_6a9e916038ba40.69034888.webp\",\"\\\/uploads\\\/comentario_foto_6a9e916038bfd6.15447205.webp\"]","videos":null,"created_at":"2026-09-07 07:26:40"},{"id":165996,"owner_user_id":316,"produto_id":19672,"nome":"B**s","foto_perfil":"\/uploads\/comentario_perfil_6a9e916038f8b8.28622007.jpg","descricao":"promoção Maravilhosa,e o kit é perfeito uma semana já mudou minha pele Tipo de pele: Mista, oleosa no nariz e testa","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e9160390283.16993936.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160390914.70230652.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160391081.41368216.webp\",\"\\\/uploads\\\/comentario_foto_6a9e91603916e9.47937867.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160391d46.89554859.webp\",\"\\\/uploads\\\/comentario_foto_6a9e9160392479.41837866.webp\"]","videos":null,"created_at":"2026-09-07 07:26:40"}]},{"id":19673,"owner_user_id":316,"titulo":"Kit Completo Antioleosidade - Limpador Antioleosidade + Ácido Salicilico + Ácido Mandélico + Calming Cream + Protetor Solar","preco":"64.90","preco_comparacao":"371.63","desconto":"83.00","categoria":null,"order_bump_ativo":0,"order_bump_produto_id":null,"promo_ativa":0,"promo_banner":null,"notas":"5","descricao":"O combo Antiacne para peles oleosas ou mistas conta com 5 fórmulas inteligentes que agem em sinergia para reduzir a formação de cravos e espinhas, melhorar a aparência dos poros e controlar a oleosidade.\r\n\r\nLimpeza da pele: O Gel de limpeza deve ser usado na rotina diurna e noturna. Aplique 1 pump sobre a pele úmida e massageie até obter uma espuma leve, enxaguando em seguida.\r\n\r\nTonificação: O Ácido Salicílico pode ser usado de dia e à noite. Logo após a limpeza, aplique de 5 a 10 gotas do produto sobre a pele do rosto, pescoço e\/ou colo, se desejar. Espalhe com as mãos.\r\n\r\nHidratação: O Sérum Hidratante pode ser usado na rotina diurna e noturna. Aplique sobre a pele seca sempre que desejar, espalhando até a absorção completa\r\nProteção: O Protetor Solar Watery Lotion é de uso diurno. Agite o produto e aplique abundantemente antes da exposição ao sol sobre a pele seca. Reaplique após sudorese intensa, nadar ou banhar-se, secar-se com toalha e durante a exposição ao sol. Se a quantidade aplicada não for adequada, o nível de proteção será significativamente reduzido. É necessária a reaplicação do produto para manter a sua efetividade.\r\n\r\nTratamento profundo: O Ácido Mandélico é de uso noturno. Aplique sobre a pele seca e preferencialmente hidratada, evitando a região dos olhos, os cantos do nariz e da boca. Espalhe 1 ou 2 pumps sobre a pele do rosto, pescoço e\/ou do colo, se desejar. No início do uso, recomenda-se usar em noites alternadas até que a pele não apresente nenhum sinal de irritação","especificacoes":"","diferenciais":"","garantia":"","fotos":["\/uploads\/produto_6a9e93cf071171.50000371.webp","\/uploads\/produto_6a9e93d39f6c42.62449558.webp","\/uploads\/produto_6a9e93d7c1ce03.81296725.webp","\/uploads\/produto_6a9e93dc211437.12988316.webp","\/uploads\/produto_6a9e93e09df6a3.70720961.webp"],"videos":[{"url":"\/uploads\/vcv_video_6a9e3d4f467e73.17805414.mp4","autor":"Goxtosa Consumista","avatar":"\/uploads\/vcv_avatar_6a9e3de9ae0be8.32565341.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d7c3b7e45.76448650.mp4","autor":"By.marianam","avatar":"\/uploads\/vcv_avatar_6a9e3dff4cd365.40410118.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d5c029727.78264602.mp4","autor":"Wallessa Gabriela","avatar":"\/uploads\/vcv_avatar_6a9e3e1df12286.00552065.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d8030e9b7.04861517.mp4","autor":"Top Ofertas","avatar":"\/uploads\/vcv_avatar_6a9e3e409212a0.26461684.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d83131dd9.64249525.mp4","autor":"Leticia Nunes","avatar":"\/uploads\/vcv_avatar_6a9e3e5a427c91.93094118.jpg"},{"url":"\/uploads\/vcv_video_6a9e3e97ba4339.21268858.mp4","autor":"Top Ofertas","avatar":"\/uploads\/vcv_avatar_6a9e3e6e94b566.37428737.jpeg"},{"url":"\/uploads\/vcv_video_6a9e3d8d35ee83.89280456.mp4","autor":"Goxtosa Consumista","avatar":"\/uploads\/vcv_avatar_6a9e3e8814b904.24768369.jpeg"}],"frete":"","entrega":"","oferta_termina_em":"","recomendacoes":null,"modelo_landing":"modelo4","avatar_comentario":null,"status":"ativo","meta_title":null,"meta_description":null,"meta_keywords":null,"estoque_atual":0,"estoque_minimo":0,"estoque_maximo":null,"sku":null,"nome_comentario":"","quantidade_produtos":3141,"created_at":"2026-09-07 07:33:27","updated_at":"2026-09-07 07:37:20","oferta_relampago":{"ativo":false,"horas":8,"ultimas":5},"variacoes":[{"id":171430,"owner_user_id":316,"produto_id":19673,"tipo":"cor","titulo":"Kit Completo Antioleosidade","preco":"64.90","preco_comparacao":"371.63","desconto":"82.54","info":"Atributo: Cor","link_checkout":"","imagem":"\/uploads\/variacao_6a9e94d5c45df1.18174601.webp","created_at":"2026-09-07 07:41:25"}],"comentarios":[{"id":165997,"owner_user_id":316,"produto_id":19673,"nome":"fe****a","foto_perfil":"\/uploads\/comentario_perfil_6a9e92f7c1f227.27924152.jpg","descricao":"Tipo de pele: Meu tipo de pele é oleoso Então, vou começar a testar os produtos e ver como ele se comporta na minha Pele, Eu não consegui Colocar fotos do meu rosto pra mostrar como está, Mas eu volto pra contar! E gente chegou em apenas três dias. Foi muito rápido.. Muito rápido mesmo, vale a pena!!","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e92f7c1fc78.03824212.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c203f2.81213900.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c20a75.84680166.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c210c7.63728877.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c217d4.95662039.webp\"]","videos":null,"created_at":"2026-09-07 07:33:27"},{"id":165998,"owner_user_id":316,"produto_id":19673,"nome":"ju***a","foto_perfil":"\/uploads\/comentario_perfil_6a9e92f7c25857.74411283.png","descricao":"Amei, peguei em uma promoção maravilhosa, chegou super rápido ❤️","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e92f7c2e758.56127402.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c2f1c2.53760130.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c2f999.44298519.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c300e8.73067837.webp\"]","videos":null,"created_at":"2026-09-07 07:33:27"},{"id":165999,"owner_user_id":316,"produto_id":19673,"nome":"A**e B**a","foto_perfil":"\/uploads\/comentario_perfil_6a9e92f7c34155.27777832.jpg","descricao":"Chegou super rápido! Só achei pouca a proteção para os produtos como está na foto A caixa do hidratante veio danificada e até parece que foi usado e tem pouco produto. Mas tirando isso veio tudo certinho e estou bem feliz com minhas compras e voltarei para comprar mais!!!","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e92f7c34c52.81403676.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c354a0.16985197.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c36090.81729195.webp\"]","videos":null,"created_at":"2026-09-07 07:33:27"},{"id":166000,"owner_user_id":316,"produto_id":19673,"nome":"e**","foto_perfil":"\/uploads\/comentario_perfil_6a9e92f7c3aab8.40234309.jpg","descricao":"Aí na foto ele está junto com mais algumas coisas q ganhei no dia do meu aniversário, mas muito bons todos os produtos recomendo!","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e92f7c3b556.96172194.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c3c020.61870342.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c3cde4.42876289.webp\"]","videos":null,"created_at":"2026-09-07 07:33:27"},{"id":166001,"owner_user_id":316,"produto_id":19673,"nome":"is**a","foto_perfil":"\/uploads\/comentario_perfil_6a9e92f7c408f1.76512679.jpeg","descricao":"Vamos ver se vai trazer melhorias para meu rosto, eu espero que sim de verdade. Porque essa marca só ouvi elogios.","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e92f7c43241.96968227.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c43c74.61505259.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c44413.76640224.webp\"]","videos":null,"created_at":"2026-09-07 07:33:27"},{"id":166002,"owner_user_id":316,"produto_id":19673,"nome":"A**✨","foto_perfil":"\/uploads\/comentario_perfil_6a9e92f7c47950.32103922.jpg","descricao":"Gostei bastante estou fazendo tratamento facial minha pele já deu uma melhorada","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e92f7c482e3.09593136.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c48a53.55530604.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c49277.07612711.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c49d16.85712138.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c4a697.58196627.webp\"]","videos":null,"created_at":"2026-09-07 07:33:27"},{"id":166003,"owner_user_id":316,"produto_id":19673,"nome":"s**_","foto_perfil":"\/uploads\/comentario_perfil_6a9e92f7c4e4e0.95902791.jpg","descricao":"O kit completo venho certinho qualidade maravilhos","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e92f7c4ec07.22306609.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c4f517.21057055.webp\"]","videos":null,"created_at":"2026-09-07 07:33:27"},{"id":166004,"owner_user_id":316,"produto_id":19673,"nome":"K**n","foto_perfil":"\/uploads\/comentario_perfil_6a9e92f7c52ca1.51256718.jpg","descricao":"Amei, chegou super rapidinho comprei na promoção, produtos maravilhos bem embalados...🥰","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e92f7c53802.88565474.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c54201.49570673.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c54b39.16269204.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c55537.37739638.webp\"]","videos":null,"created_at":"2026-09-07 07:33:27"},{"id":166005,"owner_user_id":316,"produto_id":19673,"nome":"T**a P**s","foto_perfil":"\/uploads\/comentario_perfil_6a9e92f7c58fb6.45078149.jpg","descricao":"Entrega super rápida, amei os produtos, são pequeno mas cabem em qualquer lugar","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e92f7c59861.92111223.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c59e22.36239882.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c5a4f3.78008067.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c5abe5.82691076.webp\"]","videos":null,"created_at":"2026-09-07 07:33:27"},{"id":166006,"owner_user_id":316,"produto_id":19673,"nome":"N**a D**e","foto_perfil":"\/uploads\/comentario_perfil_6a9e92f7c5f2f9.42189840.jpg","descricao":"Muito bom amei muito! Comprem sem medo, vou comprar de novo","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e92f7c60029.32068467.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c608e9.37087581.webp\"]","videos":null,"created_at":"2026-09-07 07:33:27"},{"id":166007,"owner_user_id":316,"produto_id":19673,"nome":"n**a d** l**ê","foto_perfil":"\/uploads\/comentario_perfil_6a9e92f7c64024.39089987.jpg","descricao":"Eu ainda não usei,mas gostei muito, vieram bem embalados pode. O hidratante veio com a caixinha aberta um pouco amassada mas fora isso adorei","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e92f7c64b16.56675402.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c65237.50684712.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c659b6.18967520.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c66124.78585510.webp\"]","videos":null,"created_at":"2026-09-07 07:33:27"},{"id":166008,"owner_user_id":316,"produto_id":19673,"nome":"D**a G**s","foto_perfil":"\/uploads\/comentario_perfil_6a9e92f7c69682.14784846.jpg","descricao":"Chegou super rápido, são meus primeiros produtos da marca,so deu pra comprar nessa promoção de 59,90 kkkkkkkkk","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e92f7c69f24.95203507.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c6a757.37841487.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c6aec1.15782869.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c6b482.18485785.webp\"]","videos":null,"created_at":"2026-09-07 07:33:27"},{"id":166009,"owner_user_id":316,"produto_id":19673,"nome":"B**s","foto_perfil":"\/uploads\/comentario_perfil_6a9e92f7c6ef65.04848212.jpg","descricao":"promoção Maravilhosa,e o kit é perfeito uma semana já mudou minha pele Tipo de pele: Mista, oleosa no nariz e testa","nota":5,"fotos":"[\"\\\/uploads\\\/comentario_foto_6a9e92f7c6f9b6.06708848.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c702c6.56419585.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c709b8.56136915.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c71020.26921284.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c71718.76993397.webp\",\"\\\/uploads\\\/comentario_foto_6a9e92f7c71e29.72570600.webp\"]","videos":null,"created_at":"2026-09-07 07:33:27"}]}];

    const FALLBACK_MEDIA = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
    let bodyScrollY = 0;
    let produtoSelecionado = null;
    let variacoesSelecionadasPorTipo = {};
    let modalProdutoAtual = null;
    let galleryImages = [];
    let galleryIndex = 0;

    function t(key) {
      var map = {
        no_variations: 'Este produto não possui variações cadastradas.',
        quantity: 'Quantidade',
        variations: 'Variações',
        select_variation_first: 'Selecione uma variação para continuar.',
        already_in_cart: 'Este produto já está no seu carrinho.',
        added_to_cart: 'Adicionado ao carrinho',
        album_and_pack_added: 'Álbum e pacote adicionados ao carrinho.',
        some_items_already_in_cart: 'Alguns itens já estavam no carrinho.'
      };
      return map[key] || key;
    }

    function resolveMediaPath(path, base) {
      if (!path) return FALLBACK_MEDIA;
      if (/^https?:\/\//i.test(path) || /^data:/i.test(path)) return path;
      var raw = String(path).trim().replace(/^\.\//,'');
      try {
        if (base) {
          var baseUrl = /^https?:/i.test(base) ? base : new URL(base.replace(/^\.\//,''), document.baseURI).toString();
          return new URL(raw, baseUrl).toString();
        }
        if (raw.startsWith('/')) return new URL(raw, window.location.origin || document.baseURI).toString();
        if (raw.includes('/')) return new URL('/'+raw.replace(/^\/+/,''), window.location.origin || document.baseURI).toString();
        return new URL(raw, document.baseURI).toString();
      } catch(e) {
        return raw.startsWith('/') ? raw : '/'+raw;
      }
    }

    function buildGalleryImages(primary, extras) {
      if (!extras) extras = [];
      var list = [];
      function add(value) {
        if (!value) return;
        var resolved = resolveMediaPath(value);
        if (resolved && list.indexOf(resolved) === -1) list.push(resolved);
      }
      add(primary);
      if (Array.isArray(extras)) extras.forEach(add);
      if (!list.length) list.push(FALLBACK_MEDIA);
      return list;
    }

    function calcularDesconto(preco, precoComparacao) {
      var vp = Number(preco || 0), vc = Number(precoComparacao || 0);
      if (!vc || vc <= vp) return 0;
      return Math.round(((vc - vp) / vc) * 100);
    }

    function showImageViewerIndex(index) {
      if (!galleryImages.length) return;
      var total = galleryImages.length;
      galleryIndex = (index + total) % total;
      var img = document.getElementById('imageViewerImg');
      if (img) { img.onerror = function(){ img.onerror=null; img.src=FALLBACK_MEDIA; }; img.src = galleryImages[galleryIndex]; }
      var counter = document.getElementById('imageViewerCounter');
      if (counter) counter.textContent = total > 1 ? (galleryIndex+1)+' / '+total : '';
    }

    function openImageViewer(images, startIndex, title) {
      if (startIndex === undefined) startIndex = 0;
      if (!title) title = '';
      var viewer = document.getElementById('imageViewer');
      if (!viewer) return;
      var list = Array.isArray(images) ? images.filter(Boolean) : [];
      galleryImages = list.length ? list : [FALLBACK_MEDIA];
      var initial = Math.max(0, Math.min(startIndex, galleryImages.length-1));
      var titleEl = document.getElementById('imageViewerTitle');
      if (titleEl) titleEl.textContent = title;
      showImageViewerIndex(initial);
      viewer.classList.add('show');
      viewer.setAttribute('aria-hidden','false');
    }

    function closeImageViewer() {
      var viewer = document.getElementById('imageViewer');
      if (!viewer) return;
      viewer.classList.remove('show');
      viewer.setAttribute('aria-hidden','true');
    }

    function abrirModal() {
      var modal = document.getElementById('meuModal');
      if (!modal) return;
      bodyScrollY = window.scrollY || window.pageYOffset || 0;
      document.body.style.position = 'fixed';
      document.body.style.top = '-'+bodyScrollY+'px';
      document.body.style.left = '0';
      document.body.style.right = '0';
      document.body.style.width = '100%';
      document.body.style.overflow = 'hidden';
      modal.classList.remove('hidden');
    }

    function fecharModal() {
      var modal = document.getElementById('meuModal');
      if (modal) modal.classList.add('hidden');
      document.body.style.removeProperty('position');
      var top = document.body.style.top;
      document.body.style.removeProperty('top');
      document.body.style.removeProperty('left');
      document.body.style.removeProperty('right');
      document.body.style.removeProperty('width');
      document.body.style.removeProperty('overflow');
      var y = top ? parseInt(top,10) : 0;
      window.scrollTo(0, (y && !Number.isNaN(y)) ? -y : (bodyScrollY||0));
      produtoSelecionado = null;
      variacoesSelecionadasPorTipo = {};
    }

    function renderVariacoes(variacoes) {
      if (!variacoes) variacoes = [];
      var grid = document.getElementById('grid-variacoes');
      if (!grid) return;
      grid.innerHTML = '';
      variacoesSelecionadasPorTipo = {};

      if (!variacoes.length) {
        var aviso = document.createElement('p');
        aviso.style.cssText = 'font-size:14px;color:#6b7280;';
        aviso.textContent = t('no_variations');
        grid.appendChild(aviso);
        return;
      }

      var grupos = {};
      variacoes.forEach(function(v, idx) {
        var tipo = String(v.tipo || 'variacao').toLowerCase();
        if (!grupos[tipo]) grupos[tipo] = [];
        var preco = Number(v.preco || 0);
        var precoComparacao = Number(v.precoComparacao || v.preco_comparacao || preco);
        if (!precoComparacao || isNaN(precoComparacao) || precoComparacao <= 0) precoComparacao = preco;
        var desconto = Number(v.desconto || 0);
        if (!desconto || isNaN(desconto)) desconto = calcularDesconto(preco, precoComparacao);
        var entry = Object.assign({}, v, {
          idx: idx, tipo: tipo,
          titulo: v.titulo || '',
          preco: preco, precoComparacao: precoComparacao, desconto: desconto,
          checkoutLink: v.checkoutLink || v.link_checkout || '',
          imagem: resolveMediaPath(v.imagem || (modalProdutoAtual ? modalProdutoAtual.imagemPrincipal || '' : ''))
        });
        grupos[tipo].push(entry);
      });

      Object.keys(grupos).forEach(function(tipo) {
        var labelText = tipo==='cor' ? 'Cor' : tipo==='tamanho' ? 'Tamanho' : tipo.charAt(0).toUpperCase()+tipo.slice(1);
        var count = grupos[tipo].length;
        var groupTitle = document.createElement('div');
        groupTitle.style.cssText = 'display:flex;align-items:center;justify-content:space-between;margin:10px 0 8px;';
        var titleLeft = document.createElement('span');
        titleLeft.style.cssText = 'font-size:14px;font-weight:700;color:#111;';
        titleLeft.textContent = labelText+' ('+count+')';
        groupTitle.appendChild(titleLeft);
        if (tipo === 'tamanho') {
          var guia = document.createElement('span');
          guia.style.cssText = 'font-size:13px;color:#1890ff;cursor:pointer;';
          guia.textContent = 'Guia de tamanhos';
          groupTitle.appendChild(guia);
        }
        grid.appendChild(groupTitle);

        var row = document.createElement('div');
        row.className = (tipo==='cor') ? 'variation-row-grid' : 'variation-row scrollbar-hide';

        var groupGallery = buildGalleryImages(
          modalProdutoAtual ? modalProdutoAtual.imagemPrincipal || '' : '',
          grupos[tipo].map(function(v){ return v.imagem || ''; })
        );

        grupos[tipo].forEach(function(variacao, optionIndex) {
          var btn = document.createElement('div');
          btn.className = 'variation-card'+(tipo==='tamanho' ? ' is-size' : '');
          btn.setAttribute('role','button');
          btn.setAttribute('tabindex','0');
          btn.setAttribute('data-index', String(variacao.idx));
          btn.setAttribute('data-tipo', variacao.tipo);
          btn.setAttribute('data-variacao-id', String(variacao.id || ''));
          btn.setAttribute('data-variation','1');

          var imageWrap = document.createElement('div');
          imageWrap.className = 'variation-image-wrap';
          var imageEl = document.createElement('img');
          imageEl.className = 'variation-image';
          imageEl.alt = variacao.titulo;
          imageEl.src = variacao.imagem || FALLBACK_MEDIA;
          imageEl.loading = 'lazy';
          imageEl.onerror = function(){ imageEl.onerror=null; imageEl.src=FALLBACK_MEDIA; };
          imageWrap.appendChild(imageEl);

          var zoomBtn = document.createElement('button');
          zoomBtn.type = 'button';
          zoomBtn.className = 'variation-zoom';
          zoomBtn.innerHTML = '<i class="fas fa-up-right-and-down-left-from-center"></i>';
          (function(vr){
            zoomBtn.addEventListener('click', function(event) {
              event.stopPropagation();
              if (!groupGallery.length) return;
              var startIndex = Math.max(0, groupGallery.indexOf(vr.imagem));
              openImageViewer(groupGallery, startIndex, vr.titulo || '');
            });
          })(variacao);
          imageWrap.appendChild(zoomBtn);

          var label = document.createElement('span');
          label.className = 'variation-label';
          var words = (variacao.titulo || '').split(' ');
          label.textContent = words.length > 4 ? words.slice(0,4).join(' ')+'…' : variacao.titulo;
          var labelWrap = document.createElement('div');
          labelWrap.className = 'variation-label-wrap';
          labelWrap.appendChild(label);

          if (tipo !== 'tamanho') btn.appendChild(imageWrap);
          btn.appendChild(labelWrap);

          var selecionarOpcao = (function(b, r, vr, tp) {
            return function() {
              r.querySelectorAll('.variation-card.is-selected').forEach(function(c){ c.classList.remove('is-selected'); });
              b.classList.add('is-selected');
              variacoesSelecionadasPorTipo[tp] = vr;
              atualizarResumoSelecaoModal();
            };
          })(btn, row, variacao, tipo);

          btn.addEventListener('click', selecionarOpcao);
          btn.addEventListener('keydown', function(event) {
            if (event.key==='Enter' || event.key===' ') { event.preventDefault(); selecionarOpcao(); }
          });
          row.appendChild(btn);

          if (optionIndex === 0) {
            btn.classList.add('is-selected');
            variacoesSelecionadasPorTipo[tipo] = variacao;
          }
        });

        grid.appendChild(row);
      });

      atualizarResumoSelecaoModal();

      var contentEl = document.getElementById('modal-conteudo') || grid.parentNode;
      var buyBox = document.getElementById('buy-positions');
      if (!buyBox) {
        buyBox = document.createElement('div');
        buyBox.id = 'buy-positions';
        contentEl.appendChild(buyBox);
      }
      buyBox.innerHTML = '<a href="javascript:void(0);" onclick="comprarAgora()" id="div_ors_4" class="w-full text-white text-center block" style="font-size:16px;font-weight:700;border-radius:999px;padding:14px;background:#fe2d55;outline:none;-webkit-tap-highlight-color:transparent;box-shadow:none;">Adicionar ao carrinho</a>';

      var qtyBox = document.getElementById('qty-row');
      if (!qtyBox) {
        qtyBox = document.createElement('div');
        qtyBox.id = 'qty-row';
        qtyBox.className = 'mt-4 mb-2';
        contentEl.insertBefore(qtyBox, buyBox);
      }
      qtyBox.innerHTML = '<div style="display:flex;align-items:center;justify-content:space-between;margin:14px 0 12px;"><span style="font-size:14px;font-weight:700;color:#111;">'+t('quantity')+'</span><div style="display:flex;align-items:center;background:#f5f5f5;border-radius:8px;overflow:hidden;"><button type="button" id="qtd-menos" style="width:36px;height:36px;border:none;background:transparent;font-size:20px;color:#111;cursor:pointer;display:flex;align-items:center;justify-content:center;font-weight:300;">−</button><input type="number" id="qtd-input" value="1" min="1" style="width:36px;text-align:center;border:none;background:transparent;font-size:15px;font-weight:700;color:#111;outline:none;" /><button type="button" id="qtd-mais" style="width:36px;height:36px;border:none;background:transparent;font-size:20px;color:#111;cursor:pointer;display:flex;align-items:center;justify-content:center;font-weight:300;">+</button></div></div>';

      var input = qtyBox.querySelector('#qtd-input');
      var btnMenos = qtyBox.querySelector('#qtd-menos');
      var btnMais = qtyBox.querySelector('#qtd-mais');
      btnMenos.addEventListener('click', function(){
        var v = parseInt(input.value,10)||1;
        if(v>1) v--;
        input.value = v;
        if(produtoSelecionado) produtoSelecionado.quantidade = v;
      });
      btnMais.addEventListener('click', function(){
        var v = parseInt(input.value,10)||1;
        v++;
        input.value = v;
        if(produtoSelecionado) produtoSelecionado.quantidade = v;
      });
      input.addEventListener('change', function(){
        var v = parseInt(input.value,10);
        if(!v||v<1) v=1;
        input.value = v;
        if(produtoSelecionado) produtoSelecionado.quantidade = v;
      });
      if (produtoSelecionado) produtoSelecionado.quantidade = parseInt(input.value,10)||1;
    }

    function atualizarResumoSelecaoModal() {
      if (!modalProdutoAtual) return;
      var variacaoCor = variacoesSelecionadasPorTipo.cor || null;
      var variacaoTamanho = variacoesSelecionadasPorTipo.tamanho || null;
      var temGrupoCor = Array.isArray(modalProdutoAtual.variacoes) && modalProdutoAtual.variacoes.some(function(v){ return String(v.tipo||'').toLowerCase()==='cor'; });
      var usarTamanhoComoPrincipal = !temGrupoCor && !!variacaoTamanho;
      var variacaoPrincipal = usarTamanhoComoPrincipal ? variacaoTamanho : variacaoCor;
      var variacaoPacote = usarTamanhoComoPrincipal ? null : variacaoTamanho;

      var tituloAlbum = variacaoPrincipal ? (variacaoPrincipal.titulo || modalProdutoAtual.titulo || '') : (modalProdutoAtual.titulo || '');
      var tituloPacote = variacaoPacote ? (variacaoPacote.titulo || '') : '';
      var tituloFinal = tituloPacote ? tituloAlbum+' ('+tituloPacote+')' : tituloAlbum;

      var precoAlbum = Number(variacaoPrincipal ? (variacaoPrincipal.preco !== undefined ? variacaoPrincipal.preco : (modalProdutoAtual.preco||0)) : (modalProdutoAtual.preco||0));
      var precoAlbumComparacaoRaw = Number(variacaoPrincipal ? (variacaoPrincipal.precoComparacao !== undefined ? variacaoPrincipal.precoComparacao : (modalProdutoAtual.precoComparacao||precoAlbum)) : (modalProdutoAtual.precoComparacao||precoAlbum));
      var precoAlbumComparacao = (precoAlbumComparacaoRaw > 0) ? precoAlbumComparacaoRaw : precoAlbum;
      var precoTotal = precoAlbum;
      var precoComparacaoTotal = precoAlbumComparacao;
      var descontoTotal = calcularDesconto(precoTotal, precoComparacaoTotal);

      var imagemAlbum = variacaoPrincipal ? (variacaoPrincipal.imagem || modalProdutoAtual.imagemPrincipal || FALLBACK_MEDIA) : (modalProdutoAtual.imagemPrincipal || FALLBACK_MEDIA);
      var imagemPrincipal = imagemAlbum;
      var fotosPrincipais = buildGalleryImages(imagemPrincipal, [imagemAlbum].concat(Array.isArray(modalProdutoAtual.fotos) ? modalProdutoAtual.fotos : []));

      var badgeEl = document.getElementById('div_ors_badge');
      if (badgeEl) badgeEl.textContent = '-'+Math.round(descontoTotal)+'%';
      var precoEl = document.getElementById('div_ors_3');
      if (precoEl) precoEl.textContent = formatBRL(precoTotal);
      var precoCompEl = document.getElementById('div_ors_2');
      if (precoCompEl) precoCompEl.textContent = (precoComparacaoTotal > precoTotal) ? 'R$ '+formatBRL(precoComparacaoTotal) : '';
      var bilheteText = document.getElementById('modal-bilhete-text');
      if (bilheteText && descontoTotal > 0) bilheteText.textContent = Math.round(descontoTotal)+'% de desconto';
      var ofertaBanner = document.getElementById('modal-oferta-banner');
      if (ofertaBanner) ofertaBanner.style.display = (modalProdutoAtual && modalProdutoAtual.promo_ativa) ? 'flex' : 'none';
      var modalImg = document.getElementById('img-solts');
      if (modalImg) {
        if (usarTamanhoComoPrincipal) {
          modalImg.style.display = 'none';
        } else {
          modalImg.style.display = 'block';
          modalImg.onerror = function(){ modalImg.onerror=null; modalImg.src=FALLBACK_MEDIA; };
          modalImg.src = imagemPrincipal || FALLBACK_MEDIA;
        }
      }

      var checkoutDefault = (modalProdutoAtual && modalProdutoAtual.checkoutUrl) || 'checkout.php';
      var itemAlbum = {
        produtoId: modalProdutoAtual ? (modalProdutoAtual.id !== undefined ? modalProdutoAtual.id : null) : null,
        variacaoId: variacaoPrincipal ? (variacaoPrincipal.id !== undefined ? variacaoPrincipal.id : null) : null,
        tipoVariacao: usarTamanhoComoPrincipal ? 'tamanho' : 'cor',
        titulo: tituloFinal,
        preco: precoAlbum, precoComparacao: precoAlbumComparacao,
        desconto: calcularDesconto(precoAlbum, precoAlbumComparacao),
        link_checkout: variacaoPrincipal ? (variacaoPrincipal.checkoutLink || checkoutDefault) : checkoutDefault,
        imagem: imagemAlbum,
        fotos: buildGalleryImages(imagemAlbum, modalProdutoAtual ? modalProdutoAtual.fotos : []),
        quantidade: 1
      };

      produtoSelecionado = Object.assign({}, itemAlbum, {
        titulo: tituloFinal, preco: precoTotal, precoComparacao: precoComparacaoTotal,
        desconto: descontoTotal, imagem: imagemPrincipal, fotos: fotosPrincipais,
        quantidade: 1, comboItens: [itemAlbum]
      });
    }

    function normalizarProduto(produto) {
      var fotos = Array.isArray(produto.fotos) ? produto.fotos : [];
      var imagemPrincipal = resolveMediaPath(produto.imagemPrincipal || (fotos[0] || produto.imagem || ''));
      var preco = Number(produto.preco || 0);
      var precoComparacao = Number(produto.preco_comparacao || produto.precoComparacao || 0);
      if (!precoComparacao || isNaN(precoComparacao) || precoComparacao <= preco) precoComparacao = preco;
      var desconto = Number(produto.desconto || 0);
      if (!desconto || isNaN(desconto)) desconto = (precoComparacao > preco && precoComparacao > 0) ? Math.round(((precoComparacao - preco) / precoComparacao) * 100) : 0;
      var variacoes = Array.isArray(produto.variacoes) ? produto.variacoes.map(function(v) {
        var vp = Number(v.preco || preco);
        var vc = Number(v.preco_comparacao || v.precoComparacao || precoComparacao || vp);
        if (!vc || isNaN(vc) || vc <= vp) vc = precoComparacao > vp ? precoComparacao : vp;
        var vd = Number(v.desconto || 0);
        if (!vd || isNaN(vd)) vd = (vc > vp && vc > 0) ? Math.round(((vc-vp)/vc)*100) : 0;
        return {
          id: v.id !== undefined ? v.id : null,
          titulo: v.titulo || '', tipo: v.tipo || 'tamanho',
          preco: vp, precoComparacao: vc, desconto: vd,
          info: v.info || '', checkoutLink: v.link_checkout || '',
          imagem: resolveMediaPath(v.imagem || imagemPrincipal)
        };
      }) : [];
      return Object.assign({}, produto, {
        imagemPrincipal: imagemPrincipal, fotos: fotos,
        preco: preco, precoComparacao: precoComparacao, desconto: desconto,
        variacoes: variacoes,
        checkoutUrl: produto.checkoutUrl || produto.link_checkout || 'checkout.php'
      });
    }

    function abrirModalProduto(produto) {
      modalProdutoAtual = produto;
      produtoSelecionado = null;
      variacoesSelecionadasPorTipo = {};
      abrirModal();
      var loader = document.getElementById('modal-loader');
      var conteudo = document.getElementById('modal-conteudo');
      if (loader && conteudo) { loader.classList.remove('hidden'); conteudo.classList.add('hidden'); }

      setTimeout(function() {
        var img = document.getElementById('img-solts');
        var precoEl = document.getElementById('div_ors_3');
        var precoCompEl = document.getElementById('div_ors_2');
        var temCorInicial = Array.isArray(produto.variacoes) && produto.variacoes.some(function(v){ return String(v.tipo||'').toLowerCase()==='cor'; });
        if (img) {
          if (!temCorInicial) { img.style.display = 'none'; }
          else { img.style.display='block'; img.onerror=function(){img.onerror=null;img.src=FALLBACK_MEDIA;}; img.src=produto.imagemPrincipal||FALLBACK_MEDIA; }
        }
        if (precoEl) precoEl.innerHTML = '<span style="color:#fe2d55;font-size:1.3rem;font-weight:700;">R$ '+formatBRL(produto.preco)+'</span>';
        if (precoCompEl) precoCompEl.innerHTML = produto.precoComparacao && produto.precoComparacao > produto.preco ? '<span style="color:#aaa;text-decoration:line-through;font-size:1rem;">R$ '+formatBRL(produto.precoComparacao)+'</span>' : '';

        if (produto.variacoes.length === 0) {
          var gImgs = buildGalleryImages(produto.imagemPrincipal, produto.fotos);
          produtoSelecionado = {
            produtoId: produto.id !== undefined ? produto.id : null, variacaoId: null,
            titulo: produto.titulo, preco: produto.preco, precoComparacao: produto.precoComparacao,
            desconto: produto.desconto, link_checkout: produto.checkoutUrl,
            imagem: gImgs[0] || FALLBACK_MEDIA, fotos: gImgs, quantidade: 1
          };
        }

        document.getElementById('titulo-variacoes').textContent = produto.variacoes.length ? t('variations') : 'Produto';
        renderVariacoes(produto.variacoes);

        if (loader && conteudo) { loader.classList.add('hidden'); conteudo.classList.remove('hidden'); }
      }, 400);
    }

    function comprarAgora() {
      if (!produtoSelecionado) { showCenterToast(t('select_variation_first'), 'error', 2200); return; }
      var qtdInput = document.getElementById('qtd-input');
      var quantidade = 1;
      if (qtdInput) { quantidade = parseInt(qtdInput.value,10); if(!quantidade||quantidade<1) quantidade=1; }
      produtoSelecionado.quantidade = quantidade;

      var itensParaAdicionar = (Array.isArray(produtoSelecionado.comboItens) && produtoSelecionado.comboItens.length)
        ? produtoSelecionado.comboItens : [produtoSelecionado];

      var adicionados = 0, duplicados = 0;
      itensParaAdicionar.forEach(function(itemBase) {
        var item = Object.assign({}, itemBase, { quantidade: quantidade });
        if (window.cart && typeof window.cart.addItem === 'function') {
          var currentItems = window.cart.items || [];
          var existe = currentItems.some(function(p){
            return String(p.titulo||'')===String(item.titulo||'') && String(p.variacaoId||'')===String(item.variacaoId||'');
          });
          if (existe) { duplicados++; return; }
          window.cart.addItem(item);
          adicionados++;
        }
      });

      if (!adicionados) { showCenterToast(t('already_in_cart'), 'info', 2200); return; }
      fecharModal();
      setTimeout(function() {
        renderCart();
        showCenterToast(adicionados > 1 ? t('album_and_pack_added') : t('added_to_cart'), 'success', 1800);
        if (duplicados > 0) setTimeout(function(){ showCenterToast(t('some_items_already_in_cart'), 'info', 1800); }, 350);
      }, 320);
    }

    function openRecModal(id) {
      var produto = _produtosData.find(function(p){ return String(p.id) === String(id); });
      if (!produto) return;
      abrirModalProduto(normalizarProduto(produto));
    }

    document.addEventListener('DOMContentLoaded', function() {
      var imageViewerClose = document.getElementById('imageViewerClose');
      var imageViewerBackdrop = document.getElementById('imageViewerBackdrop');
      var imageViewerPrev = document.getElementById('imageViewerPrev');
      var imageViewerNext = document.getElementById('imageViewerNext');
      if (imageViewerClose) imageViewerClose.addEventListener('click', closeImageViewer);
      if (imageViewerBackdrop) imageViewerBackdrop.addEventListener('click', closeImageViewer);
      if (imageViewerPrev) imageViewerPrev.addEventListener('click', function(){ showImageViewerIndex(galleryIndex-1); });
      if (imageViewerNext) imageViewerNext.addEventListener('click', function(){ showImageViewerIndex(galleryIndex+1); });
      var meuModal = document.getElementById('meuModal');
      if (meuModal) meuModal.addEventListener('click', function(e){ if(e.target===meuModal) fecharModal(); });
    });
    </script>

    <!-- Modal Proteção do Cliente -->
    <style>
      #modalProtecao {
        position: fixed; inset: 0; z-index: 9600;
        background: rgba(0,0,0,0.5);
        display: none; align-items: flex-end; justify-content: center;
      }
      #modalProtecao.show { display: flex; }
      #modalProtecaoPainel {
        background: #f5efe3;
        width: 100%; max-width: 480px; max-height: 90vh;
        overflow-y: auto; -webkit-overflow-scrolling: touch;
        border-radius: 20px 20px 0 0;
        padding: 24px 20px calc(24px + env(safe-area-inset-bottom));
        position: relative;
      }
      .prot-close {
        position: absolute; top: 16px; right: 16px;
        width: 32px; height: 32px; border: none; background: transparent;
        font-size: 22px; color: #555; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
      }
      .prot-watermark {
        position: absolute; top: 10px; right: 20px;
        font-size: 80px; opacity: 0.07; color: #7a5c1e;
        pointer-events: none; line-height: 1;
      }
      .prot-titulo { font-size: 26px; font-weight: 800; color: #7a5c1e; margin-bottom: 20px; line-height: 1.2; }
      .prot-item { margin-bottom: 22px; }
      .prot-item-header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
      .prot-item-icon {
        width: 40px; height: 40px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
      }
      .prot-item-icon img { width: 40px; height: 40px; object-fit: contain; }
      .prot-item-title { font-size: 15px; font-weight: 700; color: #7a5c1e; }
      .prot-item-desc { font-size: 13px; color: #555; line-height: 1.55; margin-bottom: 6px; }
      .prot-link { font-size: 13px; color: #009a85; font-weight: 500; }
      .prot-divider { height: 1px; background: #e0d5c0; margin: 18px 0; }
      .prot-pagamentos { display: flex; flex-wrap: wrap; gap: 6px; margin: 8px 0; }
      .prot-pay-badge {
        height: 28px; padding: 0 10px; border-radius: 6px;
        border: 1px solid #ddd; background: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700; color: #333;
      }
    </style>
    <div id="modalProtecao" onclick="if(event.target===this)fecharModalProtecao()">
      <div id="modalProtecaoPainel">
        <button class="prot-close" onclick="fecharModalProtecao()">×</button>
        <div class="prot-watermark">✓</div>
        <div class="prot-titulo">Proteção<br>do cliente</div>

        <div class="prot-item">
          <div class="prot-item-header">
            <div class="prot-item-icon"><img src="/uploads/devolucao.png?v=1" style="width:40px;height:40px;object-fit:contain;"></div>
            <div class="prot-item-title">Devoluções gratuitas em 30 dias</div>
          </div>
          <p class="prot-item-desc">Devolução gratuita em até 30 dias após o recebimento do seu produto. Os Termos e Condições se aplicam.</p>
          <span class="prot-link">Saiba como solicitar um reembolso</span>
        </div>

        <div class="prot-divider"></div>

        <div class="prot-item">
          <div class="prot-item-header">
            <div class="prot-item-icon"><img src="/uploads/pagamentoseguro.png?v=1" style="width:40px;height:40px;object-fit:contain;"></div>
            <div class="prot-item-title">Pagamento seguro</div>
          </div>
          <p class="prot-item-desc">Para garantir a segurança, as informações do seu cartão são criptografadas e protegidas contra acesso não autorizado.</p>
          <p class="prot-item-desc">Não vendemos, alugamos ou cedemos suas informações pessoais a terceiros para fins de marketing.</p>
          <p class="prot-item-desc" style="margin-bottom:8px;">Aceitamos pagamento de:</p>
          <img src="/uploads/primeiralinhacartao.png?v=1" style="width:100%;max-width:340px;display:block;margin-bottom:14px;">
          <p class="prot-item-desc" style="margin-bottom:8px;">Certificações de segurança:</p>
          <img src="/uploads/segundalinha.png?v=1" style="width:190px;display:block;margin-bottom:14px;">
          <p class="prot-item-desc">Para obter informações sobre como usamos seus dados pessoais, consulte nossa <span style="color:#009a85;">Privacy Policy</span>.</p>
        </div>

        <div class="prot-divider"></div>

        <div class="prot-item">
          <div class="prot-item-header">
            <div class="prot-item-icon"><img src="/uploads/reembolso.png?v=1" style="width:40px;height:40px;object-fit:contain;"></div>
            <div class="prot-item-title">Reembolso se algo der errado</div>
          </div>
          <p class="prot-item-desc">Se o seu pedido for perdido ou danificado durante o transporte antes de chegar, reembolsaremos automaticamente o seu dinheiro. Você não precisa fazer nada.</p>
        </div>

        <div class="prot-divider"></div>

        <div class="prot-item" style="margin-bottom:0;">
          <div class="prot-item-header">
            <div class="prot-item-icon"><img src="/uploads/pedidonaoenviado.png?v=1" style="width:40px;height:40px;object-fit:contain;"></div>
            <div class="prot-item-title">Se o seu pedido não for enviado no prazo</div>
          </div>
          <p class="prot-item-desc">Você não precisa fazer nada. Se ele não for despachado em até 7 dias úteis, cancelaremos o seu pedido e reembolsaremos automaticamente o seu dinheiro.</p>
        </div>
      </div>
    </div>
    <script>
    function abrirModalProtecao() {
      var m = document.getElementById('modalProtecao');
      if (!m) return;
      m.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
    function fecharModalProtecao() {
      var m = document.getElementById('modalProtecao');
      if (m) m.classList.remove('show');
      document.body.style.removeProperty('overflow');
    }
    </script>
</body>
</html>