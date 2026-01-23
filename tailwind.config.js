import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["var(--font-main)", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Using CSS variables allows runtime theme switching if needed
                primary: "rgba(var(--color-primary), <alpha-value>)",
                secondary: "rgba(var(--color-secondary), <alpha-value>)",
                dark: "rgba(var(--color-text-main), <alpha-value>)",
                muted: "rgba(var(--color-text-muted), <alpha-value>)",
            },
        },
    },

    plugins: [forms],
};
