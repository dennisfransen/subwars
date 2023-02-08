<script setup lang="ts">
import { ClockIcon, UserIcon, ChevronRightIcon, SignalIcon } from '@heroicons/vue/24/outline'
import { PlayIcon } from '@heroicons/vue/24/solid'

const tournaments = ref([
	{
		id: 1,
		title: 'Fredagsrushen',
		streamer: 'PG_CS',
		registered: 10,
		teams: 8,
		follower: true,
		subscriber: true,
		live_at: '2023-04-01 10:00',
		is_live: true,
	},
	{
		id: 2,
		title: 'Lördagsrushen',
		streamer: 'Tjenabrysh',
		registered: 40,
		teams: 8,
		follower: true,
		subscriber: true,
		live_at: '2023-04-02 21:00',
		is_live: false,
	},
])
</script>

<template>
	<main class="container mx-auto px-4 py-10">
		<section class="rounded-sm bg-black/60 p-6">
			<table class="w-full text-zinc-500">
				<thead>
					<tr>
						<th class="border-b border-b-zinc-800 p-4 text-left font-normal">#</th>
						<th class="border-b border-b-zinc-800 p-4 text-left font-normal">Streamer</th>
						<th class="border-b border-b-zinc-800 p-4 text-left font-normal">Name</th>
						<th class="border-b border-b-zinc-800 p-4 text-left font-normal">Time</th>
						<th class="border-b border-b-zinc-800 p-4 text-left font-normal">Players</th>
						<th class="border-b border-b-zinc-800 p-4 text-left font-normal">Action</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(tournament, i) in tournaments" :key="tournament.id">
						<td>
							<p class="px-4 py-5 text-white">
								{{ i + 1 }}
							</p>
						</td>
						<td>
							<a
								:href="`https://twitch.tv/${tournament.streamer}`"
								target="_blank"
								title="Open Twitch profile in new tab"
								class="flex cursor-pointer items-center gap-2 px-4 py-5 font-bold text-white"
							>
								<svg
									xmlns="http://www.w3.org/2000/svg"
									width="16"
									height="16"
									fill="currentColor"
									class="text-purple-500"
									viewBox="0 0 16 16"
								>
									<path
										d="M3.857 0 1 2.857v10.286h3.429V16l2.857-2.857H9.57L14.714 8V0H3.857zm9.714 7.429-2.285 2.285H9l-2 2v-2H4.429V1.143h9.142v6.286z"
									/>
									<path d="M11.857 3.143h-1.143V6.57h1.143V3.143zm-3.143 0H7.571V6.57h1.143V3.143z" />
								</svg>
								{{ tournament.streamer }}
							</a>
						</td>
						<td>
							<p class="px-4 py-5">
								{{ tournament.title }}
							</p>
						</td>
						<td>
							<p class="flex items-center gap-2 px-4 py-5">
								<ClockIcon class="h-4 w-4" />
								{{ tournament.live_at }}
							</p>
						</td>
						<td>
							<p class="flex items-center gap-2 px-4 py-5">
								<UserIcon class="h-4 w-4" />
								{{ tournament.registered }}/{{ tournament.teams * 5 }}
							</p>
						</td>
						<td v-if="tournament.is_live">
							<div>
								<router-link
									:to="{ name: 'tournament-general', params: { id: tournament.id } }"
									class="flex items-center gap-2 px-4 py-5 font-bold text-white"
								>
									<span
										class="relative rounded-full bg-purple-500/50 p-2 after:absolute after:left-1/2 after:top-1/2 after:-translate-y-1/2 after:-translate-x-1/2 after:rounded-full after:bg-purple-500 after:p-1"
									></span>
									Playing
								</router-link>
							</div>
						</td>
						<td v-else>
							<div>
								<router-link
									:to="{ name: 'tournament-general', params: { id: tournament.id } }"
									class="flex items-center gap-2 px-4 py-5 font-bold text-white"
								>
									Register
									<PlayIcon class="h-3 w-3 text-white" />
								</router-link>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</section>
	</main>
</template>
