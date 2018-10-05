<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h1>Dashboard</h1>

                <p>
                    <router-link :to="{ name: 'dashboard' }">Dashboard</router-link> |
                    <router-link :to="{ name: 'login' }">Login</router-link> |
                    <router-link :to="{ name: 'logout' }">Logout</router-link>
                </p>

                <div class="panel panel-default">
                    <div class="panel-heading">Dashboard</div>
                    <div class="panel-body">
                        <p v-for="task in tasks" :key="task.id">Data: {{ task.user_id }} {{task.task}}</p>
                        <div class="row">
        <div class="col-md-12">
            <h4>Chart: <span style="color:blue"></span></h4>
            <line-chart :chart-data="datacollection" :options="options" :width="1200" :height="500"></line-chart>
        </div>
    </div>


    <figure class="highlight">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <form @submit.prevent="createNewTask">
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control input-lg" placeholder="(min 5 characters)" v-model="newtask" required>
                        <span class="input-group-btn">
                        <button type="submit" class="btn btn-default btn-lg">+ New Task</button>
                    </span>
                    </div>
                </form>
            </div>
        </div>
    </figure>

    <div class="row">
        <div class="col-md-6">
            <h5 class="text-center">Pending Tasks <span class="label label-warning">{{ pendingTasks.length }}</span></h5>
            <div class="list-group">
                <a class="list-group-item" v-for="todo in pendingTasks" @click="toggleTaskStatus(todo)">{{ todo.task }} <span class="glyphicon glyphicon-ok" aria-hidden="true"></span></a>
            </div>
        </div>

   
        <div class="col-md-6">
            <h5 class="text-center">Completed Tasks <span class="label label-success">{{ completedTasks.length }}</span></h5>
            <div class="list-group">
                <a class="list-group-item" v-for="todo in completedTasks" @click="toggleTaskStatus(todo)">{{ todo.task }} <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></a>
            </div>
        </div>
    </div>
</div>
                    </div>
                </div>

            </div>
        </div>
    
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import LineChart from './LineChart';
    export default {
        components:{LineChart},
        data() {
            return {
            data: 'nothing',
            tasks: [],
            newtask: "",
            options: {},
            activities: [],
            datacollection: null,
            labels: [],
            pending_tasks_count: []
                
            }
        },
        methods: {
        /**
         * To setup chart options
         */
        setupChartOptions() {
            this.options = {
                responsive: false,
                maintainAspectRatio: false
            };
        },
        /**
         * To fetch all tasks associated with the user
         */
        fetchAllTasks() {
            axios.get('/tasks')
                .then((response) => {
                    this.tasks = response.data;
                    console.log('getall');
                })
                .catch(function (error) {
                    console.error(error);
                });
        },
        /**
         * Helper function to generate the request data
         * @param taskItem
         * @returns {{item: *, statuses: {pending: Number, completed: Number}}}
         */
        generateRequestData(taskItem) {
            if (typeof(taskItem) !== 'object') {
                let taskObject = {
                    task: taskItem,
                    status: false
                };
                taskItem = taskObject;
            }
            var data = {
                item: taskItem,
                statuses: {
                    pending: this.pendingTasks.length,
                    completed: this.completedTasks.length
                }
            };
            return data;
        },
        /**
         * To create new task
         */
        createNewTask() {
            axios.post('/tasks', this.generateRequestData(this.newtask))
                .then((response) => {
                    this.tasks.push(response.data.task_item);
                    this.newtask = '';
                    console.log('store');
                    this.fillData({
                        label: moment(response.data.user_activity.created_at).format("HH:mm:ss"),
                        data: response.data.user_activity.pending_tasks
                    });
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        /**
         * To toggle the status of the task
         * @param taskItem
         */
        toggleTaskStatus(taskItem) {
            axios.put('/tasks/'+ taskItem.id +'/toggle', this.generateRequestData(taskItem))
                .then((response) => {
                    taskItem.status = response.data.task_item.status;
                    this.fillData({
                        label: moment(response.data.user_activity.created_at).format("HH:mm:ss"),
                        data: response.data.user_activity.pending_tasks
                    });
                })
                .catch((error) => {
                    this.tasks.forEach((todo) => {
                        if (todo.id === taskItem.id) {
                            todo.status = ! taskItem.status;
                        }
                    });
                    console.error('Logging the error', error);
                });
        },
        /**
         * To fetch last house activities of the user
         */
        fetchActivitiesForLast60Minutes() {
            axios.get('/activities/last60minutes')
                .then((response) => {
                    var activities = response.data;
                    console.info('Activities', response.data);
                    activities.forEach((activity) => {
                        this.labels.push(moment(activity.created_at).format("HH:mm:ss"));
                        this.pending_tasks_count.push(activity.pending_tasks);
                    }, this.labels, this.pending_tasks_count);
                    console.log('Activity Data: ', this.pending_tasks_count);
                    this.fillData();
                })
                .catch(function (error) {
                    console.error('Fetch Activities: ', error);
                });
        },
        /**
         * To fill data into the chart
         * @param newData
         */
        fillData (newData = {}) {
            if (! _.isEmpty(newData)) {
                this.labels.push(newData.label);
                this.pending_tasks_count.push(newData.data);
                // console.debug(this.$refs._chart);
            }
            this.datacollection = {
                labels: this.labels,
                datasets: [
                    {
                        label: "Pending Tasks",
                        pointRadius: 10,
                        backgroundColor: '#c7ecea',
                        borderColor: '#5E9732',
                        strokeColor: "rgba(151,187,205,1)",
                        pointColor: "#226D82",
                        pointBorderWidth: 2,
                        pointBorderColor: "#5E9732",
                        data: this.pending_tasks_count
                    }
                ]
            };
        }
    },
        computed: {
        pendingTasks() {
            return this.tasks.filter(todo => ! todo.status);
        },
        completedTasks() {
            return this.tasks.filter(todo => todo.status);
        }
    },
        mounted() {
            
        this.setupChartOptions();
        this.fetchAllTasks();
        this.fetchActivitiesForLast60Minutes();
        this.fillData();
        }
    }
</script>
