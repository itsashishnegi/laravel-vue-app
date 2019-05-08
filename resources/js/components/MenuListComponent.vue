<template>
	<ul>
		<li v-for="item in items">
			<slot :item="item">{{ item }}</slot>
		</li>
   	</ul>
</template>
<script>

	export default {
		props: [
			'items'
		]
	}
</script>