import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/**
 * Tokens visuais da Plataforma de Gestão de Projetos.
 *
 * - Cores de superfície e texto usam variáveis CSS (definidas em
 *   resources/css/app.css) e mudam automaticamente entre tema claro e escuro.
 * - `brand` (índigo) é a cor primária e `accent` (âmbar) a cor de destaque,
 *   as mesmas usadas nos slides da apresentação.
 */
const token = (name) => `rgb(var(--${name}) / <alpha-value>)`;

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/Enums/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                canvas: token('canvas'),
                surface: {
                    DEFAULT: token('surface'),
                    muted: token('surface-muted'),
                    raised: token('surface-raised'),
                },
                line: {
                    DEFAULT: token('line'),
                    strong: token('line-strong'),
                },
                ink: {
                    DEFAULT: token('ink'),
                    muted: token('ink-muted'),
                    subtle: token('ink-subtle'),
                },
                brand: {
                    50: '#eef0ff',
                    100: '#e0e4ff',
                    200: '#c7ccfe',
                    300: '#a4a9fc',
                    400: '#8184f8',
                    500: '#6366f1',
                    600: '#5046e5',
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                    950: '#1e1b4b',
                },
                accent: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                },
            },
            borderRadius: {
                xl: '0.875rem',
                '2xl': '1.125rem',
            },
            boxShadow: {
                card: '0 1px 2px 0 rgb(15 23 42 / 0.04), 0 1px 3px 0 rgb(15 23 42 / 0.06)',
                lift: '0 12px 32px -12px rgb(15 23 42 / 0.30)',
            },
            keyframes: {
                'fade-in': {
                    from: { opacity: '0', transform: 'translateY(4px)' },
                    to: { opacity: '1', transform: 'translateY(0)' },
                },
            },
            animation: {
                'fade-in': 'fade-in .25s ease-out both',
            },
        },
    },

    plugins: [forms],
};
