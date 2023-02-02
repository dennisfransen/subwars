/** @type {import('tailwindcss').Config} */
module.exports = {
	content: ['./index.html', './src/**/*.{js,ts,vue}'],
	theme: {
		extend: {
			minHeight: {
				16: '4rem',
			},
			backgroundImage: {
				mirage: "url('@/assets/images/mirage.webp')",
				anubis: "url('@/assets/images/anubis-1.webp')",
				inferno: "url('@/assets/images/inferno.webp')",
			},
			fontFamily: {
				graduate: 'Graduate, cursive',
				inter: 'Inter, sans-serif',
			},
		},
	},
	plugins: [],
}
