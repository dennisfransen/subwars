<script setup>
import { storeToRefs } from 'pinia'

const store = useApp()

const { toggleMenu, closeMenu } = store
const { isMenuOpen } = storeToRefs(store)
</script>

<template>
	<header class="sticky inset-x-0 top-0 z-50 bg-zinc-900 shadow md:hidden">
		<div class="container mx-auto flex min-h-16 items-center justify-between px-4">
			<h1 class="text-lg font-bold">
				<router-link to="/">Subwars</router-link>
			</h1>

			<button type="button" class="text-xl" @click="toggleMenu">&#9776;</button>

			<div
				class="fixed inset-y-0 right-0 z-40 w-64 space-y-6 bg-zinc-900 p-4 shadow transition-transform"
				:class="isMenuOpen ? 'translate-x-0' : 'translate-x-full'"
			>
				<div class="flex items-center justify-between">
					<h3 class="font-semibold">Menu</h3>
					<button type="button" class="text-xl" @click="toggleMenu">&times;</button>
				</div>

				<nav class="flex flex-col justify-end space-y-1">
					<router-link to="/" class="link" active-class="link-active">Home</router-link>
					<router-link to="/streamer" class="link" active-class="link-active">Streamer</router-link>
					<router-link to="/tournaments" class="link" active-class="link-active">Tournaments</router-link>
					<router-link to="/about" class="link" active-class="link-active">About</router-link>
				</nav>

				<hr />

				<div>
					<router-link to="/login" class="rounded bg-gray-700 px-5 py-2 text-sm font-medium">Login</router-link>
				</div>
			</div>
		</div>
	</header>
	<div class="fixed inset-0 z-30" :class="isMenuOpen ? 'translate-x-0' : 'translate-x-full'" @click="closeMenu"></div>
</template>
