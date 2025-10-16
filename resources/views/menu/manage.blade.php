<x-app-layout>
    <x-slot name="title">Gestión de Menú</x-slot>

    <style>
        .ocean-header-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .ocean-btn-primary {
            background: linear-gradient(135deg, #06b6d4, #0ea5e9);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);
        }

        .ocean-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(6, 182, 212, 0.5);
        }

        .ocean-btn-secondary {
            background: linear-gradient(135deg, #8b5cf6, #a78bfa);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .ocean-btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.5);
        }

        .ocean-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.875rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            background: rgba(6, 182, 212, 0.2);
            color: #06b6d4;
            border: 1px solid rgba(6, 182, 212, 0.3);
        }

        @media (max-width: 768px) {
            .ocean-header-card {
                padding: 1.5rem;
            }

            .ocean-btn-primary,
            .ocean-btn-secondary {
                padding: 0.625rem 1.25rem;
                font-size: 0.875rem;
            }
        }
    </style>

    <!-- Header -->
    <div class="ocean-header-card">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">
                    <i class="fas fa-utensils mr-3"></i>
                    Gestión de Menú
                </h1>
                <p class="text-gray-300 flex flex-wrap items-center gap-2">
                    <span>Administra los ítems del menú del restaurante</span>
                    <span class="ocean-badge">
                        <i class="fas fa-calendar"></i>
                        <span>Viernes - Domingo</span>
                    </span>
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('menu.digital.preview') }}" target="_blank" class="ocean-btn-primary">
                    <i class="fas fa-eye"></i>
                    <span>Ver Menú</span>
                </a>
                <a href="{{ route('menu.qr') }}" target="_blank" class="ocean-btn-secondary">
                    <i class="fas fa-qrcode"></i>
                    <span>QR</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Componente Livewire -->
    @livewire('menu-manager')

</x-app-layout>
