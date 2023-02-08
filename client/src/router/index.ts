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
			name: 'tournament',
			component: () => import('@/views/TournamentView.vue'),
			children: [
				{
					path: 'stream',
					name: 'tournament-stream',
					component: () => import('@/views/TournamentStreamView.vue'),
				},
				{
					path: 'teams',
					name: 'tournament-teams',
					component: () => import('@/views/TournamentTeamsView.vue'),
				},
				{
					path: 'prices',
					name: 'tournament-prices',
					component: () => import('@/views/TournamentPricesView.vue'),
				},
				{
					path: 'rules',
					name: 'tournament-rules',
					component: () => import('@/views/TournamentRulesView.vue'),
				},
				{
					path: 'settings',
					name: 'tournament-settings',
					component: () => import('@/views/TournamentSettingsView.vue'),
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
