/** @type {import('tailwindcss').Config} */
import forms from "@tailwindcss/forms";

export default {
    darkMode: 'class',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./pages/**/*.{html,js}",
        "./components/**/*.{html,js}",
    ],
    theme: {
        extend: {
            colors: {
                mintcream: {
                    100: '#F5FFFA',
                    200: '#E0F7F0',
                    300: '#D0F0E5',
                },
                seagreen: '#2E8B57',
            },
        },
    },
    plugins: [forms],
};
