<template>
    <div>
        <section class="container mx-auto p-6 font-mono">
            <div class="w-full rounded-lg shadow-lg">
                <table class="w-full">
                    <tbody class="bg-white">
                    <tr class="text-gray-700">
                        <td class="px-4 py-3 border w-4/5">
                            <el-form :model="form" ref="newTaskForm" class="mt-5">
                                <el-form-item
                                    prop="new_task"
                                    :rules="[{ required: true, message: 'Please give the task a name', trigger: 'submit'}]"
                                >
                                    <el-input v-model="form.new_task" placeholder="Type a new task..."></el-input>
                                </el-form-item>
                            </el-form>
                        </td>
                        <td class="px-4 py-3 text-ms font-semibold border w-1/5">
                            <el-row>
                                <el-button v-on:click="addTask()" type="success" icon="el-icon-plus" circle></el-button>
                            </el-row>
                        </td>
                    </tr>
                    <tr class="text-gray-700" v-for="task in tasks" :key="task.id">
                        <td class="px-4 py-3 border w-4/5">

                            <input v-if="task.editing" v-model="task.new_name" class="w-full border shadow-md">
                            <span v-else v-bind:class="{ 'line-through': task.done }">{{ task.name }}</span>
                        </td>
                        <td class="px-4 py-3 text-ms font-semibold border w-1/5">
                            <el-row v-if="task.editing">
                                <el-button v-on:click="updateTask(task)" type="success" icon="el-icon-upload"
                                           circle></el-button>
                                <el-button v-on:click="cancelEdit(task)" type="info" icon="el-icon-close"
                                           circle></el-button>
                            </el-row>
                            <el-row v-else>
                                <el-button v-on:click="toggleTaskDone(task)" :type="task.done ? 'info' : 'success'"
                                           icon="el-icon-check" circle></el-button>
                                <el-button v-on:click="task.editing = true" type="primary" icon="el-icon-edit"
                                           circle></el-button>
                                <el-button v-on:click="deleteTask(task)" type="danger" icon="el-icon-delete"
                                           circle></el-button>
                            </el-row>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>

<script>
import ElButton from 'element-ui/packages/button';

export default {
    name: "todo",
    data() {
        return {
            tasks: [],
            form: {
                new_task: ''
            }
        }

    },
    mounted() {
        this.getTasks();
    },
    methods: {

        getTasks() {
            this.axios.get('/api/tasks').then(response => {
                this.tasks = response.data;
            }).catch(error => {
                console.log(error)
                this.tasks = [];
            })
        },

        deleteTask(task) {
            if (confirm("Are you sure you want to delete this task?")) {
                this.axios.delete(`/api/tasks/${task.id}`).then(response => {
                    this.$message({
                        message: 'The task has been deleted.',
                        type: 'success'
                    });
                    this.getTasks();
                }).catch(error => {
                    this.$message({
                        message: 'The task could not be deleted.',
                        type: 'error'
                    });
                    console.log(error);
                })
            }
        },

        addTask() {

            //validate
            this.$refs.newTaskForm.validate((valid) => {
                if (valid) {

                    this.axios
                        .post('/api/tasks', {name: this.form.new_task})
                        .then(response => {
                            this.$message({
                                message: 'The task has been added.',
                                type: 'success'
                            });

                            this.getTasks();
                            this.form.new_task = '';
                            this.$refs.newTaskForm.clearValidate('new_task');
                        }).catch(error => {
                        this.$message({
                            message: 'The task could not be added.',
                            type: 'error'
                        });
                        console.log(error);
                    })

                } else {
                    return false;
                }
            });
        },

        toggleTaskDone(task) {
            task.done = !task.done;
            this.updateTask(task);
        },

        updateTask(task) {

            // update the task name
            task.name = task.new_name;

            this.axios
                .patch(`/api/tasks/${task.id}`, task)
                .then((response) => {
                    this.getTasks();
                });
        },

        cancelEdit(task) {
            task.editing = false;
            task.new_name = task.name;
        }
    },
}
</script>
