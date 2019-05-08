<template>
	<div class="row justify-content-center">
	    <div class="col-md-8">
	        <div class="card">
	            <div class="card-header">Custom Input Application</div>

	            <div class="card-body">
	            	<div class="form-group">
	                    <label>Coupon code</label>
	                    <input class="form-control" placeholder="Enter coupon code" :value="coupon" @input="validateCoupon($event.target.value)">
	                    <p class="error" :show="error">{{ error }}</p>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
</template>
<script>

	export default {
		data() {
			return {
				error: null,
				coupon: null
			}
		},
		methods: {
			setError: function(error) {
				if (error) {
					this.error = error
					//this.coupon = null
				}
				else
					this.error = null
			},
			validateCoupon: function(newValue) {
				this.coupon = newValue
				if (newValue) {
					this.setError()
					const regex = /^(([A-Z]{3})+([0-9]{3}))$/;
					if (!regex.test(newValue))
						this.setError("Please enter a valid coupon code.")
				} else {
					this.setError("Please enter a coupon code.")
				}
			}
		}
	}
</script>