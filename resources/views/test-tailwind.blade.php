@extends('app')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-800 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Test Tailwind -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-3xl font-bold text-blue-600 mb-4">Test Tailwind CSS</h1>
            <p class="text-gray-600 dark:text-gray-300 mb-4">Si vous voyez ce texte stylé avec des couleurs et des espacements corrects, Tailwind fonctionne !</p>
            <div class="flex space-x-4">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                    Bouton Bleu
                </button>
                <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                    Bouton Vert
                </button>
                <button class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                    Bouton Violet
                </button>
            </div>
        </div>

        <!-- Grid Test -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-blue-400 to-primary-500 text-white p-6 rounded-xl shadow-lg">
                <h3 class="text-xl font-semibold mb-2">Card 1</h3>
                <p>Test du gradient et des effets</p>
            </div>
            <div class="bg-gradient-to-br from-green-400 to-blue-500 text-white p-6 rounded-xl shadow-lg">
                <h3 class="text-xl font-semibold mb-2">Card 2</h3>
                <p>Test du responsive design</p>
            </div>
            <div class="bg-gradient-to-br from-primary-400 to-accent-500 text-white p-6 rounded-xl shadow-lg">
                <h3 class="text-xl font-semibold mb-2">Card 3</h3>
                <p>Test des transitions</p>
            </div>
        </div>
    </div>
</div>
@endsection