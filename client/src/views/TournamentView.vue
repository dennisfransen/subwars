<script setup lang="ts">
import { CubeIcon, CubeTransparentIcon, ChevronDoubleDownIcon } from '@heroicons/vue/24/outline'

const tournament = ref({
	id: 1,
	title: 'Fredagsrushen',
	registered: 10,
	teams: 8,
	checkedIn: 8,
	follower: true,
	subscriber: true,
	live_at: '2023-04-01',
})

const streamer = ref({
	id: 1,
	username: 'ProfessorGalen',
	twitch_username: 'meloncholy',
})

const casters = ref([
	{ id: 1, username: 'Tjenabrysh' },
	{ id: 2, username: 'Meloncholy' },
])

const sponsors = ref([
	{ id: 1, title: 'NOCCO' },
	{ id: 2, title: 'Tyngre' },
	{ id: 3, title: 'Elgiganten' },
])

const prices = ref([
	{ id: 1, title: 'Finska pinnar' },
	{ id: 2, title: '10x Monster 33cl' },
	{ id: 3, title: 'Magiska Grönsaker' },
])
</script>

<template>
	<main class="container mx-auto gap-x-10 py-10 px-4 lg:grid lg:grid-cols-12 lg:gap-y-16">
		<!-- Casters -->
		<!-- Sponsorer -->

		<!-- Lag -->
		<!-- Priser -->

		<section class="col-span-12 grid grid-cols-3 items-center">
			<nav class="flex items-center gap-6">
				<router-link to="/" class="font-graduate font-semibold uppercase">/ General</router-link>
				&bull;
				<router-link to="/" class="font-graduate font-semibold uppercase">/ Teams</router-link>
				&bull;
				<router-link to="/" class="font-graduate font-semibold uppercase">/ Rules</router-link>
			</nav>
			<div>
				<h3 class="text-center font-graduate text-6xl font-black uppercase tracking-wide">
					{{ streamer.twitch_username }}
				</h3>
			</div>
			<div class="flex items-center justify-end gap-6 font-graduate uppercase">
				<p>{{ tournament.title }}</p>
				&bull;
				<p>{{ tournament.live_at }}</p>
			</div>
		</section>

		<section class="col-span-9">
			<iframe
				src="https://player.twitch.tv/?channel=meloncholy&parent=subwars.se&muted=true"
				class="aspect-video w-full"
				allowfullscreen
			>
			</iframe>
		</section>

		<section class="col-span-3 flex flex-col gap-5 bg-black/80 p-6">
			<div class="flex items-center justify-between border-b border-b-zinc-600 py-4 font-graduate">
				<p class="uppercase tracking-wide text-zinc-600">Registered</p>
				<p class="text-5xl font-black">{{ tournament.registered }}</p>
			</div>

			<div class="flex items-center justify-between border-b border-b-zinc-600 py-4 font-graduate">
				<p class="uppercase tracking-wide text-zinc-600">Checked In</p>
				<p class="text-5xl font-black">{{ tournament.checkedIn }}</p>
			</div>

			<div class="flex items-center justify-between border-b border-b-zinc-600 py-4 font-graduate">
				<p class="uppercase tracking-wide text-zinc-600">Player slots</p>
				<p class="text-5xl font-black">{{ tournament.teams * 5 }}</p>
			</div>

			<button
				type="button"
				class="border border-zinc-600 py-4 text-sm font-semibold uppercase transition-all duration-300 hover:bg-white hover:text-black hover:shadow"
				title="Register to tournament"
			>
				Enlist to war
			</button>

			<button
				type="button"
				class="border border-zinc-600 py-4 text-sm font-semibold uppercase transition-all duration-300 hover:bg-white hover:text-black hover:shadow"
				title="Check in"
			>
				Go to war
			</button>

			<button
				type="button"
				class="border border-zinc-600 py-4 text-sm font-semibold uppercase transition-all duration-300 hover:bg-white hover:text-black hover:shadow"
				title="Leave tournament"
			>
				Abandon troups
			</button>
		</section>
	</main>
</template>
