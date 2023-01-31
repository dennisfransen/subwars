<template>
	<AppWebHeader />
	<AppMobileHeader />
	<div class="min-h-app">
		<router-view v-slot="{ Component, route }">
			<transition name="fade" mode="out-in">
				<component :is="Component" :key="route.path" />
			</transition>
		</router-view>
	</div>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
	transition: opacity 150ms ease-in-out, transform 150ms ease-in-out;
}

.fade-enter-from,
.fade-leave-to {
	opacity: 0;
	@apply translate-y-4;
}

.fade-enter-to,
.fade-leave-from {
	opacity: 1;
	@apply translate-y-0;
}
</style>
