<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌊 Blue Lagoon - Código QR</title>
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

        .qr-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 100%;
        }

        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 20px;
        }

        h1 {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .subtitle {
            color: #7f8c8d;
            font-size: 1rem;
            margin-bottom: 30px;
        }

        .qr-code {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .instructions {
            color: #7f8c8d;
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 20px 0;
        }

        .back-button {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            margin-top: 20px;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .print-button {
            background: linear-gradient(135deg, #F39C12, #E67E22);
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            margin: 10px;
        }

        .print-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        @media print {
            body {
                background: white;
            }
            .qr-container {
                box-shadow: none;
                background: white;
            }
            .back-button, .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <img src="/images/blue-lagoon-logo.svg" alt="Blue Lagoon Logo" class="logo">
        <h1>Código QR - Carta Digital</h1>
        <p class="subtitle">Escanea para ver nuestra carta</p>

        <div class="qr-code">
            @php
                $menuUrl = route('menu.digital');
                $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($menuUrl);
            @endphp
            <img src="{{ $qrCodeUrl }}" alt="Código QR Carta Digital" style="max-width: 200px; height: auto;">
        </div>

        <div class="instructions">
            <p>📱 <strong>Instrucciones:</strong></p>
            <p>1. Abre la cámara de tu teléfono</p>
            <p>2. Apunta al código QR</p>
            <p>3. Toca el enlace que aparece</p>
            <p>4. ¡Disfruta navegando nuestra carta!</p>
        </div>

        <a href="javascript:window.print()" class="print-button">
            🖨️ Imprimir QR
        </a>

        <a href="{{ route('menu.index') }}" class="back-button">
            ← Volver al Menú
        </a>

        <div style="margin-top: 20px; color: #7f8c8d; font-size: 0.8rem;">
            <p>🌊 Blue Lagoon - Morroa, Sucre</p>
            <p>📞 301 307 4861</p>
        </div>
    </div>
</body>
</html>
