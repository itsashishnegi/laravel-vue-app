<template>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Vue GitHub Commits</div>

                <div class="card-body">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="master" v-model="sha">
                      <label class="form-check-label" for="inlineRadio1">master</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="dev" v-model="sha">
                      <label class="form-check-label" for="inlineRadio2">dev</label>
                    </div>
                    <ul class="list-group">
                        <li v-for="record in records" class="list-group-item">
                            <a :href="record.html_url" target="_blank" class="commit">{{ record.sha.slice(0, 7) }}</a>
                              - <span class="message">{{ record.commit.message }}</span><br>
                              by <span class="author"><a :href="record.author.html_url" target="_blank">{{ record.commit.author.name }}</a></span>
                              at <span class="date">{{ record.commit.author.date }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        mounted() {
            console.log('Updated : Component mounted.')
        },
        created: function() {
            this.fetchData();
        },
        watch: {
            sha: 'fetchData'
        },
        methods: {
            fetchData: function() {
                axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
                axios.defaults.headers.post['crossDomain'] = true;
                // axios.defaults.headers.common['Access-Control-Request-Headers'] = null
                // axios.defaults.headers.common['Access-Control-Request-Method'] = null
                axios
                  .get('https://api.github.com/repos/vuejs/vue/commits', {
                    params: {
                      per_page: 3,
                      sha: this.sha
                    }
                  })
                  .then(response => (this.records = response.data))
            }
        },
        data() {
            return {
                records: [],
                sha: 'master'
            }
        }    
    }
</script>