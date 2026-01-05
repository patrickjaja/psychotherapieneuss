/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./templates/**/*.{html,twig}",
    "./src/**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        primary: '#A0826D',
        secondary: '#8B7355',
        accent: '#C4A77D',
        neutral: '#5D534A',
        'neutral-light': '#FAF7F2',
        'footer-dark': '#3D352E'
      },
      fontFamily: {
        sans: ['-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', 'sans-serif'],
      }
    }
  },
  plugins: [],
}
