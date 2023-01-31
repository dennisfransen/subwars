import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useApp = defineStore('app', () => {
	const isMenuOpen = ref(false)

	function toggleMenu() {
		isMenuOpen.value = !isMenuOpen.value
	}

	function closeMenu() {
		isMenuOpen.value = false
	}

	return { isMenuOpen, toggleMenu, closeMenu }
})
