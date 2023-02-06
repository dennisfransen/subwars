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
		<section>
			<ul>
				<li class="mb-6 grid grid-cols-5 p-6">
					<h3 class="font-bold">Streamer</h3>
					<h3 class="font-bold">Tournament</h3>
					<h3 class="font-bold">Registered</h3>
					<h3 class="font-bold">Date</h3>
					<h3 class="text-right font-bold">Requirements</h3>
				</li>
				<li v-for="tournament in tournaments" :key="tournament.id">
					<router-link
						:to="`/tournament/${tournament.id}`"
						class="first-of-type:border-t-1 grid grid-cols-5 border border-zinc-800 bg-black/80 p-6 transition-all duration-300 hover:bg-white hover:text-black hover:shadow"
					>
						<p class="font-bold">{{ tournament.streamer }}</p>
						<p>{{ tournament.title }}</p>
						<p>{{ tournament.registered }}/{{ tournament.teams * 5 }}</p>
						<p>{{ tournament.live_at }}</p>
						<div class="flex items-center justify-end gap-4">
							<div class="rounded border border-zinc-800 p-2">
								<CameraIcon v-if="tournament.follower" class="h-4 w-4 text-purple-600" />
							</div>
							<div class="rounded border border-zinc-800 p-2">
								<AcademicCapIcon v-if="tournament.subscriber" class="h-4 w-4 text-yellow-600" />
							</div>
						</div>
					</router-link>
				</li>
			</ul>
		</section>
	</main>
</template>
