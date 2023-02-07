<template>
	<header class="inset-x-0 top-0 z-50 hidden bg-zinc-900 shadow md:block">
		<div class="container mx-auto flex min-h-16 items-center justify-between px-4">
			<div class="flex items-center gap-16">
				<h1 class="py-2 font-graduate text-lg font-bold uppercase">
					<router-link :to="{ name: 'home' }">Subwars</router-link>
				</h1>

				<nav class="space-x-10">
					<router-link :to="{ name: 'home' }" class="text-sm text-zinc-500" v-slot="{ isActive }">
						<span :class="{ 'text-white': isActive }">Home</span>
					</router-link>

					<router-link :to="{ name: 'tournaments' }" class="text-sm text-zinc-500" v-slot="{ isActive }">
						<span :class="{ 'text-white': isActive }">Tournaments</span>
					</router-link>

					<router-link :to="{ name: 'streamer' }" class="text-sm text-zinc-500" v-slot="{ isActive }">
						<span :class="{ 'text-white': isActive }">Streamer</span>
					</router-link>

					<router-link :to="{ name: 'about' }" class="text-sm text-zinc-500" v-slot="{ isActive }">
						<span :class="{ 'text-white': isActive }">About</span>
					</router-link>
				</nav>
			</div>

			<div class="relative overflow-hidden">
				<router-link
					to="/login"
					class="block rounded-sm bg-white px-5 py-2 text-sm font-semibold text-black transition-colors duration-300 after:absolute after:inset-0 after:-translate-x-full after:rounded-sm after:bg-purple-500 after:transition-all after:duration-300 hover:text-white hover:after:translate-x-0"
				>
					<span class="relative z-50">Login with Steam</span>
				</router-link>
			</div>
		</div>
	</header>
</template>

<style scoped></style>
