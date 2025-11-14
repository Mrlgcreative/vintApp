import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Variables CSS dynamiques VintApp
                'vintapp': {
                    'primary': 'var(--color-primary)',
                    'secondary': 'var(--color-secondary)',
                    'success': 'var(--color-success)',
                    'danger': 'var(--color-danger)',
                    'warning': 'var(--color-warning)',
                    'info': 'var(--color-info)',
                    'light': 'var(--color-light)',
                    'dark': 'var(--color-dark)',
                    'accent': 'var(--color-accent)',
                },
                // Alias pour compatibilité
                primary: {
                    DEFAULT: 'var(--color-primary)',
                    50: 'var(--color-primary-50)',
                    100: 'var(--color-primary-100)',
                    200: 'var(--color-primary-200)',
                    300: 'var(--color-primary-300)',
                    400: 'var(--color-primary-400)',
                    500: 'var(--color-primary)',
                    600: 'var(--color-primary-600)',
                    700: 'var(--color-primary-700)',
                    800: 'var(--color-primary-800)',
                    900: 'var(--color-primary-900)',
                },
                secondary: {
                    DEFAULT: 'var(--color-secondary)',
                    50: 'var(--color-secondary-50)',
                    100: 'var(--color-secondary-100)',
                    200: 'var(--color-secondary-200)',
                    300: 'var(--color-secondary-300)',
                    400: 'var(--color-secondary-400)',
                    500: 'var(--color-secondary)',
                    600: 'var(--color-secondary-600)',
                    700: 'var(--color-secondary-700)',
                    800: 'var(--color-secondary-800)',
                    900: 'var(--color-secondary-900)',
                },
                success: {
                    DEFAULT: 'var(--color-success)',
                    50: 'var(--color-success-50)',
                    100: 'var(--color-success-100)',
                    200: 'var(--color-success-200)',
                    300: 'var(--color-success-300)',
                    400: 'var(--color-success-400)',
                    500: 'var(--color-success)',
                    600: 'var(--color-success-600)',
                    700: 'var(--color-success-700)',
                    800: 'var(--color-success-800)',
                    900: 'var(--color-success-900)',
                },
                danger: {
                    DEFAULT: 'var(--color-danger)',
                    50: 'var(--color-danger-50)',
                    100: 'var(--color-danger-100)',
                    200: 'var(--color-danger-200)',
                    300: 'var(--color-danger-300)',
                    400: 'var(--color-danger-400)',
                    500: 'var(--color-danger)',
                    600: 'var(--color-danger-600)',
                    700: 'var(--color-danger-700)',
                    800: 'var(--color-danger-800)',
                    900: 'var(--color-danger-900)',
                },
                warning: {
                    DEFAULT: 'var(--color-warning)',
                    50: 'var(--color-warning-50)',
                    100: 'var(--color-warning-100)',
                    200: 'var(--color-warning-200)',
                    300: 'var(--color-warning-300)',
                    400: 'var(--color-warning-400)',
                    500: 'var(--color-warning)',
                    600: 'var(--color-warning-600)',
                    700: 'var(--color-warning-700)',
                    800: 'var(--color-warning-800)',
                    900: 'var(--color-warning-900)',
                },
                info: {
                    DEFAULT: 'var(--color-info)',
                    50: 'var(--color-info-50)',
                    100: 'var(--color-info-100)',
                    200: 'var(--color-info-200)',
                    300: 'var(--color-info-300)',
                    400: 'var(--color-info-400)',
                    500: 'var(--color-info)',
                    600: 'var(--color-info-600)',
                    700: 'var(--color-info-700)',
                    800: 'var(--color-info-800)',
                    900: 'var(--color-info-900)',
                },
                accent: {
                    DEFAULT: 'var(--color-accent)',
                    50: 'var(--color-accent-50)',
                    100: 'var(--color-accent-100)',
                    200: 'var(--color-accent-200)',
                    300: 'var(--color-accent-300)',
                    400: 'var(--color-accent-400)',
                    500: 'var(--color-accent)',
                    600: 'var(--color-accent-600)',
                    700: 'var(--color-accent-700)',
                    800: 'var(--color-accent-800)',
                    900: 'var(--color-accent-900)',
                },
                dark: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                }
            },
            animation: {
                'fade-in': 'fadeIn 0.3s ease-in-out',
                'slide-in-right': 'slideInRight 0.3s ease-out',
                'pulse-slow': 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' }
                },
                slideInRight: {
                    '0%': { transform: 'translateX(30px)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' }
                }
            }
        },
    },

    plugins: [forms],
};
