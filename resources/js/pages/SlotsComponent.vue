<template>
	<div class="row justify-content-center">
	    <div class="col-md-8">
	        <div class="card">
	            <div class="card-header">Vue Slots Example</div>

	            <div class="card-body">
            		<menu-list :items="items">
            			<template scope="props">
            				<a href="/">{{ props.item }}</a>
            			</template>
            		</menu-list>
	            </div>
	        </div>
	    </div>
	</div>
</template>
<script>

	import MenuList from "../components/MenuListComponent.vue"

	export default {
		components: {
			MenuList
		},
		data() {
			return {
				items: [
					"Ankit",
					"Sumit",
					"Ravi",
					"Vikas",
					"Akshit",
					"Vinay"
				]
			}
		}
	}
</script>