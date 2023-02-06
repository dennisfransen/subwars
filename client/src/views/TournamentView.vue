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
	twitch_username: 'PG_CS',
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
	<main class="container mx-auto p-4 lg:grid lg:grid-cols-12">
		<!-- Streamer -->
		<!-- Casters -->
		<!-- Sponsorer -->
		<!-- Stream -->
		<!-- Turneringsnamn -->

		<!-- Turneringsinformation -->
		<!-- Turneringsdatum -->
		<!-- Antal Reggade -->
		<!-- Antal platser -->
		<!-- Antal Incheckade -->

		<!-- Lag -->
		<!-- Priser -->

		<!-- Regga sig -->
		<!-- Checka in -->
		<!-- Lämna -->

		<section class="col-span-9">
			<p>Tournament page</p>
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
