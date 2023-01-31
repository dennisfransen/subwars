<template>
	<main class="grid lg:grid-cols-3">
		<div class="img-container group/img">
			<div class="img bg-anubis"></div>
			<div class="img-inner">
				<p>hello world</p>
			</div>
		</div>
		<div class="img-container group/img">
			<div class="img bg-inferno"></div>
			<div class="img-inner">
				<p>hello world</p>
			</div>
		</div>
		<div class="img-container group/img">
			<div class="img bg-mirage"></div>
			<div class="img-inner">
				<p>hello world</p>
			</div>
		</div>
	</main>
</template>

<style>
.img-container {
	@apply relative h-full w-full min-h-app overflow-hidden;
}

.img-inner {
	@apply absolute inset-0 z-20;
}

.img {
	@apply absolute inset-0 overflow-hidden;
	@apply transition-all duration-700;
	@apply bg-cover bg-center bg-no-repeat brightness-50;
	@apply group-hover/img:blur-none blur-md;
	@apply group-hover/img:scale-100 scale-110;
	@apply group-hover/img:grayscale-0 grayscale;
}
</style>
