<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌊 Restaurante Piscina - Menú Digital Ocean</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            color: #f1f5f9;
            position: relative;
            overflow-x: hidden;
        }

        /* Partículas Ocean */
        .ocean-particle {
            position: fixed;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.8) 0%, rgba(14, 165, 233, 0.4) 50%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
            animation: particleFloat 20s infinite ease-in-out;
        }

        @keyframes particleFloat {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.6; }
            25% { transform: translate(30px, -30px) scale(1.1); opacity: 0.8; }
            50% { transform: translate(-20px, -60px) scale(0.9); opacity: 0.7; }
            75% { transform: translate(-40px, -30px) scale(1.05); opacity: 0.75; }
        }

        /* Gradiente de fondo animado */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 30%, rgba(6, 182, 212, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(14, 165, 233, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            z-index: 1;
            pointer-events: none;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.1); }
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
            z-index: 10;
        }

        /* Header Ocean */
        .header {
            text-align: center;
            margin-bottom: 40px;
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 2px solid rgba(6, 182, 212, 0.3);
            border-radius: 24px;
            padding: 40px 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), 0 0 60px rgba(6, 182, 212, 0.2);
            position: relative;
            overflow: hidden;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg,
                #06b6d4 0%,
                #0ea5e9 25%,
                #3b82f6 50%,
                #0ea5e9 75%,
                #06b6d4 100%);
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(to top, rgba(6, 182, 212, 0.1), transparent);
            pointer-events: none;
        }

        .logo-circle {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #06b6d4, #0ea5e9, #3b82f6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 40px rgba(6, 182, 212, 0.4);
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 10px 40px rgba(6, 182, 212, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 15px 60px rgba(6, 182, 212, 0.6); }
        }

        .logo-circle i {
            font-size: 3rem;
            color: white;
        }

        .header h1 {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #06b6d4, #0ea5e9, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            letter-spacing: -1px;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 1.2rem;
            color: #94a3b8;
            margin-bottom: 25px;
            font-weight: 500;
            letter-spacing: 1px;
        }

        .contact-info {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .contact-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(6, 182, 212, 0.1);
            border: 1px solid rgba(6, 182, 212, 0.3);
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 0.95rem;
            color: #e2e8f0;
            transition: all 0.3s ease;
        }

        .contact-badge:hover {
            background: rgba(6, 182, 212, 0.2);
            border-color: rgba(6, 182, 212, 0.5);
            transform: translateY(-2px);
        }

        .contact-badge i {
            color: #06b6d4;
            font-size: 1.1rem;
        }

        .contact-badge.whatsapp {
            background: rgba(37, 211, 102, 0.1);
            border-color: rgba(37, 211, 102, 0.3);
        }

        .contact-badge.whatsapp:hover {
            background: rgba(37, 211, 102, 0.2);
            border-color: rgba(37, 211, 102, 0.5);
        }

        .contact-badge.whatsapp i {
            color: #25D366;
        }

        /* Botón flotante WhatsApp */
        .floating-whatsapp {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: white;
            width: 65px;
            height: 65px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            text-decoration: none;
            box-shadow: 0 8px 30px rgba(37, 211, 102, 0.5);
            z-index: 1000;
            animation: pulse 3s infinite;
            transition: all 0.3s ease;
        }

        .floating-whatsapp:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 40px rgba(37, 211, 102, 0.7);
        }

        /* Navegación de Categorías */
        .categories-nav {
            position: sticky;
            top: 20px;
            z-index: 100;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
            margin: 40px 0;
            padding: 20px;
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .category-link {
            padding: 12px 24px;
            background: rgba(6, 182, 212, 0.1);
            color: #06b6d4;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(6, 182, 212, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .category-link:hover {
            background: rgba(6, 182, 212, 0.2);
            border-color: #06b6d4;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(6, 182, 212, 0.3);
        }

        .category-link i {
            font-size: 1rem;
        }

        /* Mensaje de cierre */
        .closed-notice {
            background: rgba(239, 68, 68, 0.1);
            border: 2px solid rgba(239, 68, 68, 0.3);
            border-radius: 20px;
            padding: 40px;
            margin: 30px 0;
            text-align: center;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .closed-notice h3 {
            color: #ef4444;
            font-size: 2rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .closed-notice p {
            color: #e2e8f0;
            font-size: 1.1rem;
            margin: 10px 0;
            line-height: 1.6;
        }

        .closed-notice strong {
            color: #06b6d4;
        }

        /* Sección del menú */
        .menu-section {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 24px;
            margin-bottom: 30px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .category-header {
            padding: 30px;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.2), rgba(14, 165, 233, 0.2));
            border-bottom: 2px solid rgba(6, 182, 212, 0.3);
            position: relative;
            overflow: hidden;
        }

        .category-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
            animation: shimmer 3s infinite;
        }

        .category-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #06b6d4;
            margin: 0;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .category-header h2 i {
            font-size: 1.8rem;
        }

        .category-description {
            font-style: italic;
            color: #94a3b8;
            margin: 20px 0;
            text-align: center;
            padding: 20px;
            background: rgba(6, 182, 212, 0.05);
            border-radius: 12px;
            border: 1px dashed rgba(6, 182, 212, 0.2);
        }

        .menu-items {
            padding: 30px;
        }

        .items-grid {
            display: grid;
            gap: 20px;
        }

        .menu-item {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #06b6d4, #0ea5e9, #3b82f6);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .menu-item:hover {
            background: rgba(15, 23, 42, 0.7);
            border-color: #06b6d4;
            transform: translateX(8px);
            box-shadow: 0 8px 30px rgba(6, 182, 212, 0.2);
        }

        .menu-item:hover::before {
            opacity: 1;
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            gap: 15px;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: #e2e8f0;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .item-size {
            font-size: 0.9rem;
            color: #64748b;
            font-style: italic;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .item-size i {
            color: #06b6d4;
        }

        .item-description {
            font-size: 0.95rem;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 12px;
        }

        .item-price {
            font-size: 2rem;
            font-weight: 800;
            color: #06b6d4;
            white-space: nowrap;
            text-shadow: 0 0 20px rgba(6, 182, 212, 0.5);
        }

        .item-price::before {
            content: '$';
            font-size: 1.3rem;
            vertical-align: super;
            margin-right: 2px;
        }

        .available-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #22c55e;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .available-badge i {
            font-size: 0.9rem;
        }

        .item-image {
            margin-top: 15px;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid rgba(6, 182, 212, 0.3);
        }

        .item-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .menu-item:hover .item-image img {
            transform: scale(1.05);
        }

        .featured-item {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(251, 191, 36, 0.1));
            border: 2px solid rgba(245, 158, 11, 0.4);
        }

        .featured-item::after {
            content: '⭐ Destacado';
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            color: #1e293b;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Mensaje vacío */
        .empty-notice {
            background: rgba(59, 130, 246, 0.1);
            border: 2px solid rgba(59, 130, 246, 0.3);
            border-radius: 20px;
            padding: 60px 40px;
            margin: 30px 0;
            text-align: center;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .empty-notice i {
            font-size: 4rem;
            color: #3b82f6;
            margin-bottom: 20px;
            display: block;
        }

        .empty-notice h3 {
            color: #3b82f6;
            font-size: 2rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .empty-notice p {
            color: #e2e8f0;
            font-size: 1.1rem;
            margin: 10px 0;
            line-height: 1.6;
        }

        /* Sección QR */
        .qr-section {
            text-align: center;
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 24px;
            padding: 40px;
            margin-top: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .qr-section h3 {
            color: #06b6d4;
            font-size: 2rem;
            margin-bottom: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .qr-section h3 i {
            font-size: 1.8rem;
        }

        .qr-section > p {
            color: #94a3b8;
            font-size: 1.1rem;
            margin-bottom: 25px;
        }

        #qrcode {
            background: white;
            padding: 20px;
            border-radius: 16px;
            display: inline-block;
            box-shadow: 0 10px 40px rgba(6, 182, 212, 0.3);
        }

        .qr-link {
            margin-top: 20px;
            padding: 20px;
            background: rgba(6, 182, 212, 0.05);
            border: 1px dashed rgba(6, 182, 212, 0.3);
            border-radius: 12px;
        }

        .qr-link p {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .qr-link strong {
            color: #06b6d4;
            font-size: 1rem;
            word-break: break-all;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 50px;
            padding: 30px;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 24px;
            color: #94a3b8;
        }

        .footer p {
            font-size: 1rem;
            margin: 8px 0;
            line-height: 1.6;
        }

        .footer strong {
            color: #06b6d4;
        }

        .footer i {
            color: #0ea5e9;
            margin: 0 5px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .container {
                padding: 15px;
            }

            .header h1 {
                font-size: 2.5rem;
            }

            .category-header h2 {
                font-size: 1.6rem;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .subtitle {
                font-size: 1rem;
            }

            .contact-info {
                flex-direction: column;
                gap: 12px;
            }

            .contact-badge {
                width: 100%;
                justify-content: center;
            }

            .categories-nav {
                padding: 15px;
                gap: 8px;
            }

            .category-link {
                padding: 10px 18px;
                font-size: 0.9rem;
            }

            .category-header {
                padding: 20px;
            }

            .category-header h2 {
                font-size: 1.4rem;
            }

            .menu-items {
                padding: 20px;
            }

            .menu-item {
                padding: 15px;
            }

            .item-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .item-name {
                font-size: 1.2rem;
            }

            .item-price {
                font-size: 1.6rem;
                margin-top: 10px;
            }

            .floating-whatsapp {
                width: 55px;
                height: 55px;
                font-size: 1.6rem;
                bottom: 20px;
                right: 20px;
            }

            .qr-section {
                padding: 30px 20px;
            }

            .qr-section h3 {
                font-size: 1.6rem;
            }
        }

        /* Animaciones de entrada */
        .animate-in {
            animation: slideInUp 0.6s ease forwards;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Partículas Ocean -->
    <div class="ocean-particle" style="width: 300px; height: 300px; top: 10%; left: 5%; animation-delay: 0s;"></div>
    <div class="ocean-particle" style="width: 200px; height: 200px; top: 60%; left: 80%; animation-delay: 3s;"></div>
    <div class="ocean-particle" style="width: 250px; height: 250px; top: 40%; left: 70%; animation-delay: 6s;"></div>
    <div class="ocean-particle" style="width: 180px; height: 180px; top: 80%; left: 15%; animation-delay: 9s;"></div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-circle">
                <i class="fas fa-swimming-pool"></i>
            </div>
            <h1>Restaurante Piscina</h1>
            <p class="subtitle">Bar & Restaurante - Morroa, Sucre</p>
            <div class="contact-info">
                <span class="contact-badge">
                    <i class="fas fa-map-marker-alt"></i>
                    Morroa, Sucre
                </span>
                <span class="contact-badge whatsapp">
                    <i class="fab fa-whatsapp"></i>
                    301 307 4861
                </span>
                <span class="contact-badge">
                    <i class="fas fa-clock"></i>
                    Viernes a Domingo
                </span>
            </div>
        </div>

        <!-- Mensaje si no es día de operación -->
        @if(!$isOperatingDay)
            <div class="closed-notice">
                <i class="fas fa-calendar-times"></i>
                <h3>Actualmente Cerrado</h3>
                <p>
                    Hoy es <strong>{{ $dayName }}</strong>
                </p>
                <p>
                    Estamos abiertos los <strong>Viernes, Sábados y Domingos</strong>
                </p>
                <p style="color: #94a3b8; margin-top: 20px;">
                    <i class="fas fa-water"></i> ¡Te esperamos este fin de semana!
                </p>
            </div>
        @endif

        <!-- Navegación de Categorías -->
        @if($categories->count() > 0)
        <div class="categories-nav">
            @foreach($categories as $category)
                <a href="#category-{{ $category['id'] }}" class="category-link">
                    <i class="fas fa-utensils"></i>
                    {{ $category['nombre'] }}
                </a>
            @endforeach
        </div>

        <!-- Categorías con productos -->
        @foreach($categories as $category)
            <div class="menu-section animate-in" id="category-{{ $category['id'] }}" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                <div class="category-header">
                    <h2>
                        <i class="fas fa-concierge-bell"></i>
                        {{ $category['nombre'] }}
                    </h2>
                </div>

                <div class="menu-items">
                    @if($category['descripcion'])
                        <p class="category-description">
                            <i class="fas fa-info-circle"></i>
                            {{ $category['descripcion'] }}
                        </p>
                    @endif

                    <div class="items-grid">
                        @foreach($category['items'] as $item)
                            <div class="menu-item {{ $item['featured'] ?? false ? 'featured-item' : '' }}">
                                <div class="item-header">
                                    <div class="item-info">
                                        <h3 class="item-name">{{ $item['name'] }}</h3>

                                        @if(!empty($item['size']))
                                            <p class="item-size">
                                                <i class="fas fa-ruler"></i>
                                                {{ $item['size'] }}
                                            </p>
                                        @endif

                                        @if($item['description'])
                                            <p class="item-description">{{ $item['description'] }}</p>
                                        @endif

                                        @if($item['available'])
                                            <span class="available-badge">
                                                <i class="fas fa-check-circle"></i>
                                                Disponible
                                            </span>
                                        @endif
                                    </div>
                                    <span class="item-price">{{ number_format($item['price'], 0, ',', '.') }}</span>
                                </div>

                                @if($item['image_url'])
                                    <div class="item-image">
                                        <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" loading="lazy">
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
        @else
            <div class="empty-notice">
                <i class="fas fa-utensils"></i>
                <h3>Menú no disponible</h3>
                <p>
                    En este momento no hay ítems disponibles en el menú.
                </p>
                <p style="color: #94a3b8; margin-top: 15px;">
                    Por favor, contáctanos para más información.
                </p>
            </div>
        @endif

        <!-- Sección QR -->
        <div class="qr-section">
            <h3>
                <i class="fas fa-qrcode"></i>
                ¡Comparte nuestro menú!
            </h3>
            <p>Escanea el código QR para acceder a nuestro menú digital</p>
            <div style="margin: 25px 0;">
                <div id="qrcode"></div>
            </div>
            <div class="qr-link">
                <p>O comparte este enlace:</p>
                <strong>{{ url('/menu/digital') }}</strong>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <i class="fas fa-copyright"></i>
                2025 <strong>Restaurante Piscina</strong> - Bar & Restaurante
            </p>
            <p>
                <i class="fas fa-water"></i>
                "Donde el sabor se encuentra con la tradición"
                <i class="fas fa-water"></i>
            </p>
            <p style="font-size: 0.9rem; margin-top: 15px; color: #64748b;">
                <i class="fas fa-code"></i>
                Desarrollado con Ocean Theme
            </p>
        </div>
    </div>

    <!-- WhatsApp flotante -->
    <a href="https://wa.me/573013074861" class="floating-whatsapp" target="_blank" aria-label="Contactar por WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- QR Code Generator -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        // Generar código QR
        const qrElement = document.getElementById('qrcode');
        if (qrElement) {
            const url = '{{ url("/menu/digital") }}';
            QRCode.toCanvas(qrElement, url, {
                width: 220,
                margin: 2,
                color: {
                    dark: '#0f172a',
                    light: '#ffffff'
                }
            });
        }

        // Animación de scroll suave
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animación al hacer scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.menu-section').forEach((section) => {
            observer.observe(section);
        });
    </script>
</body>
</html>
