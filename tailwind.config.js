import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ["var(--font-main)", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // <alpha-value> is essential for opacity like bg-primary/10
                primary: "rgb(var(--color-primary) / <alpha-value>)",
                secondary: "rgb(var(--color-secondary) / <alpha-value>)",
                navy: "rgb(var(--color-bg-body) / <alpha-value>)",
                surface: "rgb(var(--color-bg-card) / <alpha-value>)",
                mainText: "rgb(var(--color-text-main) / <alpha-value>)",
                mutedText: "rgb(var(--color-text-muted) / <alpha-value>)",
                customWhite: "rgb(var(--color-white) / <alpha-value>)",
            },
            // Custom animation for your product ID toggle
            animation: {
                'fade-in-down': 'fadeInDown 0.3s ease-out',
            },
            keyframes: {
                fadeInDown: {
                    '0%': { opacity: '0', transform: 'translateY(-10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },
    plugins: [forms],
};
