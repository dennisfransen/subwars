<script setup lang="ts">
import { ArrowSmallRightIcon } from '@heroicons/vue/24/solid'
</script>

<template>
	<main class="grid lg:grid-cols-3">
		<div class="img-container group/img">
			<div class="img bg-mic-min"></div>
			<div class="img-inner">
				<div>
					<h2 class="font-graduate text-5xl font-bold uppercase tracking-wide">Streamer</h2>
					<p class="mt-2 text-lg">Do you wan't to host a tournament?</p>
					<router-link
						to="/streamer"
						class="transtiion-all mt-8 inline-flex items-center justify-between gap-6 rounded border border-white/50 px-4 py-2 font-sans text-lg duration-300 hover:border-white hover:bg-white hover:font-semibold hover:text-black hover:shadow-lg"
					>
						Become Streamer
						<ArrowSmallRightIcon class="h-5 w-5" />
					</router-link>
				</div>
			</div>
		</div>
		<div class="img-container group/img">
			<div class="img bg-half-keyboard-min"></div>
			<div class="img-inner">
				<div>
					<h2 class="font-graduate text-5xl font-bold uppercase tracking-wide">Tournaments</h2>
					<p class="mt-2 text-lg">Let's fight for glory and fine prices!</p>
					<router-link
						to="/tournaments"
						class="transtiion-all mt-8 inline-flex items-center justify-between gap-6 rounded border border-white/50 px-4 py-2 font-sans text-lg duration-300 hover:border-white hover:bg-white hover:font-semibold hover:text-black hover:shadow-lg"
					>
						View Tournaments
						<ArrowSmallRightIcon class="h-5 w-5" />
					</router-link>
				</div>
			</div>
		</div>
		<div class="img-container group/img">
			<div class="img bg-computer-min"></div>
			<div class="img-inner">
				<div>
					<h2 class="font-graduate text-5xl font-bold uppercase tracking-wide">About</h2>
					<p class="mt-2 text-lg">How do i become a fears warrior?</p>
					<router-link
						to="/about"
						class="transtiion-all mt-8 inline-flex items-center justify-between gap-6 rounded border border-white/50 px-4 py-2 font-sans text-lg duration-300 hover:border-white hover:bg-white hover:font-semibold hover:text-black hover:shadow-lg"
					>
						Read More
						<ArrowSmallRightIcon class="h-5 w-5" />
					</router-link>
				</div>
			</div>
		</div>
	</main>
</template>

<style scoped>
.img-container {
	@apply min-h-app relative h-full w-full overflow-hidden;
}

.img-inner {
	@apply absolute inset-0 z-20;
	@apply grid place-items-center;
}

.img {
	@apply absolute inset-0 overflow-hidden;
	@apply transition-all duration-700;
	@apply bg-cover bg-center bg-no-repeat brightness-50;
	@apply blur-md group-hover/img:blur-none;
	@apply scale-110 group-hover/img:scale-100;
}
</style>
