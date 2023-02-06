<script setup lang="ts">
defineProps({
	tournament: {
		type: Object,
		required: true,
	},
})
</script>

<template>
	<div class="sticky right-0 top-8">
		<div class="flex flex-col gap-y-5 bg-black/60 p-6">
			<div class="flex items-center justify-between border-b border-b-zinc-800 py-4 font-graduate">
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

		<div class="mt-5 flex flex-col gap-y-5 bg-black/60 p-6">
			<button
				type="button"
				class="border border-zinc-600 py-4 text-sm font-semibold uppercase transition-all duration-300 hover:bg-white hover:text-black hover:shadow"
				title="Register to tournament"
			>
				Enlist to subwar
			</button>

			<button
				type="button"
				class="border border-zinc-600 py-4 text-sm font-semibold uppercase transition-all duration-300 hover:bg-white hover:text-black hover:shadow"
				title="Check in"
			>
				Go to subwar
			</button>

			<button
				type="button"
				class="border border-zinc-600 py-4 text-sm font-semibold uppercase transition-all duration-300 hover:bg-white hover:text-black hover:shadow"
				title="Leave tournament"
			>
				Abandon troops
			</button>
		</div>
	</div>
</template>
