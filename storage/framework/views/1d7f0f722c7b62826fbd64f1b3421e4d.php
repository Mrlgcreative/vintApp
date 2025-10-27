<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<title><?php echo e(config('app.name', 'VintApp')); ?></title>

<script src="https://cdn.tailwindcss.com"></script>

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
  position: relative;
}

/* Container principal */
#splashContainer {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

/* Zone du logo panier */
.cart-logo {
  position: relative;
  width: 200px;
  height: 200px;
  margin-bottom: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Zone du logo panier */
.cart-logo {
  position: relative;
  width: 200px;
  height: 200px;
  margin-bottom: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* SVG Panier */
.cart-svg {
  width: 160px;
  height: 160px;
  opacity: 0;
  transform: scale(0) rotate(-180deg);
  animation: cartAppear 1.2s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.3s forwards;
}

.cart-svg path,
.cart-svg circle,
.cart-svg rect {
  stroke: #7c3aed;
  stroke-width: 3;
  fill: none;
  stroke-linecap: round;
  stroke-linejoin: round;
}

.cart-svg .cart-fill {
  fill: #7c3aed;
  fill-opacity: 0;
  animation: cartFill 0.8s ease-out 1.5s forwards;
}

/* Cercle de fond */
.cart-circle {
  position: absolute;
  width: 180px;
  height: 180px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(124, 58, 237, 0.1), rgba(99, 102, 241, 0.1));
  opacity: 0;
  animation: circleGrow 1s ease-out 0.5s forwards;
  z-index: -1;
}

/* Nom de l'app */
.app-name {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
  font-size: 52px;
  font-weight: 900;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #6366f1 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-top: 10px;
  opacity: 0;
  transform: translateY(30px);
  animation: textReveal 1s cubic-bezier(0.68, -0.55, 0.265, 1.55) 1.8s forwards;
}

/* Tagline */
.tagline {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  font-size: 13px;
  font-weight: 500;
  color: #6b7280;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  margin-top: 12px;
  opacity: 0;
  animation: tagFade 0.8s ease-out 2.3s forwards;
}

/* Animations Keyframes */

/* Apparition du panier */
@keyframes cartAppear {
  0% {
    opacity: 0;
    transform: scale(0) rotate(-180deg);
  }
  60% {
    opacity: 1;
    transform: scale(1.1) rotate(10deg);
  }
  100% {
    opacity: 1;
    transform: scale(1) rotate(0deg);
  }
}

/* Remplissage du panier */
@keyframes cartFill {
  0% {
    fill-opacity: 0;
  }
  100% {
    fill-opacity: 0.15;
  }
}

/* Cercle de fond */
@keyframes circleGrow {
  0% {
    opacity: 0;
    transform: scale(0);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

/* Texte */
@keyframes textReveal {
  0% {
    opacity: 0;
    transform: translateY(30px);
    letter-spacing: -0.1em;
  }
  100% {
    opacity: 1;
    transform: translateY(0);
    letter-spacing: 0.05em;
  }
}

@keyframes tagFade {
  0% {
    opacity: 0;
    transform: translateY(15px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Outro */
@keyframes fadeOutZoom {
  0% {
    opacity: 1;
    transform: scale(1);
    filter: blur(0px);
  }
  100% {
    opacity: 0;
    transform: scale(1.2);
    filter: blur(25px);
  }
}

.outro {
  animation: fadeOutZoom 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

/* Responsive */
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
  .app-name {
    font-size: 38px;
  }
  .tagline {
    font-size: 11px;
  }
}
</style>
 
</style>
</head>
<body>

<!-- Container principal -->
<div id="splashContainer">
  
  <!-- Logo panier de marché -->
  <div class="cart-logo">
    <div class="cart-circle"></div>
    
    <svg class="cart-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
      <!-- Corps du panier -->
      <path class="cart-fill" d="M 25 35 L 30 70 C 30 75 32 80 35 80 L 65 80 C 68 80 70 75 70 70 L 75 35 Z"/>
      
      <!-- Contour du panier -->
      <path d="M 25 35 L 30 70 C 30 75 32 80 35 80 L 65 80 C 68 80 70 75 70 70 L 75 35" 
            stroke-width="3.5"/>
      
      <!-- Haut du panier (ouverture) -->
      <line x1="22" y1="35" x2="78" y2="35" stroke-width="4"/>
      
      <!-- Poignée gauche -->
      <path d="M 30 35 Q 35 20 45 25" stroke-width="3.5"/>
      
      <!-- Poignée droite -->
      <path d="M 70 35 Q 65 20 55 25" stroke-width="3.5"/>
      
      <!-- Articles dans le panier -->
      <!-- Article 1 (chemise pliée) -->
      <rect x="35" y="45" width="12" height="10" rx="1.5" class="cart-fill" stroke-width="2.5"/>
      
      <!-- Article 2 (pantalon plié) -->
      <rect x="52" y="48" width="10" height="12" rx="1.5" class="cart-fill" stroke-width="2.5"/>
      
      <!-- Article 3 (vêtement) -->
      <rect x="40" y="58" width="15" height="8" rx="1.5" class="cart-fill" stroke-width="2.5"/>
      
      <!-- Roue gauche -->
      <circle cx="35" cy="85" r="4" fill="#7c3aed"/>
      
      <!-- Roue droite -->
      <circle cx="65" cy="85" r="4" fill="#7c3aed"/>
    </svg>
  </div>
  
  <!-- Nom de l'application -->
  <h1 class="app-name">VintApp</h1>
  
  <!-- Tagline -->
  <p class="tagline">Vend.Achète.En confinace</p>

</div>

<script>
// Redirection automatique
const splashContainer = document.getElementById('splashContainer');

const redirectTimer = setTimeout(() => {
  splashContainer.classList.add('outro');
  setTimeout(() => {
    window.location.href = "<?php echo e(route('home')); ?>";
  }, 800);
}, 3500);

// Skip au clic/touch
const redirectToHome = () => {
  clearTimeout(redirectTimer);
  splashContainer.classList.add('outro');
  setTimeout(() => {
    window.location.href = "<?php echo e(route('home')); ?>";
  }, 800);
};

document.addEventListener('click', redirectToHome);
document.addEventListener('touchstart', redirectToHome, { passive: true });
</script>

</body>
</html>
<?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/splash.blade.php ENDPATH**/ ?>