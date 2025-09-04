module.exports = {
  content: ["./pages/*.{html,js}", "./index.html", "./js/*.js"],
  theme: {
    extend: {
      colors: {
        // Primary Colors - Forest Green
        primary: {
          DEFAULT: "#2D5A27", // forest-green-500
          50: "#F0F7EF", // forest-green-50
          100: "#D4E8D1", // forest-green-100
          200: "#A9D1A3", // forest-green-200
          300: "#7EBA75", // forest-green-300
          400: "#53A347", // forest-green-400
          500: "#2D5A27", // forest-green-500
          600: "#244821", // forest-green-600
          700: "#1B361B", // forest-green-700
          800: "#122415", // forest-green-800
          900: "#09120F", // forest-green-900
        },
        // Secondary Colors - Harvest Orange
        secondary: {
          DEFAULT: "#F4A261", // harvest-orange-500
          50: "#FEF7F0", // harvest-orange-50
          100: "#FDEBD4", // harvest-orange-100
          200: "#FBD7A9", // harvest-orange-200
          300: "#F9C37E", // harvest-orange-300
          400: "#F7AF53", // harvest-orange-400
          500: "#F4A261", // harvest-orange-500
          600: "#E6944D", // harvest-orange-600
          700: "#D88639", // harvest-orange-700
          800: "#CA7825", // harvest-orange-800
          900: "#BC6A11", // harvest-orange-900
        },
        // Accent Colors - Coral
        accent: {
          DEFAULT: "#E76F51", // coral-500
          50: "#FDF4F2", // coral-50
          100: "#FAE3DE", // coral-100
          200: "#F5C7BD", // coral-200
          300: "#F0AB9C", // coral-300
          400: "#EB8F7B", // coral-400
          500: "#E76F51", // coral-500
          600: "#D95A3A", // coral-600
          700: "#CB4523", // coral-700
          800: "#BD300C", // coral-800
          900: "#9A2710", // coral-900
        },
        // Background Colors
        background: "#FEFEFE", // neutral-50
        surface: "#F8F9FA", // neutral-100
        // Text Colors
        text: {
          primary: "#2C3E50", // slate-700
          secondary: "#6C757D", // slate-500
        },
        // Status Colors
        success: {
          DEFAULT: "#27AE60", // emerald-600
          50: "#ECFDF5", // emerald-50
          100: "#D1FAE5", // emerald-100
          500: "#10B981", // emerald-500
          600: "#27AE60", // emerald-600
        },
        warning: {
          DEFAULT: "#F39C12", // amber-600
          50: "#FFFBEB", // amber-50
          100: "#FEF3C7", // amber-100
          500: "#F59E0B", // amber-500
          600: "#F39C12", // amber-600
        },
        error: {
          DEFAULT: "#E74C3C", // red-600
          50: "#FEF2F2", // red-50
          100: "#FEE2E2", // red-100
          500: "#EF4444", // red-500
          600: "#E74C3C", // red-600
        },
        // Neutral Colors
        neutral: {
          50: "#F8FAFC", // slate-50
          100: "#F1F5F9", // slate-100
          200: "#E2E8F0", // slate-200
          300: "#CBD5E1", // slate-300
          400: "#94A3B8", // slate-400
          500: "#64748B", // slate-500
          600: "#475569", // slate-600
          700: "#334155", // slate-700
          800: "#1E293B", // slate-800
          900: "#0F172A", // slate-900
        },
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        inter: ['Inter', 'sans-serif'],
        caveat: ['Caveat', 'cursive'],
      },
      fontWeight: {
        normal: '400',
        medium: '500',
        semibold: '600',
        bold: '700',
      },
      boxShadow: {
        soft: '0 2px 8px rgba(0, 0, 0, 0.08)',
        medium: '0 4px 12px rgba(0, 0, 0, 0.12)',
      },
      transitionDuration: {
        smooth: '300ms',
      },
      transitionTimingFunction: {
        'ease-out': 'ease-out',
      },
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
      },
      borderRadius: {
        'xl': '0.75rem',
        '2xl': '1rem',
      },
    },
  },
  plugins: [],
}