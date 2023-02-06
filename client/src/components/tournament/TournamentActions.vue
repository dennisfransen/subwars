<script setup lang="ts">
defineProps({
	tournament: {
		type: Object,
		required: true,
	},
})
</script>

<template>
	<div class="flex flex-col gap-y-5 bg-black/60 py-4 px-6">
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
	</div>
</template>
