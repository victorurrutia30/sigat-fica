import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                utec: {
                    primary: '#5A1533',
                    'primary-dark': '#3D0D22',
                    'primary-light': '#7A2548',
                    'primary-soft': '#F0E8ED',
                    'gray-medium': '#C9C9C9',
                    'gray-dark': '#343434',
                    'bg-light': '#F5F5F5',
                    success: '#2E7D32',
                    warning: '#E65100',
                    danger: '#B71C1C',
                    info: '#1565C0',
                },
            },
        },
    },

    plugins: [forms],
};