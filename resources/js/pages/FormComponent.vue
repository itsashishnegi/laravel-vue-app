<template>
	<div class="row justify-content-center">
	    <div class="col-md-8">
	        <div class="card">
	            <div class="card-header">Form Application</div>

	            <div class="card-body">
	            	<form @submit.prevent="submitForm">
		                <div class="form-group">
		                    <label>Full Name</label>
		                    <input class="form-control" @keydown="form.errors.removeError('name')" v-model="form.data.name" placeholder="Name">
		                    <p class="error" :show="form.errors.getError('name')">{{ form.errors.getError('name') }}</p>
		                </div>
		                <div class="form-group">
		                    <label>Email</label>
		                    <input class="form-control" @keydown="form.errors.removeError('email')" v-model="form.data.email" placeholder="Email">
		                    <p class="error" :show="form.errors.getError('email')">{{ form.errors.getError('email') }}</p>
		                </div>
		                <div class="form-group">
		                    <label>Password</label>
		                    <input type="password" class="form-control" @keydown="form.errors.removeError('password')" v-model="form.data.password" placeholder="Password">
		                    <p class="error" :show="form.errors.getError('password')">{{ form.errors.getError('password') }}</p>
		                </div>
		                <div class="form-group">
		                    <label>Confirm Password</label>
		                    <input type="password" class="form-control" @keydown="form.errors.removeError('password_confirmation')" v-model="form.data.password_confirmation" placeholder="Confirm Password">
		                    <p class="error" :show="form.errors.getError('password_confirmation')">{{ form.errors.getError('password_confirmation') }}</p>
		                </div>
		                <div class="form-group">
		                    <label>Phone Number</label>
		                    <input class="form-control" @keydown="form.errors.removeError('phone_number')" v-model="form.data.phone_number" placeholder="Phone Number">
		                    <p class="error" :show="form.errors.getError('phone_number')">{{ form.errors.getError('phone_number') }}</p>
		                </div>
		                <div class="form-group">
		                    <label>Verification Method</label>
		                    <select class="form-control" v-model="form.data.verification_method" @change="form.errors.removeError('verification_method')">
							  <option value="sms">SMS</option>
							  <option value="email">Email</option>
							</select>
		                    <p class="error" :show="form.errors.getError('verification_method')">{{ form.errors.getError('verification_method') }}</p>
		                </div>
		                <div class="form-group">
		                    <label>Device</label>
		                    <select class="form-control" v-model="form.data.device_type" @change="form.errors.removeError('device_type')">
							  <option value="ios">iOS</option>
							  <option value="android">Android</option>
							</select>
		                    <p class="error" :show="form.errors.getError('device_type')">{{ form.errors.getError('device_type') }}</p>
		                </div>
		                <div class="form-group">
		                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
		                </div>
	                </form>
	            </div>
	        </div>
	    </div>
	</div>
</template>
<script>
	class Error {
		constructor() {
			this.data = [];
		}

		getError(field) {
			if (this.data[field]) {
	            return this.data[field][0];
	        }
		}

		removeError(field) {
			if (this.data[field]) {
	            delete this.data[field][0];
	        }
		}
	}

	class Form {
		constructor(data) {
			this.data = data;
			this.errors = new Error();
		}

		submit(method, url) {
			axios[method](url, this.data)
			.then(response => {
				this.data = [];
				this.errors.data = [];
			})
			.catch(error => {
				this.errors.data = error.response.data.errors;
			});
		}
	}

	export default {
		data() {
			return {
				form: new Form({
					name: null,
					email: null,
					password: null,
					password_confirmation: null,
					phone_number: null,
					verification_method: null,
					device_type: null
				})
			}
		},
		methods: {
			submitForm: function() {
				this.form.submit('post', '/api/register');
			}
		}
	}
</script>