<template>
	<header class="inset-x-0 top-0 z-50 hidden bg-zinc-900 shadow md:block">
		<div class="container mx-auto flex min-h-16 items-center justify-between px-4">
			<div class="flex items-center gap-16">
				<h1 class="py-2 font-graduate text-lg font-bold uppercase">
					<router-link :to="{ name: 'home' }">Subwars</router-link>
				</h1>

				<nav class="flex items-center gap-10">
					<div class="relative">
						<router-link :to="{ name: 'home' }" class="text-sm text-zinc-500" v-slot="{ isActive }">
							<span
								class="after:absolute after:-bottom-5 after:left-0 after:right-0 after:h-[2px] after:bg-zinc-500 after:opacity-0 after:transition-all after:duration-500"
								:class="{ 'text-white after:opacity-100': isActive }"
							>
								Home
							</span>
						</router-link>
					</div>

					<div class="relative">
						<router-link :to="{ name: 'tournaments' }" class="text-sm text-zinc-500" v-slot="{ isActive }">
							<span
								class="after:absolute after:-bottom-5 after:left-0 after:right-0 after:h-[2px] after:bg-zinc-500 after:opacity-0 after:transition-all after:duration-500"
								:class="{ 'text-white after:opacity-100': isActive }"
							>
								Tournaments
							</span>
						</router-link>
					</div>

					<div class="relative">
						<router-link :to="{ name: 'streamer' }" class="text-sm text-zinc-500" v-slot="{ isActive }">
							<span
								class="after:absolute after:-bottom-5 after:left-0 after:right-0 after:h-[2px] after:bg-zinc-500 after:opacity-0 after:transition-all after:duration-500"
								:class="{ 'text-white after:opacity-100': isActive }"
							>
								Streamer
							</span>
						</router-link>
					</div>

					<div class="relative">
						<router-link :to="{ name: 'about' }" class="text-sm text-zinc-500" v-slot="{ isActive }">
							<span
								class="after:absolute after:-bottom-5 after:left-0 after:right-0 after:h-[2px] after:bg-zinc-500 after:opacity-0 after:transition-all after:duration-500"
								:class="{ 'text-white after:opacity-100': isActive }"
							>
								About
							</span>
						</router-link>
					</div>
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

<style scoped></style>
