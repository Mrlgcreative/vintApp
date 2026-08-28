import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
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
                // Palette Vinted Violet complète en Tailwind
                vinted: {
                    // Primary purple shades
                    primary: {
                        DEFAULT: '#7c3aed',
                        50: '#faf5ff',
                        100: '#f3e8ff',
                        200: '#e9d5ff',
                        300: '#d8b4fe',
                        400: '#c084fc',
                        500: '#a855f7',
                        600: '#7c3aed',
                        700: '#6d28d9',
                        800: '#5b21b6',
                        900: '#4c1d95',
                    },
                    // Secondary grays
                    secondary: {
                        DEFAULT: '#f3f4f6',
                        50: '#f9fafb',
                        100: '#f3f4f6',
                        200: '#e5e7eb',
                        300: '#d1d5db',
                        400: '#9ca3af',
                        500: '#6b7280',
                        600: '#4b5563',
                        700: '#374151',
                        800: '#1f2937',
                        900: '#111827',
                    },
                    // Accent light purple
                    accent: {
                        DEFAULT: '#faf5ff',
                        50: '#faf5ff',
                        100: '#f3e8ff',
                        200: '#e9d5ff',
                    },
                    // Success green
                    success: {
                        DEFAULT: '#10b981',
                        50: '#f0fdf4',
                        100: '#dcfce7',
                        200: '#bbf7d0',
                        300: '#86efac',
                        400: '#4ade80',
                        500: '#22c55e',
                        600: '#10b981',
                        700: '#15803d',
                        800: '#166534',
                        900: '#14532d',
                    },
                    // Warning orange
                    warning: {
                        DEFAULT: '#f59e0b',
                        50: '#fffbeb',
                        100: '#fef3c7',
                        200: '#fde68a',
                        300: '#fcd34d',
                        400: '#fbbf24',
                        500: '#f59e0b',
                        600: '#d97706',
                        700: '#b45309',
                        800: '#92400e',
                        900: '#78350f',
                    },
                    // Danger red
                    danger: {
                        DEFAULT: '#ef4444',
                        50: '#fef2f2',
                        100: '#fee2e2',
                        200: '#fecaca',
                        300: '#fca5a5',
                        400: '#f87171',
                        500: '#ef4444',
                        600: '#dc2626',
                        700: '#b91c1c',
                        800: '#991b1b',
                        900: '#7f1d1d',
                    },
                },
                // Alias compatibilité (utilise vinted)
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
                'fade-in-up': 'fadeInUp 0.3s ease-out',
                'slide-in-right': 'slideInRight 0.3s ease-out',
                'pulse-slow': 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'shimmer': 'shimmer 1.5s infinite',
                'shimmer-overlay': 'shimmer-overlay 2s infinite',
                // Splash
                'splash-cart': 'splashCartAppear 1s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s forwards',
                'splash-fill': 'splashCartFill 0.6s ease-out 1.2s forwards',
                'splash-draw': 'splashStrokeDraw 1s cubic-bezier(0.4, 0, 0.2, 1) 0.4s forwards',
                'splash-draw-delay': 'splashStrokeDraw 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.7s forwards',
                'splash-item-1': 'splashItemPop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) 1.0s forwards',
                'splash-item-2': 'splashItemPop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) 1.15s forwards',
                'splash-item-3': 'splashItemPop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) 1.3s forwards',
                'splash-tag': 'splashTagBounce 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 1.45s forwards',
                'splash-circle': 'splashCircleGrow 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.3s forwards, splashCirclePulse 2.5s ease-in-out 1.2s infinite',
                'splash-hint': 'splashFadeUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) 2.6s forwards',
                'splash-outro': 'splashFadeOutZoom 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' }
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' }
                },
                slideInRight: {
                    '0%': { transform: 'translateX(30px)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' }
                },
                shimmer: {
                    '0%': { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' }
                },
                'shimmer-overlay': {
                    '100%': { transform: 'translateX(100%)' }
                },
                // Splash
                splashCartAppear: {
                    '0%': { opacity: '0', transform: 'scale(0) rotate(-180deg)' },
                    '70%': { opacity: '1', transform: 'scale(1.08) rotate(5deg)' },
                    '100%': { opacity: '1', transform: 'scale(1) rotate(0deg)' }
                },
                splashCartFill: {
                    '100%': { fillOpacity: '0.12' }
                },
                splashStrokeDraw: {
                    '100%': { strokeDashoffset: '0' }
                },
                splashItemPop: {
                    '0%': { opacity: '0', transform: 'scale(0) translateY(4px)' },
                    '70%': { transform: 'scale(1.15) translateY(0)' },
                    '100%': { opacity: '1', transform: 'scale(1) translateY(0)' }
                },
                splashTagBounce: {
                    '0%': { opacity: '0', transform: 'scale(0) rotate(-20deg)' },
                    '60%': { transform: 'scale(1.2) rotate(5deg)' },
                    '100%': { opacity: '1', transform: 'scale(1) rotate(0deg)' }
                },
                splashCircleGrow: {
                    '0%': { opacity: '0', transform: 'scale(0)' },
                    '100%': { opacity: '1', transform: 'scale(1)' }
                },
                splashCirclePulse: {
                    '0%, 100%': { transform: 'scale(1)', opacity: '1' },
                    '50%': { transform: 'scale(1.06)', opacity: '0.7' }
                },
                splashFadeUp: {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' }
                },
                splashFadeOutZoom: {
                    '0%': { opacity: '1', transform: 'scale(1)', filter: 'blur(0)' },
                    '100%': { opacity: '0', transform: 'scale(1.1)', filter: 'blur(16px)' }
                }
            },
            borderRadius: {
                'vinted': '8px',
                'vinted-lg': '12px',
                'vinted-xl': '16px',
            },
            boxShadow: {
                'vinted-sm': '0 2px 8px rgba(0, 0, 0, 0.08)',
                'vinted': '0 4px 16px rgba(0, 0, 0, 0.12)',
                'vinted-lg': '0 8px 24px rgba(0, 0, 0, 0.15)',
                'vinted-xl': '0 20px 40px rgba(0, 0, 0, 0.15)',
                'vinted-primary': '0 4px 12px rgba(124, 58, 237, 0.3)',
                'vinted-focus': '0 0 0 3px rgba(124, 58, 237, 0.1)',
            },
            transitionDuration: {
                '400': '400ms',
            },
        },
    },

    plugins: [forms],
};
