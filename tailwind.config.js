import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

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
                primary: "var(--color-primary)",
                secondary: "var(--color-secondary)",
                navy: "var(--color-bg-body)",
                surface: "var(--color-bg-card)",
                mainText: "var(--color-text-main)",
                mutedText: "var(--color-text-muted)",
                customWhite: "var(--color-white)",
            },
        },
    },
    plugins: [forms],
};
