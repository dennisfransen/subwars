<template>
	<header class="inset-x-0 top-0 z-50 hidden bg-zinc-900 shadow md:block">
		<div class="container mx-auto flex min-h-16 items-center justify-between px-4">
			<div class="flex items-center gap-16">
				<h1 class="font-graduate text-lg font-bold uppercase">
					<router-link to="/">Subwars</router-link>
				</h1>

				<nav class="flex items-center gap-10">
					<router-link
						to="/"
						class="relative block overflow-x-hidden text-sm text-gray-400 after:absolute after:bottom-0 after:left-0 after:h-[2px] after:w-full after:translate-x-full after:transform after:bg-gray-400 after:transition-transform after:duration-300"
						active-class="after:translate-x-0 text-white"
						>Home</router-link
					>
					<router-link
						to="/streamer"
						class="relative block overflow-x-hidden text-sm text-gray-400 after:absolute after:bottom-0 after:left-0 after:h-[2px] after:w-full after:translate-x-full after:transform after:bg-gray-400 after:transition-transform after:duration-300"
						active-class="after:translate-x-0 text-white"
						>Streamer</router-link
					>
					<router-link
						to="/tournaments"
						class="relative block overflow-x-hidden text-sm text-gray-400 after:absolute after:bottom-0 after:left-0 after:h-[2px] after:w-full after:translate-x-full after:transform after:bg-gray-400 after:transition-transform after:duration-300"
						active-class="after:translate-x-0 text-white"
						>Tournaments</router-link
					>
					<router-link
						to="/about"
						class="relative block overflow-x-hidden text-sm text-gray-400 after:absolute after:bottom-0 after:left-0 after:h-[2px] after:w-full after:translate-x-full after:transform after:bg-gray-400 after:transition-transform after:duration-300"
						active-class="after:translate-x-0 text-white"
						>About</router-link
					>
				</nav>
			</div>

			<div>
				<router-link
					to="/login"
					class="rounded bg-gray-700 px-5 py-2 text-sm font-medium transition-all duration-300 hover:bg-gray-600 hover:shadow"
				>
					Login
				</router-link>
			</div>
		</div>
	</header>
</template>
