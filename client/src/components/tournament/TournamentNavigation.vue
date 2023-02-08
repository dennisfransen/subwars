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
	is_live: true,
})

const streamer = ref({
	id: 1,
	username: 'ProfessorGalen',
	twitch_username: 'meloncholy',
})
</script>

<template>
	<section class="flex items-start justify-between gap-4 pt-10 pb-14">
		<div>
			<h3 class="text-center font-graduate text-5xl font-black uppercase tracking-wide">
				{{ streamer.twitch_username }}
			</h3>
			<div class="mt-2 flex items-center gap-2">
				<span
					v-if="tournament.is_live"
					class="relative inline-block h-4 w-4 rounded-full bg-purple-500/50 after:absolute after:left-1/2 after:top-1/2 after:-translate-y-1/2 after:-translate-x-1/2 after:rounded-full after:bg-purple-500 after:p-1"
				></span>
				<p class="text-zinc-500">{{ tournament.title }} &bull; {{ tournament.live_at }}</p>
			</div>
		</div>

		<nav class="flex flex-1 flex-wrap items-center justify-end gap-6 text-zinc-500">
			<router-link
				:to="{ name: 'tournament-stream', params: { id: tournament.id } }"
				class="font-graduate text-sm uppercase"
				v-slot="{ isExactActive }"
			>
				<span :class="{ 'text-base text-white': isExactActive }">Stream</span>
			</router-link>
			|
			<router-link
				:to="{ name: 'tournament-teams', params: { id: tournament.id } }"
				class="font-graduate text-sm uppercase"
				v-slot="{ isExactActive }"
			>
				<span :class="{ 'text-base text-white': isExactActive }">Teams</span>
			</router-link>
			|
			<router-link
				:to="{ name: 'tournament-prices', params: { id: tournament.id } }"
				class="font-graduate text-sm uppercase"
				v-slot="{ isExactActive }"
			>
				<span :class="{ 'text-base text-white': isExactActive }">Prices</span>
			</router-link>
			|
			<router-link
				:to="{ name: 'tournament-rules', params: { id: tournament.id } }"
				class="font-graduate text-sm uppercase"
				v-slot="{ isExactActive }"
			>
				<span :class="{ 'text-base text-white': isExactActive }">Rules</span>
			</router-link>
			|
			<router-link
				:to="{ name: 'tournament-settings-teams', params: { id: tournament.id } }"
				class="font-graduate text-sm uppercase"
				v-slot="{ isExactActive }"
			>
				<span :class="{ 'text-base text-white': isExactActive }">Settings</span>
			</router-link>
		</nav>
	</section>
</template>
