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
					class="block rounded-sm bg-purple-500 px-5 py-2 text-sm font-semibold text-white transition-all duration-300 after:absolute after:inset-0 after:-translate-x-full after:rounded-sm after:bg-white after:transition-all after:duration-300 hover:text-black hover:after:translate-x-0"
				>
					<span class="relative z-50 flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
							<path
								d="M.329 10.333A8.01 8.01 0 0 0 7.99 16C12.414 16 16 12.418 16 8s-3.586-8-8.009-8A8.006 8.006 0 0 0 0 7.468l.003.006 4.304 1.769A2.198 2.198 0 0 1 5.62 8.88l1.96-2.844-.001-.04a3.046 3.046 0 0 1 3.042-3.043 3.046 3.046 0 0 1 3.042 3.043 3.047 3.047 0 0 1-3.111 3.044l-2.804 2a2.223 2.223 0 0 1-3.075 2.11 2.217 2.217 0 0 1-1.312-1.568L.33 10.333Z"
							/>
							<path
								d="M4.868 12.683a1.715 1.715 0 0 0 1.318-3.165 1.705 1.705 0 0 0-1.263-.02l1.023.424a1.261 1.261 0 1 1-.97 2.33l-.99-.41a1.7 1.7 0 0 0 .882.84Zm3.726-6.687a2.03 2.03 0 0 0 2.027 2.029 2.03 2.03 0 0 0 2.027-2.029 2.03 2.03 0 0 0-2.027-2.027 2.03 2.03 0 0 0-2.027 2.027Zm2.03-1.527a1.524 1.524 0 1 1-.002 3.048 1.524 1.524 0 0 1 .002-3.048Z"
							/>
						</svg>
						Login with Steam
					</span>
				</router-link>
			</div>
		</div>
	</header>
</template>

<style scoped></style>
