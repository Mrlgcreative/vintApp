<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#111827" media="(prefers-color-scheme: dark)">
<title><?php echo e(config('app.name', 'VintApp')); ?></title>
<link rel="preconnect" href="<?php echo e(route('home')); ?>">

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', sans-serif;
  overflow: hidden;
  background: #ffffff;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  height: 100dvh;
  position: relative;
}

/* ─── Dark mode ─── */
@media (prefers-color-scheme: dark) {
  body { background: #111827; }
  .tagline { color: #9ca3af; }
  .splash-progress-track { background: rgba(255,255,255,0.08); }
  .splash-progress-bar { background: linear-gradient(90deg, #7c3aed, #a855f7); }
  .cart-circle { background: linear-gradient(135deg, rgba(124, 58, 237, 0.15), rgba(99, 102, 241, 0.15)); }
  .splash-hint { color: rgba(255,255,255,0.3); }
}

/* ─── Container principal ─── */
#splashContainer {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

/* ─── Zone du logo panier ─── */
.cart-logo {
  position: relative;
  width: 200px;
  height: 200px;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* ─── SVG Panier ─── */
.cart-svg {
  width: 150px;
  height: 150px;
  opacity: 0;
  transform: scale(0) rotate(-180deg);
  animation: cartAppear 1s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s forwards;
  filter: drop-shadow(0 4px 16px rgba(124, 58, 237, 0.2));
  will-change: transform, opacity;
}

.cart-svg path,
.cart-svg circle,
.cart-svg rect,
.cart-svg line,
.cart-svg polyline,
.cart-svg ellipse {
  stroke: #7c3aed;
  stroke-width: 2.8;
  fill: none;
  stroke-linecap: round;
  stroke-linejoin: round;
}

.cart-svg .cart-fill {
  fill: #7c3aed;
  fill-opacity: 0;
  animation: cartFill 0.6s ease-out 1.2s forwards;
}

/* Stroke draw-in animation */
.cart-svg .draw-in {
  stroke-dasharray: 200;
  stroke-dashoffset: 200;
  animation: strokeDraw 1s cubic-bezier(0.4, 0, 0.2, 1) 0.4s forwards;
}

.cart-svg .draw-in-delay {
  stroke-dasharray: 200;
  stroke-dashoffset: 200;
  animation: strokeDraw 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.7s forwards;
}

/* Articles pop-in séquentiel */
.cart-svg .item-1 { opacity: 0; animation: itemPop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) 1.0s forwards; }
.cart-svg .item-2 { opacity: 0; animation: itemPop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) 1.15s forwards; }
.cart-svg .item-3 { opacity: 0; animation: itemPop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) 1.3s forwards; }

/* Badge prix bounce */
.cart-svg .price-tag { opacity: 0; animation: tagBounce 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 1.45s forwards; }

/* ─── Cercle de fond pulsant ─── */
.cart-circle {
  position: absolute;
  width: 180px;
  height: 180px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(124, 58, 237, 0.08), rgba(99, 102, 241, 0.08));
  opacity: 0;
  animation: circleGrow 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.3s forwards,
             circlePulse 2.5s ease-in-out 1.2s infinite;
  z-index: -1;
}

/* ─── Nom de l'app ─── */
.app-name {
  display: flex;
  align-items: baseline;
  margin-top: 8px;
  gap: 0;
  position: relative;
}

.app-name .name-part {
  font-size: 48px;
  font-weight: 900;
  letter-spacing: 0.03em;
  text-transform: uppercase;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.app-name .name-vint {
  background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
  -webkit-background-clip: text;
  background-clip: text;
  opacity: 0;
  transform: translateX(10px);
  animation: nameSlideLeft 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 1.4s forwards;
}

.app-name .name-app {
  background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
  -webkit-background-clip: text;
  background-clip: text;
  opacity: 0;
  transform: translateX(-10px);
  animation: nameSlideRight 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 1.55s forwards;
}

/* ─── Tagline ─── */
.tagline {
  font-size: 13px;
  font-weight: 500;
  color: #6b7280;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  margin-top: 10px;
  opacity: 0;
  animation: fadeUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) 1.8s forwards;
}

/* ─── Barre de progression ─── */
.splash-progress {
  margin-top: 32px;
  width: 120px;
  opacity: 0;
  animation: fadeUp 0.4s cubic-bezier(0.4, 0, 0.2, 1) 2.1s forwards;
}

.splash-progress-track {
  width: 100%;
  height: 3px;
  background: rgba(0,0,0,0.06);
  border-radius: 3px;
  overflow: hidden;
}

.splash-progress-bar {
  width: 0%;
  height: 100%;
  background: linear-gradient(90deg, #7c3aed, #a855f7);
  border-radius: 3px;
  animation: progressFill 1.8s cubic-bezier(0.4, 0, 0.2, 1) 2.2s forwards;
}

/* ─── Hint "Appuyez pour continuer" ─── */
.splash-hint {
  position: fixed;
  bottom: 32px;
  left: 0;
  right: 0;
  text-align: center;
  font-size: 12px;
  color: rgba(0,0,0,0.25);
  letter-spacing: 0.05em;
  opacity: 0;
  animation: fadeUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) 2.6s forwards;
}

/* ═══════════════════════════════════════
   Keyframes
   ═══════════════════════════════════════ */

@keyframes cartAppear {
  0% {
    opacity: 0;
    transform: scale(0) rotate(-180deg);
  }
  70% {
    opacity: 1;
    transform: scale(1.08) rotate(5deg);
  }
  100% {
    opacity: 1;
    transform: scale(1) rotate(0deg);
  }
}

@keyframes cartFill {
  to { fill-opacity: 0.12; }
}

@keyframes strokeDraw {
  to { stroke-dashoffset: 0; }
}

@keyframes itemPop {
  0% { opacity: 0; transform: scale(0) translateY(4px); }
  70% { transform: scale(1.15) translateY(0); }
  100% { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes tagBounce {
  0% { opacity: 0; transform: scale(0) rotate(-20deg); }
  60% { transform: scale(1.2) rotate(5deg); }
  100% { opacity: 1; transform: scale(1) rotate(0deg); }
}

@keyframes circleGrow {
  from {
    opacity: 0;
    transform: scale(0);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes circlePulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.06); opacity: 0.7; }
}

@keyframes nameSlideLeft {
  0% {
    opacity: 0;
    transform: translateX(10px);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes nameSlideRight {
  0% {
    opacity: 0;
    transform: translateX(-10px);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes progressFill {
  0% { width: 0%; }
  40% { width: 60%; }
  100% { width: 100%; }
}

@keyframes fadeOutZoom {
  0% {
    opacity: 1;
    transform: scale(1);
    filter: blur(0);
  }
  100% {
    opacity: 0;
    transform: scale(1.1);
    filter: blur(16px);
  }
}

.outro {
  animation: fadeOutZoom 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

/* ═══════════════════════════════════════
   Responsive
   ═══════════════════════════════════════ */

@media (max-width: 640px) {
  .cart-logo {
    width: 160px;
    height: 160px;
  }
  .cart-svg {
    width: 120px;
    height: 120px;
  }
  .cart-circle {
    width: 140px;
    height: 140px;
  }
  .app-name .name-part {
    font-size: 36px;
  }
  .tagline {
    font-size: 11px;
  }
  .splash-progress {
    width: 100px;
  }
  .splash-hint {
    bottom: 24px;
  }
}

/* ═══════════════════════════════════════
   prefers-reduced-motion
   ═══════════════════════════════════════ */

@media (prefers-reduced-motion: reduce) {
  .cart-svg {
    animation: none;
    opacity: 1;
    transform: none;
  }
  .cart-svg .cart-fill {
    animation: none;
    fill-opacity: 0.12;
  }
  .cart-svg .draw-in,
  .cart-svg .draw-in-delay {
    animation: none;
    stroke-dashoffset: 0;
  }
  .cart-svg .item-1,
  .cart-svg .item-2,
  .cart-svg .item-3,
  .cart-svg .price-tag {
    animation: none;
    opacity: 1;
  }
  .cart-circle {
    animation: none;
    opacity: 1;
    transform: none;
  }
  .app-name .name-vint,
  .app-name .name-app {
    animation: none;
    opacity: 1;
    transform: none;
  }
  .tagline,
  .splash-progress,
  .splash-hint {
    animation: none;
    opacity: 1;
    transform: none;
  }
  .splash-progress-bar {
    animation: none;
    width: 100%;
  }
  .outro {
    animation-duration: 0.2s;
  }
}
</style>
</head>
<body>

<div id="splashContainer" role="status" aria-label="Chargement de <?php echo e(config('app.name', 'VintApp')); ?>">
  
  <!-- Logo panier -->
  <div class="cart-logo">
    <div class="cart-circle"></div>
    
    <svg class="cart-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <!-- Sac shopping — corps principal -->
      <path class="cart-fill draw-in" 
            d="M 24 38 L 24 78 C 24 82 27 85 31 85 L 69 85 C 73 85 76 82 76 78 L 76 38 Z"
            stroke-width="3"/>
      
      <!-- Anse gauche -->
      <path class="draw-in-delay" 
            d="M 36 38 C 36 24 42 18 50 18 C 58 18 64 24 64 38" 
            stroke-width="3" fill="none"/>
      
      <!-- Ligne d'ouverture du sac -->
      <line class="draw-in" x1="22" y1="38" x2="78" y2="38" stroke-width="3.2"/>

      <!-- ─── Articles dans le sac ─── -->
      
      <!-- Article 1 : T-shirt plié -->
      <g class="item-1">
        <rect x="32" y="48" width="14" height="10" rx="2" stroke-width="2" class="cart-fill"/>
        <line x1="35" y1="51" x2="43" y2="51" stroke-width="1.5" stroke="#7c3aed" opacity="0.5"/>
        <line x1="36" y1="54" x2="42" y2="54" stroke-width="1.5" stroke="#7c3aed" opacity="0.5"/>
      </g>
      
      <!-- Article 2 : Cintre avec vêtement -->
      <g class="item-2">
        <path d="M 56 46 L 62 50 L 56 50 Z" stroke-width="1.8" class="cart-fill"/>
        <line x1="59" y1="44" x2="59" y2="46" stroke-width="2"/>
        <path d="M 53 50 L 53 60 L 65 60 L 65 50" stroke-width="2" class="cart-fill"/>
      </g>
      
      <!-- Article 3 : Sneaker/chaussure stylisée -->
      <g class="item-3">
        <path d="M 36 64 C 36 62 38 61 40 61 L 52 61 C 54 61 56 62 56 64 L 56 68 C 56 70 54 72 52 72 L 36 72 C 34 72 33 70 34 68 Z" 
              stroke-width="1.8" class="cart-fill"/>
        <circle cx="40" cy="67" r="1.2" fill="#7c3aed" stroke="none"/>
        <circle cx="46" cy="67" r="1.2" fill="#7c3aed" stroke="none"/>
      </g>

      <!-- Badge étiquette prix -->
      <g class="price-tag">
        <circle cx="74" cy="30" r="10" fill="#7c3aed" stroke="none"/>
        <text x="74" y="31" text-anchor="middle" dominant-baseline="middle" 
              fill="white" font-size="9" font-weight="700" font-family="system-ui, sans-serif"
              stroke="none">%</text>
      </g>
    </svg>
  </div>
  
  <h1 class="app-name">
    <span class="name-part name-vint">Vint</span><span class="name-part name-app">App</span>
  </h1>
  <p class="tagline">Vends · Achète · En confiance</p>

  <!-- Barre de progression -->
  <div class="splash-progress">
    <div class="splash-progress-track">
      <div class="splash-progress-bar"></div>
    </div>
  </div>
</div>

<p class="splash-hint">Appuyez pour continuer</p>

<script>
(function() {
  var container = document.getElementById('splashContainer');
  var redirecting = false;
  var homeUrl = <?php echo json_encode(route('home'), 15, 512) ?>;

  function goHome() {
    if (redirecting) return;
    redirecting = true;
    container.classList.add('outro');
    setTimeout(function() {
      window.location.href = homeUrl;
    }, 600);
  }

  // Auto-redirect après la fin des animations + progress bar
  var timer = setTimeout(goHome, 4200);

  // Skip au clic/touch
  function skip() {
    clearTimeout(timer);
    goHome();
  }

  document.addEventListener('click', skip);
  document.addEventListener('touchstart', skip, { passive: true });
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' || e.key === ' ' || e.key === 'Escape') skip();
  });
})();
</script>

</body>
</html>
<?php /**PATH D:\Mes projets\vintApp\resources\views/splash.blade.php ENDPATH**/ ?>