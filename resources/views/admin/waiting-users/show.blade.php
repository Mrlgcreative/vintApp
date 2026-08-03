@extends('layouts.admin')

@section('title', 'Détails de la pré-inscription')
@section('page-title', $waitingUser->name)

@section('page-actions')
<a href="{{ route('admin.waiting-users.index') }}"
   class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
    <i class="fas fa-arrow-left"></i>Retour à la liste
</a>
@endsection

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20 px-4 py-3">
            <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400"></i>
            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20 px-4 py-3">
            <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Colonne principale -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Informations utilisateur -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-user mr-2 text-primary-600"></i>Informations de l'utilisateur
                    </h3>
                </div>
                <div class="p-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Nom complet</dt>
                            <dd class="mt-1 text-base font-semibold text-slate-900 dark:text-white">{{ $waitingUser->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Email</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-white">
                                {{ $waitingUser->email }}
                                @if($waitingUser->email_confirmed_at)
                                    <i class="fas fa-check-circle text-emerald-500 ml-1" title="Vérifié"></i>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Téléphone</dt>
                            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $waitingUser->phone ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Pays</dt>
                            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $waitingUser->country }}</dd>
                        </div>
                    </div>

                    @if($waitingUser->message)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Message</dt>
                            <dd class="mt-2 rounded-xl border border-primary-100 bg-primary-50/60 dark:border-primary-800 dark:bg-primary-900/10 px-4 py-3 text-sm text-slate-700 dark:text-slate-200">
                                {{ $waitingUser->message }}
                            </dd>
                        </div>
                    @endif

                    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Adresse IP</dt>
                            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $waitingUser->ip_address ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Navigateur</dt>
                            <dd class="mt-1 text-xs text-slate-700 dark:text-slate-200 break-all">{{ $waitingUser->user_agent ?? '-' }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes admin -->
            @if($waitingUser->admin_notes)
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                        <h5 class="text-base font-semibold text-slate-900 dark:text-white">
                            <i class="fas fa-sticky-note mr-2 text-amber-500"></i>Notes administrateur
                        </h5>
                    </div>
                    <div class="p-5 sm:p-6">
                        <p class="text-sm text-slate-700 dark:text-slate-200">{{ $waitingUser->admin_notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Compte converti -->
            @if($waitingUser->converted_user_id)
                <div class="rounded-2xl border border-emerald-200 bg-white shadow-sm dark:border-emerald-800 dark:bg-slate-800 overflow-hidden">
                    <div class="flex items-center justify-between border-b border-emerald-100 dark:border-emerald-800/50 px-5 py-4">
                        <h5 class="text-base font-semibold text-emerald-700 dark:text-emerald-300">
                            <i class="fas fa-user-check mr-2"></i>Compte utilisateur créé
                        </h5>
                    </div>
                    <div class="p-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">User ID</dt>
                                <dd class="mt-1 text-base font-semibold text-slate-900 dark:text-white">#{{ $waitingUser->convertedUser->id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Date de conversion</dt>
                                <dd class="mt-1 text-base font-semibold text-slate-900 dark:text-white">{{ $waitingUser->converted_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </div>
                        <a href="#" class="mt-4 inline-flex items-center gap-2 rounded-xl border border-emerald-300 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-2 text-sm font-medium text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors">
                            <i class="fas fa-external-link-alt"></i>Voir le profil utilisateur
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Panneau latéral -->
        <div class="space-y-6">
            <!-- Statut -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                    <h5 class="text-base font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-info-circle mr-2 text-primary-600"></i>Statut
                    </h5>
                </div>
                <div class="p-5 text-center">
                    <div class="mb-3">{!! $waitingUser->status_badge !!}</div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        <i class="fas fa-clock mr-1"></i>
                        En attente depuis {{ $waitingUser->waiting_days }} jour(s)
                    </p>
                </div>
            </div>

            <!-- Timeline -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                    <h5 class="text-base font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-history mr-2 text-primary-600"></i>Historique
                    </h5>
                </div>
                <div class="p-5">
                    <ol class="relative space-y-5 border-l-2 border-slate-100 dark:border-slate-700 ml-2.5">
                        <li class="ml-6">
                            <span class="absolute -left-[11px] flex h-5 w-5 items-center justify-center rounded-full bg-white dark:bg-slate-800 ring-2 ring-primary-500">
                                <i class="fas fa-plus-circle text-primary-500"></i>
                            </span>
                            <div>
                                <p class="font-medium text-slate-900 dark:text-white">Inscription</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $waitingUser->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </li>

                        @if($waitingUser->email_confirmed_at)
                            <li class="ml-6">
                                <span class="absolute -left-[11px] flex h-5 w-5 items-center justify-center rounded-full bg-white dark:bg-slate-800 ring-2 ring-emerald-500">
                                    <i class="fas fa-check-circle text-emerald-500"></i>
                                </span>
                                <div>
                                    <p class="font-medium text-slate-900 dark:text-white">Email confirmé</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $waitingUser->email_confirmed_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </li>
                        @endif

                        @if($waitingUser->approved_at)
                            <li class="ml-6">
                                <span class="absolute -left-[11px] flex h-5 w-5 items-center justify-center rounded-full bg-white dark:bg-slate-800 ring-2 ring-emerald-500">
                                    <i class="fas fa-thumbs-up text-emerald-500"></i>
                                </span>
                                <div>
                                    <p class="font-medium text-slate-900 dark:text-white">Approuvé</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $waitingUser->approved_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </li>
                        @endif

                        @if($waitingUser->rejected_at)
                            <li class="ml-6">
                                <span class="absolute -left-[11px] flex h-5 w-5 items-center justify-center rounded-full bg-white dark:bg-slate-800 ring-2 ring-red-500">
                                    <i class="fas fa-times-circle text-red-500"></i>
                                </span>
                                <div>
                                    <p class="font-medium text-slate-900 dark:text-white">Rejeté</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $waitingUser->rejected_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </li>
                        @endif

                        @if($waitingUser->converted_at)
                            <li class="ml-6">
                                <span class="absolute -left-[11px] flex h-5 w-5 items-center justify-center rounded-full bg-white dark:bg-slate-800 ring-2 ring-violet-500">
                                    <i class="fas fa-user-check text-violet-500"></i>
                                </span>
                                <div>
                                    <p class="font-medium text-slate-900 dark:text-white">Compte créé</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $waitingUser->converted_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </li>
                        @endif
                    </ol>
                </div>
            </div>

            <!-- Actions -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                    <h5 class="text-base font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-tools mr-2 text-primary-600"></i>Actions
                    </h5>
                </div>
                <div class="p-5 space-y-3">
                    @if($waitingUser->status === 'pending')
                        <form action="{{ route('admin.waiting-users.resend-confirmation', $waitingUser) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                                <i class="fas fa-envelope"></i>Renvoyer confirmation
                            </button>
                        </form>
                    @endif

                    @if($waitingUser->status === 'confirmed' || $waitingUser->status === 'pending')
                        <form action="{{ route('admin.waiting-users.approve', $waitingUser) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                                <i class="fas fa-check"></i>Approuver
                            </button>
                        </form>

                        <button type="button" data-bs-toggle="modal" data-bs-target="#rejectModal"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                            <i class="fas fa-times"></i>Rejeter
                        </button>
                    @endif

                    <form action="{{ route('admin.waiting-users.destroy', $waitingUser) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cette pré-inscription ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                            <i class="fas fa-trash"></i>Supprimer
                        </button>
                    </form>

                    <a href="{{ route('admin.waiting-users.index') }}"
                       class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <i class="fas fa-arrow-left"></i>Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de rejet -->
<div id="rejectModal" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-800 shadow-2xl ring-1 ring-slate-200 dark:ring-slate-700 animate-pop">
        <div class="flex items-center justify-between bg-amber-50 dark:bg-amber-900/20 px-5 py-4 border-b border-amber-100 dark:border-amber-800">
            <h5 class="text-base font-semibold text-amber-800 dark:text-amber-200 flex items-center gap-2">
                <i class="fas fa-times-circle"></i>Rejeter la pré-inscription
            </h5>
            <button type="button" data-bs-dismiss="modal" class="text-amber-400 hover:text-amber-600 transition-colors">
                <i class="fas fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="{{ route('admin.waiting-users.reject', $waitingUser) }}" method="POST">
            @csrf
            <div class="p-5 sm:p-6">
                <label for="reason" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Raison du rejet</label>
                <textarea id="reason" name="reason" rows="4" required
                          placeholder="Expliquez pourquoi cette demande est rejetée..."
                          class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors"></textarea>
                <div class="mt-4 flex items-start gap-2 rounded-xl border-l-4 border-amber-400 bg-amber-50 dark:bg-amber-900/20 p-4">
                    <i class="fas fa-exclamation-triangle text-amber-400 mt-0.5"></i>
                    <p class="text-sm text-amber-700 dark:text-amber-200">Cette action ne peut pas être annulée facilement.</p>
                </div>
            </div>
            <div class="bg-slate-50 dark:bg-slate-900 rounded-b-2xl px-5 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button type="button" data-bs-dismiss="modal"
                        class="inline-flex justify-center items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors w-full sm:w-auto">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex justify-center items-center gap-2 w-full sm:w-auto rounded-xl bg-red-600 hover:bg-red-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                    <i class="fas fa-times-circle"></i>Confirmer le rejet
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
