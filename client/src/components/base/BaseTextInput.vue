<script setup lang='ts'>
defineProps({
	label: String,
	modelValue: String
})
</script>

<template>
	<div>
		<label :for="$attrs.id">{{ label }}</label>
		<input type="text" :name="$attrs.id" :id="$attrs.id" @input="(e) => $emit('update:modelValue', e.target.value)" v-bind="$attrs">
	</div>
</template>
