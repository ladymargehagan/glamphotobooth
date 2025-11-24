module.exports = {
  content: [
    "./index.php",
    "./login.php",
    "./register.php",
    "./gallery.php",
    "./packages.php",
    "./provider-signup.php",
    "./views/**/*.php",
    "./views/components/**/*.php",
    "./js/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        'navy': {
          'dark': '#1B2B4D',
          'medium': '#2C3E5F'
        },
        'gold': {
          'primary': '#D4AF78',
          'accent': '#C9A961'
        },
        'cream': '#F5F5F0'
      },
      fontFamily: {
        'heading': ["'Lavishly Yours'", "'Playfair Display'", "'Georgia'", 'serif'],
        'body': ["'Montserrat'", "'Inter'", "'Helvetica Neue'", 'sans-serif']
      },
      fontSize: {
        'xs': '0.75rem',
        'sm': '0.875rem',
        'base': '1rem',
        'lg': '1.125rem',
        'xl': '1.25rem',
        '2xl': '1.5rem',
        '3xl': '2rem',
        '4xl': '2.75rem',
        '5xl': '3.5rem'
      },
      spacing: {
        'xs': '0.5rem',
        'sm': '1rem',
        'md': '1.5rem',
        'lg': '2rem',
        'xl': '3rem',
        '2xl': '4rem'
      },
      boxShadow: {
        'sm': '0 1px 2px rgba(27, 43, 77, 0.05)',
        'md': '0 4px 6px rgba(27, 43, 77, 0.1)',
        'lg': '0 10px 15px rgba(27, 43, 77, 0.15)',
        'xl': '0 20px 25px rgba(27, 43, 77, 0.2)',
        'gold': '0 4px 20px rgba(212, 175, 120, 0.3)'
      },
      borderRadius: {
        'sm': '4px',
        'md': '8px',
        'lg': '12px',
        'xl': '16px'
      },
      transitionDuration: {
        'fast': '150ms',
        'normal': '300ms',
        'slow': '500ms'
      }
    }
  },
  plugins: [],
}
