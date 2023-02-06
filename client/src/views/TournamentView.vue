<script setup lang="ts">
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
	<main class="container mx-auto p-4">
		<section class="flex items-start justify-between gap-4 pt-10 pb-14">
			<div>
				<h3 class="text-center font-graduate text-5xl font-black uppercase tracking-wide">
					{{ streamer.twitch_username }}
				</h3>
				<p class="mt-2 text-zinc-500">{{ tournament.title }} &bull; {{ tournament.live_at }}</p>
			</div>

			<nav class="flex flex-1 flex-wrap items-center justify-end gap-6 text-zinc-500">
				<router-link
					:to="{ name: 'tournament-general', params: { id: tournament.id } }"
					class="font-graduate text-sm uppercase"
					v-slot="{ isActive }"
				>
					<span :class="{ 'text-base text-white': isActive }">General</span>
				</router-link>
				|
				<router-link
					:to="{ name: 'tournament-teams', params: { id: tournament.id } }"
					class="font-graduate text-sm uppercase"
					v-slot="{ isActive }"
				>
					<span :class="{ 'text-base text-white': isActive }">Teams</span>
				</router-link>
				|
				<router-link
					:to="{ name: 'tournament-prices', params: { id: tournament.id } }"
					class="font-graduate text-sm uppercase"
					v-slot="{ isActive }"
				>
					<span :class="{ 'text-base text-white': isActive }">Prices</span>
				</router-link>
				|
				<router-link
					:to="{ name: 'tournament-rules', params: { id: tournament.id } }"
					class="font-graduate text-sm uppercase"
					v-slot="{ isActive }"
				>
					<span :class="{ 'text-base text-white': isActive }">Rules</span>
				</router-link>
				|
				<router-link
					:to="{ name: 'tournament-settings', params: { id: tournament.id } }"
					class="font-graduate text-sm uppercase"
					v-slot="{ isActive }"
				>
					<span :class="{ 'text-base text-white': isActive }">Settings</span>
				</router-link>
			</nav>
		</section>

		<div class="grid grid-cols-12 gap-10">
			<div class="col-span-9">
				<router-view />
			</div>

			<div class="col-span-3">
				<TournamentActions :tournament="tournament" />
			</div>
		</div>
	</main>
</template>
