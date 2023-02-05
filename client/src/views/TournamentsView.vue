<script setup lang="ts">
import { AcademicCapIcon, CameraIcon } from '@heroicons/vue/24/solid'

const tournaments = ref([
	{
		id: 1,
		title: 'Fredagsrushen',
		streamer: 'PG_CS',
		registered: 10,
		teams: 8,
		follower: true,
		subscriber: true,
		live_at: '2023-04-01',
	},
	{
		id: 2,
		title: 'Lördagsrushen',
		streamer: 'Tjenabrysh',
		registered: 40,
		teams: 8,
		follower: true,
		subscriber: true,
		live_at: '2023-04-02',
	},
])
</script>

<template>
	<main class="container mx-auto p-4">
		<div
			class="fixed inset-0 -z-10 bg-csgo bg-cover bg-no-repeat after:absolute after:inset-0 after:z-0 after:bg-black/80"
		></div>
		<section>
			<ul>
				<li class="grid grid-cols-5 border-b border-b-zinc-800 p-6">
					<h3 class="font-bold">Streamer</h3>
					<h3 class="font-bold">Tournament</h3>
					<h3 class="font-bold">Registered</h3>
					<h3 class="font-bold">Date</h3>
					<h3 class="text-right font-bold">Requirements</h3>
				</li>
				<li v-for="tournament in tournaments" :key="tournament.id">
					<router-link
						:to="`/tournament/${tournament.id}`"
						class="mt-4 grid grid-cols-5 rounded bg-zinc-800 p-6 transition-all duration-300 hover:bg-zinc-700 hover:font-bold hover:shadow-lg"
					>
						<p class="font-bold">{{ tournament.streamer }}</p>
						<p>{{ tournament.title }}</p>
						<p>{{ tournament.registered }}/{{ tournament.teams * 5 }}</p>
						<p>{{ tournament.live_at }}</p>
						<div class="flex items-center justify-end gap-4">
							<CameraIcon v-if="tournament.follower" class="h-5 w-5 text-purple-400" title="follower" />
							<AcademicCapIcon v-if="tournament.subscriber" class="h-5 w-5 text-yellow-400" title="subscriber" />
						</div>
					</router-link>
				</li>
			</ul>
		</section>
	</main>
</template>
