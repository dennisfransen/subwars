<script setup lang="ts">
import { useRoute } from 'vue-router'

const route = useRoute()
const isSettingsLayout = computed(() => route.name?.toString().includes('settings'))

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
	<main class="container mx-auto p-4">
		<router-view />

		<router-view name="tournament-base" v-slot="{ Component }">
			<div class="grid grid-cols-12 gap-10">
				<div class="col-span-9">
					<component :is="Component" />
				</div>
				<div class="col-span-3">
					<TournamentActions v-if="!isSettingsLayout" />
				</div>
			</div>
		</router-view>

		<router-view>
			<div class="mb-4 space-x-6" v-if="isSettingsLayout">
				<router-link :to="{ name: 'tournament-settings-general', params: { id: tournament.id } }">General</router-link>
				<router-link :to="{ name: 'tournament-settings-teams', params: { id: tournament.id } }">Teams</router-link>
				<router-link :to="{ name: 'tournament-settings-prices', params: { id: tournament.id } }">Prices</router-link>
				<router-link :to="{ name: 'tournament-settings-casters', params: { id: tournament.id } }">Casters</router-link>
				<router-link :to="{ name: 'tournament-settings-sponsors', params: { id: tournament.id } }"
					>Sponsors</router-link
				>
			</div>

			<router-view name="tournament-settings" />
		</router-view>
	</main>
</template>
