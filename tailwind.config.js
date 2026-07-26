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
            fontSize: {
                // Aumentar base de 1rem (16px) a 1.125rem (18px) - +2px global
                '2xs': ['0.625rem', { lineHeight: '0.875rem' }],      // 10px
                xs: ['0.75rem', { lineHeight: '1rem' }],              // 12px
                sm: ['0.875rem', { lineHeight: '1.25rem' }],          // 14px
                base: ['1.125rem', { lineHeight: '1.75rem' }],        // 18px (era 16px)
                lg: ['1.25rem', { lineHeight: '1.75rem' }],           // 20px
                xl: ['1.5rem', { lineHeight: '2rem' }],               // 24px
                '2xl': ['1.875rem', { lineHeight: '2.25rem' }],       // 30px
                '3xl': ['2.25rem', { lineHeight: '2.5rem' }],         // 36px
                '4xl': ['3rem', { lineHeight: '1' }],                 // 48px
                '5xl': ['3.75rem', { lineHeight: '1' }],              // 60px
                '6xl': ['4.5rem', { lineHeight: '1' }],               // 72px
                '7xl': ['6rem', { lineHeight: '1' }],                 // 96px
                '8xl': ['7.5rem', { lineHeight: '1' }],               // 120px
                '9xl': ['9rem', { lineHeight: '1' }],                 // 144px
            },
        },
    },

    plugins: [forms],
};
