import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'

export default defineConfig({
	plugins: [
		vue(),
		Components({
			dts: true,
			dirs: ['./src/components'],
		}),
		AutoImport({
			dts: true,
			dirs: ['./src/composables'],
			imports: ['vue'],
		}),
	],
	resolve: {
		alias: {
			'@': fileURLToPath(new URL('./src', import.meta.url)),
		},
	},
})
