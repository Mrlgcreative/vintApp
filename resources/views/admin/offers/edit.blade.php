@extends('layouts.admin')

@section('title', 'Modifier l\'offre')
@section('page-title', 'Modifier l\'offre')
@section('page-subtitle', $offer->title)

@section('content')
<div class="max-w-4xl">
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <form action="{{ route('admin.offers.update', $offer) }}" method="POST">
            @csrf @method('PUT')
            <div class="border-b border-slate-100 px-6 py-4 dark:border-slate-700">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Informations de l'offre</h3>
            </div>
            <div class="p-6 space-y-6">
                @include('admin.offers._fields', ['offer' => $offer])
            </div>
            <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4 dark:border-slate-700 dark:bg-slate-900/50 sm:flex-row">
                <a href="{{ route('admin.offers.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <i class="fas fa-times"></i>Annuler
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-900 hover:bg-gray-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                    <i class="fas fa-save"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection