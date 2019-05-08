<template>
    <div class="card-deck mb-3 text-center">
        <div class="card mb-4 box-shadow" v-for="app in apps">
          <div class="card-header">
            <h4 class="my-0 font-weight-normal">{{app.name}}</h4>
          </div>
          <div class="card-body">
            <div class="card-text">
                {{ app.description }}
            </div>
          </div>
          <div class="card-footer">
            <router-link :to="app.url">Go to App</router-link>
          </div>
        </div>
    </div>
</template>
<script>
    export default {
        mounted() {
            console.log('Updated : Component mounted.')
        },
        data() {
            return {
                apps: [
                    {
                        "name": "To Do Application",
                        "description": "Add task lists, mark complete, delete, filter",
                        "url": "/todo"
                    },
                    {
                        "name": "GitHub Commits Example",
                        "description": "This example fetches latest Vue.js commits data from GitHub’s API and displays them as a list. You can switch between the master and dev branches.",
                        "url": "/github"
                    },
                    {
                        "name": "Form Example",
                        "description": "Create form, add validation, submit online.",
                        "url": "/form"
                    },
                    {
                        "name": "Custom Input Example",
                        "description": "Custom input field example",
                        "url": "/input"
                    },
                    {
                        "name": "Vue Slots",
                        "description": "VueJS slots example",
                        "url": "/slots"
                    }
                ]
            }
        }    
    }
</script>