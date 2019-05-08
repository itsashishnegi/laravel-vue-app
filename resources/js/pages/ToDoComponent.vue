<template>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">To Do Application</div>

                    <div class="card-body">
                        <div class="form-group">
                            <input class="form-control" v-model="task" placeholder="Task Name">
                            <p :show="error" class="error">{{ error }}</p>
                        </div>
                        <div class="form-group">
                            <button @click="addToTaskList" class="btn btn-primary btn-sm">Add Task</button>
                        </div>
                        <ul class="list-group">
                            <li v-for="(task, index) in filteredTasks" class="list-group-item" :class="{'list-group-item-success': task.is_completed}">
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" @click="taskStateChange($event, index)" :checked="task.is_completed">
                                  <label class="form-check-label">
                                    {{task.name}}
                                  </label>
                                </div> 
                                <button type="button" class="close" aria-label="Close" @click="removeTaskFromTaskList(index)">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body text-center">
                        <span>Total: {{filteredLength(null)}}</span>&nbsp;|&nbsp;<span>Pending: {{filteredLength(false)}}</span>&nbsp;|&nbsp;<span>Completed: {{filteredLength(true)}}</span>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-sm btn-outline-dark" @click="showFilteredTasks(null)" :class="applyActiveClass(null)">All Tasks</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="showFilteredTasks(false)" :class="applyActiveClass(false)">Pending Tasks</button>
                        <button type="button" class="btn btn-sm btn-outline-success" @click="showFilteredTasks(true)" :class="applyActiveClass(true)">Completed Tasks</button>
                    </div>
                </div>
            </div>
        </div>
</template>
<style type="text/css">
    .error{
        color: red;
    }
</style>
<script>
    export default {
        mounted() {
            console.log('Updated : Component mounted.')
        },
        data() {
            return {
                tasks: [],
                task: '',
                error: '',
                flag: null
            }
        },
        computed: {
            filteredTasks: function() {
                if (this.flag === null) return this.tasks

                return this.tasks.filter(function (todo) {
                    return todo.is_completed === this.flag
                }, this)
            }
        },
        methods: {
            addToTaskList: function() {
                if (this.task == '') {
                    this.error = "Task name is required"
                    return
                }
                this.tasks.push({"name": this.task, "is_completed": false})
                this.task = ''
                this.error = ''
            },
            removeTaskFromTaskList: function(index) {
                this.tasks.splice(index, 1)
            },
            taskStateChange: function($event, index) {
                if ($event.target.checked) {
                    this.tasks[index].is_completed = true
                } else {
                    this.tasks[index].is_completed = false
                }
            },
            showFilteredTasks: function(flag) {
                this.flag = flag
            },
            applyActiveClass: function(flag) {
                if (this.flag === flag) return 'active'
            },
            filteredLength: function(flag) {
                if (flag === null) return this.tasks.length

                return this.tasks.filter(function (todo) {
                    return todo.is_completed === flag
                }, flag).length
            }
        }
    }
</script>
