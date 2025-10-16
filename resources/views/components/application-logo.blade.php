<svg viewBox="0 0 400 120" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
  <!-- Water Waves Background -->
  <defs>
    <linearGradient id="waveGradient" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" style="stop-color:#3282b8;stop-opacity:1" />
      <stop offset="50%" style="stop-color:#bbe1fa;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#0f4c75;stop-opacity:1" />
    </linearGradient>
    <linearGradient id="textGradient" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" style="stop-color:#ffffff;stop-opacity:1" />
      <stop offset="50%" style="stop-color:#bbe1fa;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#ffffff;stop-opacity:1" />
    </linearGradient>
  </defs>

  <!-- Wave Background -->
  <path d="M0,50 Q100,20 200,50 T400,50 L400,120 L0,120 Z" fill="url(#waveGradient)" opacity="0.3"/>
  <path d="M0,60 Q100,30 200,60 T400,60 L400,120 L0,120 Z" fill="url(#waveGradient)" opacity="0.5"/>

  <!-- Pool/Lagoon Icon -->
  <circle cx="60" cy="50" r="25" fill="#3282b8" stroke="#bbe1fa" stroke-width="3"/>
  <circle cx="60" cy="50" r="18" fill="#bbe1fa" opacity="0.7"/>
  <circle cx="60" cy="50" r="12" fill="#0f4c75" opacity="0.5"/>

  <!-- Ripple Effects -->
  <circle cx="60" cy="50" r="30" fill="none" stroke="#bbe1fa" stroke-width="1" opacity="0.4"/>
  <circle cx="60" cy="50" r="35" fill="none" stroke="#bbe1fa" stroke-width="1" opacity="0.2"/>

  <!-- Palm Leaves (stylized) -->
  <path d="M40,35 Q45,25 55,30 Q50,40 40,35" fill="#0f4c75" opacity="0.6"/>
  <path d="M75,35 Q80,25 70,30 Q75,40 75,35" fill="#0f4c75" opacity="0.6"/>

  <!-- Restaurant Text -->
  <text x="110" y="35" font-family="Playfair Display, serif" font-size="20" font-weight="600" fill="url(#textGradient)">Blue Lagoon</text>
  <text x="110" y="55" font-family="Inter, sans-serif" font-size="12" font-weight="400" fill="#bbe1fa" opacity="0.9">RESTAURANT</text>
  <text x="110" y="75" font-family="Inter, sans-serif" font-size="10" font-weight="300" fill="#ffffff" opacity="0.7">Sistema de Gestión</text>

  <!-- Decorative Elements -->
  <circle cx="320" cy="30" r="3" fill="#bbe1fa" opacity="0.6">
    <animate attributeName="opacity" values="0.6;1;0.6" dur="2s" repeatCount="indefinite"/>
  </circle>
  <circle cx="340" cy="45" r="2" fill="#3282b8" opacity="0.8">
    <animate attributeName="opacity" values="0.8;1;0.8" dur="3s" repeatCount="indefinite"/>
  </circle>
  <circle cx="360" cy="35" r="2.5" fill="#bbe1fa" opacity="0.7">
    <animate attributeName="opacity" values="0.7;1;0.7" dur="2.5s" repeatCount="indefinite"/>
  </circle>
</svg>
