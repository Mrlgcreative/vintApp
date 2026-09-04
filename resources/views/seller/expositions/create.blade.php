@extends('app')

@section('title', 'Nouvelle exposition')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex">
        @include('seller.partials.sidebar')

        <main class="flex-1 p-6 lg:p-8 pb-20 lg:pb-8">
            <div class="max-w-3xl mx-auto">
                <div class="mb-8">
                    <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-emerald-800 rounded-2xl shadow-xl p-6 sm:p-8 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
                        <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>
                        <div class="relative">
                            <h1 class="text-2xl sm:text-3xl font-bold flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/15 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                    <i class="fas fa-plus text-base"></i>
                                </div>
                                Nouvelle exposition
                            </h1>
                            <p class="text-white/80 mt-2 text-sm sm:text-base">Exposez une sélection de vos articles dans l'annuaire des boutiques</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <form action="{{ route('seller.expositions.store') }}" method="POST">
                        @csrf
                        <div class="border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Informations de l'exposition</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            @include('seller.expositions._fields', ['exposition' => null])
                        </div>
                        <div class="flex flex-col-reverse justify-end gap-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-6 py-4 sm:flex-row">
                            <a href="{{ route('seller.expositions.index') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-times"></i>Annuler
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 hover:bg-gray-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                                <i class="fas fa-save"></i>Créer l'exposition
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection