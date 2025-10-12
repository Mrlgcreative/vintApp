<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Ici, vous pouvez enregistrer tous les canaux de diffusion d'événements
| que votre application prend en charge. L'autorisation du canal donnée
| est utilisée pour déterminer si un utilisateur peut écouter le canal.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal privé pour les notifications de l'utilisateur
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
