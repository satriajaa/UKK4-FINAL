import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                evergreen: {
                    50: "#e8ffe5",
                    100: "#d0ffcc",
                    200: "#a2ff99",
                    300: "#73ff66",
                    400: "#44ff33",
                    500: "#15ff00",
                    600: "#11cc00",
                    700: "#0d9900",
                    800: "#096600",
                    900: "#043300",
                    950: "#032400",
                },
            },
            fontFamily: {
                sans: ["Inter", "system-ui", "sans-serif"],
            },
        },
    },
    plugins: [],
};
