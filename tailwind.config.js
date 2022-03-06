let colors = {
    transparent: "var(--transparent)",
    black: "var(--black)",
    white: "var(--white)",

    primary: "var(--primary)",
    'primary-light': "var(--primary-light)",

    main: "var(--main)",

    sidebar: "var(--sidebar)",
    "sidebar-icon": "var(--sidebar-icon)",
    "sidebar-menu": "var(--sidebar-menu)",
    "sidebar-menu-active": "var(--sidebar-menu-active)",

    info: "var(--info)",
    danger: "var(--danger)",
    warning: "var(--warning)",
    success: "var(--success)",

    tooltip: "var(--tooltip)",
    "tooltip-text": "var(--tooltip-text)"
};

module.exports = {
    // important: true,
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        fontFamily: {
            sans: ["Graphik", "sans-serif"],
            serif: ["Merriweather", "serif"],
        },
        textSizes: {
            xs: ".75rem", // 12px
            sm: ".875rem", // 14px
            base: "1rem", // 16px
            lg: "1.125rem", // 18px
            xl: "1.25rem", // 20px
            "2xl": "1.5rem", // 24px
            "3xl": "1.875rem", // 30px
            "4xl": "2.25rem", // 36px
            "5xl": "3rem", // 48px
        },
        fontWeights: {
            hairline: 200,
            thin: 200,
            light: 300,
            normal: 400,
            medium: 400,
            semibold: 600,
            bold: 800,
            extrabold: 800,
        },
        leading: {
            none: 1,
            tight: 1.25,
            normal: 1.5,
            loose: 2,
            9: "2.25rem",
            12: "3rem",
            36: "2.25rem",
        },
        screens: {
            tablet: "640px",
            laptop: "1024px",
            desktop: "1280px",
        },
        spacing: {
            0: "0",
            '0.5': "4px",
            1: "8px",
            2: "12px",
            3: "16px",
            4: "24px",
            5: "32px",
            6: "48px",
            7: "64px",
            8: "96px",
        },
        extend: {
            colors,
            textColor: colors,
            backgroundColor: colors,
            borderWidth: {
                3: '3px'
            },
            margin: {
                '0.5': '0.125rem'
            },
            padding: {
                '0.5': '0.125rem'
            },
            width: {
                sidebar: "300px",
            },
            minWidth: {
                button: "120px",
            },
            height: {
                loading: "8px",
                header: "46px",
            },
            zIndex: {
                100: 100,
            }
        },
    },
    plugins: [
        require("@tailwindcss/forms")
    ],
    corePlugins: {
        preflight: false,
    },
};
