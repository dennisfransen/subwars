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
				crowd: "url('@/assets/images/crowd.jpg')",
				streaming: "url('@/assets/images/streaming.jpg')",
				keyboard: "url('@/assets/images/keyboard.jpg')",
				smoking: "url('@/assets/images/smoking.jpg')",
				csgo: "url('@/assets/images/csgo.jpeg')",
			},
			fontFamily: {
				graduate: 'Graduate, cursive',
				inter: 'Inter, sans-serif',
			},
		},
	},
	plugins: [],
}
