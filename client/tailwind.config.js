/** @type {import('tailwindcss').Config} */
module.exports = {
	content: ['./index.html', './src/**/*.{js,ts,vue}'],
	theme: {
		extend: {
			minHeight: {
				16: '4rem',
			},
			backgroundImage: {
				smoking: "url('@/assets/images/smoking.jpg')",
				csgo: "url('@/assets/images/csgo.jpeg')",

				't-red': "url('@/assets/images/t-red.jpg')",
				't-green': "url('@/assets/images/t-green.jpg')",
				'ct-green': "url('@/assets/images/ct-green.jpg')",

				crowd: "url('@/assets/images/crowd.jpg')",
				streaming: "url('@/assets/images/streaming.jpg')",
				keyboard: "url('@/assets/images/keyboard.jpg')",
				question: "url('@/assets/images/question.jpg')",
				'half-keyboard': "url('@/assets/images/half-keyboard.jpg')",
				mic: "url('@/assets/images/mic.jpg')",
				computer: "url('@/assets/images/computer.jpg')",

				'crowd-min': "url('@/assets/images/crowd-min.jpg')",
				'streaming-min': "url('@/assets/images/streaming-min.jpg')",
				'keyboard-min': "url('@/assets/images/keyboard-min.jpg')",
				'question-min': "url('@/assets/images/question-min.jpg')",
				'half-keyboard-min': "url('@/assets/images/half-keyboard-min.jpg')",
				'mic-min': "url('@/assets/images/mic-min.jpg')",
				'computer-min': "url('@/assets/images/computer-min.jpg')",
			},
			fontFamily: {
				graduate: 'Graduate, cursive',
				inter: 'Inter, sans-serif',
			},
		},
	},
	plugins: [],
}
