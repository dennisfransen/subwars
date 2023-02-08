import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes: [
		{
			path: '/',
			name: 'home',
			component: () => import('@/views/HomeView.vue'),
		},
		{
			path: '/about',
			name: 'about',
			component: () => import('@/views/AboutView.vue'),
		},
		{
			path: '/login',
			name: 'login',
			component: () => import('@/views/LoginView.vue'),
		},
		{
			path: '/register',
			name: 'register',
			component: () => import('@/views/RegisterView.vue'),
		},
		{
			path: '/streamer',
			name: 'streamer',
			component: () => import('@/views/StreamerView.vue'),
		},
		{
			path: '/tournaments',
			name: 'tournaments',
			component: () => import('@/views/TournamentsView.vue'),
		},
		{
			path: '/tournament/:id',
			component: () => import('@/components/layouts/TournamentLayout.vue'),
			children: [
				{
					path: 'stream',
					name: 'tournament-stream',
					components: {
						default: () => import('@/components/tournament/TournamentNavigation.vue'),
						'tournament-base': () => import('@/views/tournament-base/TournamentStreamView.vue'),
					},
				},
				{
					path: 'teams',
					name: 'tournament-teams',
					components: {
						default: () => import('@/components/tournament/TournamentNavigation.vue'),
						'tournament-base': () => import('@/views/tournament-base/TournamentTeamsView.vue'),
					},
				},
				{
					path: 'prices',
					name: 'tournament-prices',
					components: {
						default: () => import('@/components/tournament/TournamentNavigation.vue'),
						'tournament-base': () => import('@/views/tournament-base/TournamentPricesView.vue'),
					},
				},
				{
					path: 'rules',
					name: 'tournament-rules',
					components: {
						default: () => import('@/components/tournament/TournamentNavigation.vue'),
						'tournament-base': () => import('@/views/tournament-base/TournamentRulesView.vue'),
					},
				},
				{
					path: 'settings',
					component: () => import('@/components/tournament/TournamentNavigation.vue'),
					children: [
						{
							path: 'general',
							name: 'tournament-settings-general',
							components: {
								default: () => import('@/components/tournament/TournamentNavigation.vue'),
								'tournament-settings': () => import('@/views/tournament-settings/TournamentSettingsGeneralView.vue'),
							},
						},
						{
							path: 'teams',
							name: 'tournament-settings-teams',
							components: {
								default: () => import('@/components/tournament/TournamentNavigation.vue'),
								'tournament-settings': () => import('@/views/tournament-settings/TournamentSettingsTeamsView.vue'),
							},
						},
						{
							path: 'prices',
							name: 'tournament-settings-prices',
							components: {
								default: () => import('@/components/tournament/TournamentNavigation.vue'),
								'tournament-settings': () => import('@/views/tournament-settings/TournamentSettingsPricesView.vue'),
							},
						},
						{
							path: 'casters',
							name: 'tournament-settings-casters',
							components: {
								default: () => import('@/components/tournament/TournamentNavigation.vue'),
								'tournament-settings': () => import('@/views/tournament-settings/TournamentSettingsCastersView.vue'),
							},
						},
						{
							path: 'sponsors',
							name: 'tournament-settings-sponsors',
							components: {
								default: () => import('@/components/tournament/TournamentNavigation.vue'),
								'tournament-settings': () => import('@/views/tournament-settings/TournamentSettingsSponsorsView.vue'),
							},
						},
					],
				},
			],
		},
		{
			path: '/:pathMatch(.*)*',
			name: 'not-found',
			component: () => import('@/views/NotFoundView.vue'),
		},
	],
	scrollBehavior() {
		return new Promise((resolve) => {
			setTimeout(() => {
				resolve({ left: 0, top: 0, behavior: 'smooth' })
			}, 50)
		})
	},
})

router.beforeEach(() => {
	useApp().closeMenu()
})

export default router
