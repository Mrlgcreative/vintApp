@extends('layouts.admin')

@section('title', 'Modifier l\'utilisateur')
@section('page-title', 'Modifier : ' . $user->name)

@section('page-actions')
<div class="flex flex-col sm:flex-row gap-2">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
        <i class="fas fa-arrow-left"></i>Retour à la liste
    </a>
    <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
        <i class="fas fa-eye"></i>Voir
    </a>
    <button type="button" onclick="confirmDelete()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-trash"></i>Supprimer
    </button>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" id="userForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
            <!-- Colonne principale (2/3) -->
            <div class="lg:col-span-2 space-y-4 lg:space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Informations personnelles</h2>
                    </div>
                    <div class="p-5 sm:p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nom complet <span class="text-red-600">*</span></label>
                                <input type="text"
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('name') border-red-500 focus:ring-red-500/40 @enderror"
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Adresse email <span class="text-red-600">*</span></label>
                                <input type="email"
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('email') border-red-500 focus:ring-red-500/40 @enderror"
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mt-4 sm:mt-6">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Téléphone</label>
                                <input type="tel"
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('phone') border-red-500 focus:ring-red-500/40 @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Date de naissance</label>
                                <input type="date"
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('date_of_birth') border-red-500 focus:ring-red-500/40 @enderror"
                                       id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}">
                                @error('date_of_birth')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 sm:mt-6">
                            <label for="bio" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Biographie</label>
                            <textarea
                                class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('bio') border-red-500 focus:ring-red-500/40 @enderror"
                                id="bio" name="bio" rows="3">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">Description ou biographie de l'utilisateur</p>
                        </div>
                    </div>
                </div>

                <!-- Sécurité et mot de passe -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Sécurité et accès</h2>
                    </div>
                    <div class="p-5 sm:p-6">
                        <div class="flex items-center gap-3 rounded-2xl border px-4 py-3 text-sm bg-sky-50 border-sky-200 text-sky-800 dark:bg-sky-900/20 dark:border-sky-800 dark:text-sky-300 mb-5">
                            <i class="fas fa-info-circle text-sky-500"></i>
                            <span>Laissez vide pour conserver le mot de passe actuel</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nouveau mot de passe</label>
                                <div class="relative">
                                    <input type="password"
                                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 pr-11 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('password') border-red-500 focus:ring-red-500/40 @enderror"
                                           id="password" name="password">
                                    <button type="button" onclick="togglePassword('password')"
                                            class="absolute right-1 top-1/2 -translate-y-1/2 flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors dark:hover:bg-slate-700">
                                        <i class="fas fa-eye" id="password-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">Minimum 8 caractères</p>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Confirmer le nouveau mot de passe</label>
                                <div class="relative">
                                    <input type="password"
                                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 pr-11 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors"
                                           id="password_confirmation" name="password_confirmation">
                                    <button type="button" onclick="togglePassword('password_confirmation')"
                                            class="absolute right-1 top-1/2 -translate-y-1/2 flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors dark:hover:bg-slate-700">
                                        <i class="fas fa-eye" id="password_confirmation-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mt-4 sm:mt-6">
                            <div>
                                <label for="role" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Rôle <span class="text-red-600">*</span></label>
                                <select
                                    class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('role') border-red-500 focus:ring-red-500/40 @enderror"
                                    id="role" name="role" required>
                                    <option value="">Sélectionner un rôle</option>
                                    <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>Utilisateur</option>
                                    <option value="seller" {{ old('role', $user->role) === 'seller' ? 'selected' : '' }}>Vendeur</option>
                                    <option value="moderator" {{ old('role', $user->role) === 'moderator' ? 'selected' : '' }}>Modérateur</option>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrateur</option>
                                </select>
                                @error('role')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Statut du compte</label>
                                <select
                                    class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('status') border-red-500 focus:ring-red-500/40 @enderror"
                                    id="status" name="status">
                                    <option value="active" {{ old('status', $user->status ?? 'active') === 'active' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactif</option>
                                    <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspendu</option>
                                    <option value="banned" {{ old('status', $user->status) === 'banned' ? 'selected' : '' }}>Banni</option>
                                </select>
                                @error('status')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Adresse -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Adresse</h2>
                    </div>
                    <div class="p-5 sm:p-6">
                        <div>
                            <label for="address" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Adresse</label>
                            <input type="text"
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('address') border-red-500 focus:ring-red-500/40 @enderror"
                                   id="address" name="address" value="{{ old('address', $user->address) }}">
                            @error('address')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mt-4 sm:mt-6">
                            <div>
                                <label for="city" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Ville</label>
                                <input type="text"
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('city') border-red-500 focus:ring-red-500/40 @enderror"
                                       id="city" name="city" value="{{ old('city', $user->city) }}">
                                @error('city')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Code postal</label>
                                <input type="text"
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('postal_code') border-red-500 focus:ring-red-500/40 @enderror"
                                       id="postal_code" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
                                @error('postal_code')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="country" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Pays</label>
                                <select
                                    class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('country') border-red-500 focus:ring-red-500/40 @enderror"
                                    id="country" name="country">
                                    <option value="">Sélectionner un pays</option>
                                    <option value="FR" {{ old('country', $user->country) === 'FR' ? 'selected' : '' }}>France</option>
                                    <option value="BE" {{ old('country', $user->country) === 'BE' ? 'selected' : '' }}>Belgique</option>
                                    <option value="CH" {{ old('country', $user->country) === 'CH' ? 'selected' : '' }}>Suisse</option>
                                    <option value="CA" {{ old('country', $user->country) === 'CA' ? 'selected' : '' }}>Canada</option>
                                    <option value="US" {{ old('country', $user->country) === 'US' ? 'selected' : '' }}>États-Unis</option>
                                </select>
                                @error('country')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar avec avatar et options -->
            <div class="space-y-4 lg:space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Photo de profil</h2>
                    </div>
                    <div class="p-5 sm:p-6 text-center">
                        <div class="mb-3">
                            <div class="avatar-preview" id="avatarPreview">
                                @if($user->avatar)
                                    <img src="{{ $user->avatar_url }}" alt="Avatar actuel">
                                @else
                                    <div class="avatar-placeholder">
                                        <i class="fas fa-user fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($user->avatar)
                            <label class="flex items-start justify-center gap-2 mb-3 cursor-pointer">
                                <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 mt-0.5" id="remove_avatar" name="remove_avatar" value="1">
                                <span class="text-sm text-red-600">Supprimer la photo actuelle</span>
                            </label>
                        @endif

                        <input type="file"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-500 dark:text-slate-300 file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-primary-600 hover:file:bg-primary-100 transition-colors @error('avatar') border-red-500 focus:ring-red-500/40 @enderror"
                               id="avatar" name="avatar" accept="image/*">
                        @error('avatar')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">JPG, PNG (max 2MB)</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Options du compte</h2>
                    </div>
                    <div class="p-5 sm:p-6 space-y-3">
                        <label class="flex items-start gap-3">
                            <input type="checkbox" value="1"
                                   class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 mt-0.5"
                                   id="email_verified" name="email_verified" {{ old('email_verified', $user->email_verified_at ? 1 : 0) ? 'checked' : '' }}>
                            <span class="text-sm text-slate-700 dark:text-slate-300">Email vérifié</span>
                        </label>

                        <label class="flex items-start gap-3">
                            <input type="checkbox" value="1"
                                   class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 mt-0.5"
                                   id="notifications_enabled" name="notifications_enabled" {{ old('notifications_enabled', $user->notifications_enabled ?? 1) ? 'checked' : '' }}>
                            <span class="text-sm text-slate-700 dark:text-slate-300">Notifications activées</span>
                        </label>

                        <label class="flex items-start gap-3">
                            <input type="checkbox" value="1"
                                   class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 mt-0.5"
                                   id="marketing_emails" name="marketing_emails" {{ old('marketing_emails', $user->marketing_emails) ? 'checked' : '' }}>
                            <span class="text-sm text-slate-700 dark:text-slate-300">Emails marketing</span>
                        </label>

                        <label class="flex items-start gap-3">
                            <input type="checkbox" value="1"
                                   class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 mt-0.5"
                                   id="is_seller" name="is_seller" {{ old('is_seller', $user->is_seller) ? 'checked' : '' }}>
                            <span class="text-sm text-slate-700 dark:text-slate-300">Peut vendre des articles</span>
                        </label>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Préférences</h2>
                    </div>
                    <div class="p-5 sm:p-6 space-y-4">
                        <div>
                            <label for="language" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Langue</label>
                            <select
                                class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('language') border-red-500 focus:ring-red-500/40 @enderror"
                                id="language" name="language">
                                <option value="fr" {{ old('language', $user->language ?? 'fr') === 'fr' ? 'selected' : '' }}>Français</option>
                                <option value="en" {{ old('language', $user->language) === 'en' ? 'selected' : '' }}>English</option>
                                <option value="es" {{ old('language', $user->language) === 'es' ? 'selected' : '' }}>Español</option>
                            </select>
                            @error('language')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="timezone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Fuseau horaire</label>
                            <select
                                class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('timezone') border-red-500 focus:ring-red-500/40 @enderror"
                                id="timezone" name="timezone">
                                <option value="Europe/Paris" {{ old('timezone', $user->timezone ?? 'Europe/Paris') === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                                <option value="Europe/London" {{ old('timezone', $user->timezone) === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                                <option value="America/New_York" {{ old('timezone', $user->timezone) === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                            </select>
                            @error('timezone')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Statistiques</h2>
                    </div>
                    <div class="p-5 sm:p-6">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="text-center p-4 bg-sky-50 dark:bg-sky-900/20 rounded-xl">
                                <i class="fas fa-box text-2xl text-sky-600 mb-2"></i>
                                <h4 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $user->items_count ?? 0 }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Articles</p>
                            </div>
                            <div class="text-center p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl">
                                <i class="fas fa-shopping-cart text-2xl text-emerald-600 mb-2"></i>
                                <h4 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $user->orders_count ?? 0 }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Commandes</p>
                            </div>
                            <div class="text-center p-4 bg-sky-50 dark:bg-sky-900/20 rounded-xl">
                                <i class="fas fa-star text-2xl text-sky-600 mb-2"></i>
                                <h4 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $user->reviews_count ?? 0 }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Avis</p>
                            </div>
                            <div class="text-center p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                                <i class="fas fa-thumbs-up text-2xl text-amber-600 mb-2"></i>
                                <h4 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $user->rating ?? 0 }}/5</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Note</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mt-6">
            <button type="button" onclick="history.back()" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <i class="fas fa-times"></i>Annuler
            </button>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" name="action" value="save" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                    <i class="fas fa-save"></i>Mettre à jour
                </button>
                <button type="submit" name="action" value="save_and_continue" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                    <i class="fas fa-save"></i>Mettre à jour et continuer
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" tabindex="-1" class="modal-wrapper fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" data-bs-dismiss="modal"></div>
    <div class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-slate-800 shadow-xl ring-1 ring-slate-200 dark:ring-slate-700 animate-pop">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700">
            <h3 class="font-semibold text-slate-900 dark:text-white">Confirmer la suppression</h3>
            <button type="button" class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors dark:hover:bg-slate-700" data-bs-dismiss="modal">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="p-5 sm:p-6">
            <p class="text-sm text-slate-600 dark:text-slate-300">Êtes-vous sûr de vouloir supprimer l'utilisateur <strong class="text-slate-900 dark:text-white">{{ $user->name }}</strong> ?</p>
            @if($user->items_count > 0)
                <div class="flex items-center gap-3 rounded-2xl border px-4 py-3 text-sm mt-4 bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-300">
                    <i class="fas fa-triangle-exclamation text-amber-500"></i>
                    <span>Cet utilisateur possède {{ $user->items_count }} article(s).</span>
                </div>
            @endif
            @if($user->orders_count > 0)
                <div class="flex items-center gap-3 rounded-2xl border px-4 py-3 text-sm mt-3 bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-300">
                    <i class="fas fa-triangle-exclamation text-amber-500"></i>
                    <span>Cet utilisateur a {{ $user->orders_count }} commande(s) associée(s).</span>
                </div>
            @endif
            <p class="text-sm text-red-600 mt-4">Cette action est irréversible.</p>
        </div>
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 px-5 py-4 border-t border-slate-100 dark:border-slate-700">
            <button type="button" data-bs-dismiss="modal" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">Annuler</button>
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors w-full sm:w-auto">Supprimer définitivement</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-preview {
        width: 120px;
        height: 120px;
        margin: 0 auto 1.25rem;
        border-radius: 9999px;
        overflow: hidden;
        border: 3px solid #e2e8f0;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .08);
    }

    .dark .avatar-preview {
        border-color: #334155;
        background: #1e293b;
    }

    .avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        color: #94a3b8;
    }

    .is-invalid {
        border-color: #ef4444 !important;
    }

    .invalid-feedback {
        display: none;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc2626;
    }

    .invalid-feedback[style*="block"] {
        display: block;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aperçu de l'avatar
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');

    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.innerHTML = `<img src="${e.target.result}" alt="Avatar preview">`;
            };
            reader.readAsDataURL(file);
        }
    });

    // Vérification en temps réel de la correspondance des mots de passe
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');

    function checkPasswordMatch() {
        if (passwordConfirmInput.value && passwordInput.value !== passwordConfirmInput.value) {
            passwordConfirmInput.classList.add('is-invalid');
            showError(passwordConfirmInput, 'Les mots de passe ne correspondent pas');
        } else {
            passwordConfirmInput.classList.remove('is-invalid');
            removeError(passwordConfirmInput);
        }
    }

    passwordInput.addEventListener('input', checkPasswordMatch);
    passwordConfirmInput.addEventListener('input', checkPasswordMatch);

    // Validation du formulaire
    const form = document.getElementById('userForm');
    form.addEventListener('submit', function(e) {
        let valid = true;

        // Validation du mot de passe si renseigné
        const password = passwordInput.value;
        const passwordConfirmation = passwordConfirmInput.value;

        if (password && password !== passwordConfirmation) {
            showError(passwordConfirmInput, 'Les mots de passe ne correspondent pas');
            valid = false;
        }

        if (password && password.length < 8) {
            showError(passwordInput, 'Le mot de passe doit contenir au moins 8 caractères');
            valid = false;
        }

        // Validation de l'email
        const email = document.getElementById('email').value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showError(document.getElementById('email'), 'Adresse email invalide');
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });

    function showError(input, message) {
        input.classList.add('is-invalid');
        let feedback = input.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            // Gérer le cas des input-group
            const parent = input.closest('.input-group') || input.parentNode;
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            parent.appendChild(feedback);
        }
        feedback.textContent = message;
        feedback.style.display = 'block';
    }

    function removeError(input) {
        const feedback = input.parentNode.querySelector('.invalid-feedback') ||
                        input.closest('.input-group')?.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.style.display = 'none';
        }
    }
});

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eye = document.getElementById(fieldId + '-eye');

    if (field.type === 'password') {
        field.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}

function confirmDelete() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
</script>
@endpush
