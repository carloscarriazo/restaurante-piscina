<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌊 Blue Lagoon - Carta</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4ECDC4, #45B7D1);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .menu-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
        }

        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 20px;
        }

        h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 300;
            letter-spacing: 2px;
        }

        .subtitle {
            color: #7f8c8d;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .menu-options {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .menu-button {
            background: linear-gradient(135deg, #4ECDC4, #45B7D1);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .menu-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        .qr-button {
            background: linear-gradient(135deg, #F39C12, #E67E22);
        }

        .contact-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ecf0f1;
            color: #7f8c8d;
        }

        .whatsapp {
            color: #25D366;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="menu-container">
        <img src="/images/blue-lagoon-logo.svg" alt="Blue Lagoon Logo" class="logo">
        <h1>BLUE LAGOON</h1>
        <p class="subtitle">Restaurante & Bar - Morroa, Sucre</p>

        <div class="menu-options">
            <a href="{{ route('menu.digital') }}" class="menu-button">
                📱 Ver Carta Digital
            </a>

            <a href="{{ route('menu.qr') }}" class="menu-button qr-button">
                📷 Generar Código QR
            </a>
        </div>

        <div class="contact-info">
            <p>📍 Morroa, Sucre</p>
            <p class="whatsapp">📞 301 307 4861</p>
            <p>🕒 Abierto todos los días</p>
        </div>
    </div>
</body>
</html>
