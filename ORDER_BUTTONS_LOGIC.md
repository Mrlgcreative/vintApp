# Logique d'Affichage des Boutons - Page Commande

## 📋 Vue d'ensemble

Ce document explique la logique d'affichage des boutons et messages dans `show.blade.php` selon le statut de la commande et le rôle de l'utilisateur (acheteur ou vendeur).

---

## 🔄 Cycle de vie d'une commande

```
1. PENDING (En attente)
   ↓ [Acheteur clique "Confirmer le paiement"]
   
2. CONFIRMED (Confirmée)
   ↓ [Vendeur clique "Modifier le statut" → Marquer comme expédiée]
   
3. SHIPPED (Expédiée)
   ↓ [Acheteur clique "✅ Commande Reçue"]
   
4. DELIVERED (Livrée) - Optionnel
   ↓ [Acheteur clique "✅ Commande Reçue"]
   
5. COMPLETED (Terminée)
   → Distribution automatique des fonds
```

---

## 🎯 Logique d'affichage selon le statut

### **1. Status = PENDING (En attente de paiement)**

#### **Vue ACHETEUR :**
| Zone | Élément affiché |
|------|----------------|
| **Actions principales** | ✅ Bouton "Confirmer le paiement" (vert)<br>✅ Bouton "Annuler" (rouge) |
| **Actions rapides** | ⚠️ Alerte jaune : "En attente de paiement - Veuillez confirmer le paiement pour continuer" |
| **Condition** | `$order->buyer_id === Auth::id() && $order->status === 'pending'` |

#### **Vue VENDEUR :**
| Zone | Élément affiché |
|------|----------------|
| **Actions principales** | ❌ Aucun bouton |
| **Actions rapides** | ℹ️ Alerte jaune : "En attente de paiement - L'acheteur n'a pas encore payé" |
| **Condition** | `$order->item->user_id === Auth::id() && $order->status === 'pending'` |

---

### **2. Status = CONFIRMED (Paiement confirmé)**

#### **Vue ACHETEUR :**
| Zone | Élément affiché |
|------|----------------|
| **Actions principales** | ❌ Aucun bouton |
| **Actions rapides** | ℹ️ Alerte bleue : "Paiement confirmé - En attente d'expédition par le vendeur" |
| **Condition** | `$order->buyer_id === Auth::id() && $order->status === 'confirmed'` |

#### **Vue VENDEUR :**
| Zone | Élément affiché |
|------|----------------|
| **Actions principales** | ✅ Bouton "Modifier le statut" (orange) |
| **Actions rapides** | ℹ️ Alerte bleue : "Paiement confirmé - Vous pouvez maintenant expédier la commande" |
| **Condition** | `$order->item->user_id === Auth::id() && $order->status === 'confirmed'` |

---

### **3. Status = SHIPPED (Expédiée)**

#### **Vue ACHETEUR :**
| Zone | Élément affiché |
|------|----------------|
| **Actions principales** | ❌ Aucun bouton |
| **Actions rapides** | ✅ **Bouton "✅ Commande Reçue" (vert, large)**<br>ℹ️ Alerte bleue : "Cliquez sur 'Commande Reçue' une fois la livraison effectuée" |
| **Condition** | `$order->buyer_id === Auth::id() && $order->status === 'shipped' && !$order->confirmed_by_buyer_at` |

#### **Vue VENDEUR :**
| Zone | Élément affiché |
|------|----------------|
| **Actions principales** | ✅ Bouton "Modifier le statut" (orange) |
| **Actions rapides** | ℹ️ Alerte bleue : "Commande expédiée - En attente de confirmation de réception par l'acheteur" |
| **Condition** | `$order->item->user_id === Auth::id() && $order->status === 'shipped'` |

---

### **4. Status = DELIVERED (Livrée)**

#### **Vue ACHETEUR :**
| Zone | Élément affiché |
|------|----------------|
| **Actions principales** | ❌ Aucun bouton |
| **Actions rapides** | ✅ **Bouton "✅ Commande Reçue" (vert, large)**<br>ℹ️ Alerte bleue : "Confirmez la réception pour finaliser la transaction" |
| **Condition** | `$order->buyer_id === Auth::id() && $order->status === 'delivered' && !$order->confirmed_by_buyer_at` |

#### **Vue VENDEUR :**
| Zone | Élément affiché |
|------|----------------|
| **Actions principales** | ❌ Aucun bouton |
| **Actions rapides** | ✅ Alerte verte : "Commande livrée - En attente de confirmation par l'acheteur" |
| **Condition** | `$order->item->user_id === Auth::id() && $order->status === 'delivered'` |

---

### **5. Status = COMPLETED (Confirmée par l'acheteur)**

#### **Vue ACHETEUR & VENDEUR :**
| Zone | Élément affiché |
|------|----------------|
| **Actions principales** | ❌ Aucun bouton |
| **Actions rapides** | ✅ Alerte verte : "✅ Réception confirmée - Le [date]<br>[Note optionnelle de l'acheteur]<br>💰 La distribution des fonds a été effectuée" |
| **Condition** | `$order->confirmed_by_buyer_at !== null` |

---

## 💰 Distribution automatique des fonds

### **Quand ?**
La distribution se fait automatiquement quand l'acheteur clique sur **"✅ Commande Reçue"** et que la méthode `OrderController::confirmDelivery()` est appelée.

### **Comment ?**
```php
// Dans OrderController::confirmDelivery()
1. Vérifier que l'acheteur est connecté
2. Vérifier que status = 'shipped' OU 'delivered'
3. Vérifier que confirmed_by_buyer_at === null
4. Enregistrer la confirmation (date + note)
5. Changer status → 'completed'
6. Récupérer les pourcentages (commission 10%, transport 5%)
7. Calculer les montants :
   - Commission = total * 10%
   - Transport = total * 5%
   - Vendeur = total - commission - transport
8. Débiter le wallet PENDING du vendeur
9. Créditer le wallet MAIN du vendeur (montant net)
10. Créditer le wallet ENTERPRISE (commission + transport)
11. Créer 3 transactions pour traçabilité
12. Commit DB transaction
```

### **Résultat :**
| Wallet | Avant | Après |
|--------|-------|-------|
| **Vendeur - Pending** | 170.00 USD | 0.00 USD |
| **Vendeur - Main** | 0.00 USD | 144.50 USD (85%) |
| **Plateforme - Enterprise** | 0.00 USD | 25.50 USD (15%) |

---

## 🔐 Sécurité et validations

### **Bouton "Confirmer le paiement" :**
```php
// Conditions d'affichage
$order->buyer_id === Auth::id() 
&& $order->status === 'pending'

// Validations dans OrderController::confirmPayment()
- Vérifier que c'est l'acheteur
- Vérifier que status === 'pending'
- Changer status → 'confirmed'
- Enregistrer paid_at → now()
```

### **Bouton "✅ Commande Reçue" :**
```php
// Conditions d'affichage
$order->buyer_id === Auth::id() 
&& in_array($order->status, ['shipped', 'delivered'])
&& !$order->confirmed_by_buyer_at

// Validations dans OrderController::confirmDelivery()
- Vérifier que c'est l'acheteur
- Vérifier que status IN ['shipped', 'delivered']
- Vérifier que confirmed_by_buyer_at === null
- Effectuer la distribution
```

### **Bouton "Modifier le statut" (Vendeur) :**
```php
// Conditions d'affichage
$order->item->user_id === Auth::id() 
&& in_array($order->status, ['confirmed', 'shipped'])

// Permet au vendeur de :
- Marquer comme expédiée (confirmed → shipped)
- Marquer comme livrée (shipped → delivered)
```

---

## 📝 Messages contextuels

### **Codes couleur :**
- 🟡 **Jaune (warning)** : En attente de paiement
- 🔵 **Bleu (info)** : Paiement confirmé, en attente d'expédition
- 🔵 **Bleu (primary)** : Expédié/Livré, en attente de confirmation
- 🟢 **Vert (success)** : Réception confirmée, distribution effectuée

### **Personnalisation selon le rôle :**
Chaque message s'adapte selon que l'utilisateur est acheteur ou vendeur :
- **Acheteur** : "Veuillez confirmer le paiement..."
- **Vendeur** : "L'acheteur n'a pas encore payé..."

---

## 🧪 Tests à effectuer

### **Scénario 1 : Acheteur**
1. ✅ Créer une commande → Voir bouton "Confirmer le paiement"
2. ✅ Cliquer "Confirmer le paiement" → Status change à 'confirmed'
3. ✅ Attendre que vendeur expédie → Voir bouton "✅ Commande Reçue"
4. ✅ Cliquer "✅ Commande Reçue" → Distribution effectuée
5. ✅ Voir alerte "Réception confirmée" avec date

### **Scénario 2 : Vendeur**
1. ✅ Recevoir notification de paiement → Voir bouton "Modifier le statut"
2. ✅ Marquer comme expédiée → Status change à 'shipped'
3. ✅ Attendre confirmation acheteur → Voir message "En attente de confirmation"
4. ✅ Après confirmation → Voir alerte "Réception confirmée"
5. ✅ Vérifier wallet MAIN → Balance augmentée de 85%

### **Scénario 3 : Admin**
1. ✅ Vérifier dashboard → Voir wallets pending diminuer
2. ✅ Vérifier transactions → 3 transactions créées
3. ✅ Vérifier wallet enterprise → Balance augmentée de 15%

---

## 🎨 Amélioration : Badges de statut

Dans l'en-tête de la carte commande, le badge de statut utilise ces couleurs :
```php
'pending'   => 'bg-warning'   // Jaune
'confirmed' => 'bg-info'      // Bleu clair
'shipped'   => 'bg-primary'   // Bleu
'delivered' => 'bg-success'   // Vert
'cancelled' => 'bg-danger'    // Rouge
```

---

## 📊 Résumé visuel

```
┌─────────────────────────────────────────────────────────┐
│  ACHETEUR                    VENDEUR                    │
├─────────────────────────────────────────────────────────┤
│  [Créer commande]                                       │
│         ↓                                               │
│  📋 PENDING                                             │
│  ⚠️ "En attente de paiement"                           │
│  🔘 Confirmer le paiement     ℹ️ "Acheteur n'a pas payé" │
│         ↓                                               │
│  💳 CONFIRMED                                           │
│  ℹ️ "En attente d'expédition" 🔘 Modifier le statut     │
│                                      ↓                  │
│                              [Marquer comme expédiée]   │
│         ↓                                               │
│  📦 SHIPPED                                             │
│  🔘 ✅ Commande Reçue          ℹ️ "En attente confirmation"│
│         ↓                                               │
│  [Acheteur confirme]                                    │
│         ↓                                               │
│  ✅ COMPLETED                                           │
│  💰 Distribution effectuée   💰 Distribution effectuée  │
│  ✅ Vendeur: 144.50 USD      ✅ Vendeur: 144.50 USD    │
│  ✅ Plateforme: 25.50 USD    ✅ Plateforme: 25.50 USD  │
└─────────────────────────────────────────────────────────┘
```

---

## ✅ Modifications effectuées

### **Fichier : `show.blade.php`**

#### **1. Section "Actions principales" (ligne ~180-220)**
- ✅ Bouton "Confirmer le paiement" : Affiché UNIQUEMENT si `status === 'pending'`
- ✅ Bouton "Modifier le statut" : Affiché UNIQUEMENT si vendeur ET `status IN ['confirmed', 'shipped']`
- ✅ Bouton "Annuler" : Affiché UNIQUEMENT si acheteur ET `status === 'pending'`

#### **2. Section "Actions rapides" (ligne ~230-290)**
- ✅ Messages contextuels selon le statut
- ✅ Bouton "✅ Commande Reçue" déplacé ici (plus visible)
- ✅ Taille du bouton augmentée (`btn-lg`)
- ✅ Messages adaptés selon acheteur/vendeur
- ✅ Alerte de confirmation avec mention de la distribution

---

## 🚀 Prochaines étapes

1. ✅ Tester l'affichage pour tous les statuts
2. ⏳ Convertir le style Bootstrap → Tailwind (cohérence avec les autres pages)
3. ⏳ Ajouter des notifications par email (paiement reçu, commande expédiée, etc.)
4. ⏳ Ajouter un historique détaillé des changements de statut

---

**Date de création** : 12 octobre 2025  
**Dernière modification** : 12 octobre 2025  
**Auteur** : GitHub Copilot
