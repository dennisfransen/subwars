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
</script>

<template>
	<div class="sticky right-0 top-8">
		<div class="flex flex-col gap-y-5 rounded-sm bg-black/60 p-6">
			<div class="flex items-center justify-between rounded-sm border-b border-b-zinc-800 py-4 font-graduate">
				<p class="uppercase tracking-wide text-zinc-500">Registered</p>
				<p class="text-4xl font-black">{{ tournament.registered }}</p>
			</div>

			<div class="flex items-center justify-between border-b border-b-zinc-800 py-4 font-graduate">
				<p class="uppercase tracking-wide text-zinc-500">Checked In</p>
				<p class="text-4xl font-black">{{ tournament.checkedIn }}</p>
			</div>

			<div class="flex items-center justify-between border-b border-b-zinc-800 py-4 font-graduate">
				<p class="uppercase tracking-wide text-zinc-500">Player slots</p>
				<p class="text-4xl font-black">{{ tournament.teams * 5 }}</p>
			</div>
		</div>

		<div class="mt-16 flex flex-col gap-y-5">
			<button
				type="button"
				class="rounded-sm border border-zinc-600 py-4 text-sm font-semibold uppercase transition-all duration-300 hover:bg-white hover:text-black hover:shadow"
				title="Register to tournament"
			>
				Register to subwar
			</button>

			<button
				type="button"
				class="rounded-sm border border-zinc-600 py-4 text-sm font-semibold uppercase transition-all duration-300 hover:bg-white hover:text-black hover:shadow"
				title="Check in"
			>
				Ready up!
			</button>

			<button
				type="button"
				class="rounded-sm border border-zinc-600 py-4 text-sm font-semibold uppercase transition-all duration-300 hover:bg-white hover:text-black hover:shadow"
				title="Leave tournament"
			>
				Abandon troops
			</button>
		</div>
	</div>
</template>
