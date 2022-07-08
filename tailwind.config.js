let colors = {
    "primary": "var(--primary)",
    'primary-light': "var(--primary-light)",

    "main": "var(--main)",

    "sidebar": "var(--sidebar)",
    "sidebar-icon": "var(--sidebar-icon)",
    "sidebar-menu": "var(--sidebar-menu)",
    "sidebar-menu-active": "var(--sidebar-menu-active)",
    
    "info": "var(--info)",
    "danger": "var(--danger)",
    "warning": "var(--warning)",
    "success": "var(--success)",

    "tooltip": "var(--tooltip)",
    "tooltip-text": "var(--tooltip-text)"
}

module.exports = {
    important: false,
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors,
            zIndex: {
                100: 100
            }
        }
    },
    plugins: [
        require("@tailwindcss/forms")({
            strategy: 'class',
        }),
    ],
    corePlugins: {
        preflight: false,
    }
}