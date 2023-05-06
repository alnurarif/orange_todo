<script setup>
    import {onMounted, ref} from 'vue'
    import {useRouter} from 'vue-router';
    const router = useRouter();
    
    let todos = ref([]);
    let suppliers = ref([]);
    let tasks = ref([]);
    let tasks_in_bucket = ref([]);
    let form = ref({
        id : "",
        name : "",
    })
    let bucket_form = ref({
        bucket_task_name : "",
    });
    let isEditMode = ref(false);
    let isDeleteMode = ref(false);
    let singleTodo = ref({});
    let add_todo_error = ref("");
    let isEditModalShow = ref(false);
    let isDetailModalShow = ref(false);
    let isDeleteModalShow = ref(false);
    let singleTodoDetail = ref({})

    const logout = () => {
        localStorage.removeItem('token');
        localStorage.removeItem('user_info');
        router.push('/');
    }

    
    const addEditModalShow = () => {
        form.value.name = "";
        tasks_in_bucket.value = [];

        isEditMode.value = false;
        isEditModalShow.value = !isEditModalShow.value;
    }
    const showDetailModal = () => {
        isDetailModalShow.value = !isDetailModalShow.value;
    }
    const showDeleteModal = () => {
        isDeleteModalShow.value = !isDeleteModalShow.value;
    }
    const showEditTodo = (id) => {
        isEditModalShow.value = !isEditModalShow.value;
        isEditMode.value = true;
        let single_todo = todos.value.find(todo => todo.id == id);
        form.value = {
            id : single_todo.id,
            name : single_todo.name,
        }
        tasks_in_bucket.value = single_todo.tasks;
        singleTodoDetail.value = single_todo;
    }
    const closeAddEditModal = () => {
        form.value = {
            id : 0,
            name : '',
            description : '',
            status : 'active'
        };
        isEditModalShow.value = false;
        isEditMode.value = false;
    }
    onMounted(async () => {
        getTodos();
        // getSuppliers();
        // getItems();
    })

    const add_todo = () => {
        add_todo_error.value = "";
        if(form.value.name.length == ""){
            add_todo_error.value = "Todo Name is required!";
            return false;
        }
        if(tasks_in_bucket.value.length == 0){
            add_todo_error.value = "There is no task!";
            return false;
        }
        

        if(isEditMode.value){
            var data = {
                'name': form.value.name,
                'task_name' : [],
                'task_completed' : []
            };
            singleTodoDetail.value.tasks.map((task) => {
                data.task_name.push(task.name);
                data.task_completed.push(task.completed);
            });
        }else{
            var data = new FormData();
            data.append('name', form.value.name);
            tasks_in_bucket.value.map((task) => {
                data.append('task_name[]', task.name);
            });

        }
        var config = {
            method: (isEditMode.value) ? 'put' : 'post',
            url: (isEditMode.value) ? `/api/todos/${form.value.id}` : '/api/todos',
            headers: (isEditMode.value) ? { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Content-Type': 'application/x-www-form-urlencoded'
            } : { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            data : data
        };

        axios(config)
        .then(function (response) {
            form.value.name = "";
            tasks_in_bucket.value = [];
            add_todo_error.value = "";
            isEditModalShow.value = !isEditModalShow.value;
            getTodos();
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    const add_tasks_in_bucket = () => {
        let found_task = tasks.value.find(task => task.id == bucket_form.value.bucket_task_id);
        let task = {};
        task.name = bucket_form.value.name;
        tasks_in_bucket.value.push(task);

        bucket_form.value.name = ""
    }

    const remove_from_bucket = (task_index) => {
        let new_tasks_in_bucket = tasks_in_bucket.value.filter((task,index) => index != task_index);
        tasks_in_bucket.value = new_tasks_in_bucket;
        singleTodoDetail.value.tasks = new_tasks_in_bucket;
    }

    const getTodos = async () => {

        var config = {
            method: 'get',
            url: '/api/todos',
            headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        };

        axios(config)
        .then(function (response) {
            todos.value = response.data;
        })
        .catch(function (error) {
            console.log(error);
        });
    }
    const getSuppliers = async () => {

        var config = {
            method: 'get',
            url: '/api/suppliers',
            headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        };

        axios(config)
        .then(function (response) {
            suppliers.value = response.data;
        })
        .catch(function (error) {
            console.log(error);
        });
    }
    const getItems = async () => {

        var config = {
            method: 'get',
            url: '/api/items',
            headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        };

        axios(config)
        .then(function (response) {
            items.value = response.data;
        })
        .catch(function (error) {
            console.log(error);
        });
    }
    const getTodo = async (id) => {

        var config = {
            method: 'get',
            url: `/api/todos/${id}`,
            headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        };

        axios(config)
        .then(function (response) {
            singleTodo.value = response.data;
        })
        .catch(function (error) {
            console.log(error);
        });
    }
    const add_edit_todo = () => {
        add_todo_error.value = "";
        
        var data = new FormData();
        data.append('name', form.value.name);

        var config = {
            method: 'post',
            url: '/api/todos',
            headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            data : data
        };

        axios(config)
        .then(function (response) {
            form.value = {
                supplier_id : "",
                item_id : "",
                quantity : 0,
                price : 0
            };
            add_todo_error.value = "";
            isEditModalShow.value = !isEditModalShow.value;
            getTodos();
        })
        .catch(function (error) {
            console.log(error);
        });
    }
    const confirmDelete = () => {
        if(isDeleteMode.value){
            var config = {
                method: 'delete',
                url: `/api/todos/${singleTodoDetail.value.id}`,
                headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            };

            axios(config)
            .then(function (response) {
                // console.log(JSON.stringify(response.data));
                getTodos();
                deleteCancel();
            })
            .catch(function (error) {
                console.log(error);
            });
        }
    }
    
    const change_task_state = (task) => {
        singleTodoDetail.value.tasks = singleTodoDetail.value.tasks.filter((t) => {
            if(t.id === task.id){
                t.completed = (t.completed == true) ? 0 : 1;
            }
            return t;
        });
        let singleTask = singleTodoDetail.value.tasks.find((t) => t.id === task.id);

        var data = new FormData();
        data.append('id', task.id);
        data.append('completed', singleTask.completed);

        var config = {
            method: 'put',
            url: `/api/tasks/change_state/${task.id}`,
            headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            data : data
        };

        axios(config)
        .then(function (response) {
            // getTodos();
            todos.value.map((todo) => {
                if(todo.id === singleTodoDetail.value.id){
                    todo = singleTodoDetail.value.id;
                }
            });
        })
        .catch(function (error) {
            console.log(error);
            singleTodoDetail.value.tasks = singleTodoDetail.value.tasks.filter((t) => {
                if(t.id === task.id){
                    t.completed = (t.completed == true) ? 0 : 1;
                }
                return t;
            });
        });
        
    }
    const changeTaskName = (e,task) => {
        tasks_in_bucket.value = tasks_in_bucket.value.filter((t) => {
            if(t.id === task.id){
                t.name = e.target.value
            }
            return t;
        });
    }
    const showDetailTodo = (id) => {
        showDetailModal();
        singleTodoDetail.value = todos.value.find((todo) => todo.id === id);
    }
    const deleteTodo = (id) => {
        showDeleteModal();
        singleTodoDetail.value = todos.value.find((todo) => todo.id === id);
        isDeleteMode.value = true;
    }
    const deleteCancel = () => {
        singleTodoDetail.value = {};
        isDeleteMode.value = false;
        isDeleteModalShow.value = false;
    }
    const showStatus = (status) => {
        if(status == "active"){
            return "Active";
        }else if(status == "inactive"){
            return "Inactive";
        }
    }
</script>
<template>
    <header>
		<h1>Todo App</h1>
		<div id="user-dropdown" class="dropdown">
			
			<p class="cursor-pointer" @click="logout()">Logout</p>
			
		</div>
	</header>

	<div class="container">
		<div class="sidebar">
			<ul>
				<li><router-link to="/user/home">Todos</router-link></li>
			</ul>
		</div>

		<div class="content">
            <div class="main_content_header">
			    <h2>Todos</h2>
                <button class="smart-button" @click="addEditModalShow()">Add</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Name</th>
                        <th>Tasks Number</th>
                        <th>Tasks Undone</th>
                        <th>Tasks Done</th>
                        <th>Actions</th>
                        <!-- <th>Actions</th> -->
                    </tr>
                </thead>
                <tbody>

                    <tr v-for="(todo, index, key) in todos" :key="todo.id" v-if="todos.length > 0">
                        <td>{{index + 1}}</td>
                        <td>{{todo.name}}</td>
                        <td>{{todo.tasks.length}}</td>
                        <td>{{todo.tasks.filter((task) => task.completed == 0).length}}</td>
                        <td>{{todo.tasks.filter((task) => task.completed == 1).length}}</td>
                        <td>
                            <i class="fas fa-trash-alt cursor-pointer" @click="deleteTodo(todo.id)"></i>
                            <i class="fas fa-solid fa-list cursor-pointer ml_10" @click="showDetailTodo(todo.id)"></i>
                            <i class="fas fa-edit cursor-pointer ml_10" @click="showEditTodo(todo.id)"></i>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
		</div>
	</div>

    <!-- <div id="modal" class="modal" :class="[isEditModalShow ? 'show' : 'hide']">
        <div class="modal-content">
            <span id="close-modal" class="close" @click="closeAddEditModal">&times;</span>
            <h2>Add</h2>
            <p class="error_text" :class="[add_todo_error != '' ? 'show' : 'hide']">{{add_todo_error}}</p>
            <div>
                <form @submit.prevent="add_edit_todo">
                    <label for="name">Name</label>
                    <input v-model="form.name" type="text" id="name" name="name"><br>
                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div> -->


    <div id="modal" class="modal" :class="[isDetailModalShow ? 'show' : 'hide']">
        <div class="modal-content">
            <span id="close-modal" class="close" @click="isDetailModalShow = false">&times;</span>
            <h2>Todo Details</h2>
            <div>
                
                <div class="added_todos">
                    <p class="mt_10 mb_10"><span class="bold">Name :</span> {{singleTodoDetail?.name}}</p>
                    <p class="mt_10 mb_10"><span class="bold">Status :</span> {{(singleTodoDetail?.tasks?.filter((task) => task?.completed == 0).length == 0) ? 'Completed' : 'Not Completed'}}</p>

                    <table class="small_table">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Done/Undone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(task, index, key) in singleTodoDetail?.tasks" :key="key" v-if="singleTodoDetail?.tasks?.length > 0">
                                <td>{{index + 1}}</td>
                                <td>{{task.name}}</td>
                                <td>{{(task.completed) ? "Done" : "Undone"}}</td>
                                <td><input type="checkbox" :checked="task.completed" @change="change_task_state(task)"/></td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <div id="modal" class="modal" :class="[isDeleteModalShow ? 'show' : 'hide']">
        <div class="modal-content">
            <span id="close-modal" class="close" @click="deleteCancel">&times;</span>
            <h2>Confirmation</h2>
            <div>
                <h3 class="mt_10 mb_10">Are you sure to delete this todo?</h3>
                <button class="smart-button" @click="confirmDelete()">Confirm</button>

            </div>
        </div>
    </div>

    <div id="modal" class="modal" :class="[isEditModalShow ? 'show' : 'hide']">
        <div class="modal-content">
            <span id="close-modal" class="close" @click="isEditModalShow = false">&times;</span>
            <h2>{{(isEditMode) ? "Update": "Add"}}</h2>
            
            <div>
                <div class="input-container">
                    <label for="name">Todo Name:</label>
                    <input v-model="form.name" type="text" id="name" class="input-todo" name="name">
                    <p class="error_text" :class="[add_todo_error != '' ? 'show' : 'hide']"><strong>{{add_todo_error}}</strong></p>
                </div>
                <div class="container_item">
                    <div class="row header">
                        <div class="column item">Task Name</div>
                        <div class="column action">Action</div>
                    </div>
                    <div class="row">
                        <div class="column input">
                        <input v-model="bucket_form.name" @keyup.enter="add_tasks_in_bucket" type="text" placeholder="Task Name">
                        </div>
                        <div class="column button">
                        <button @click="add_tasks_in_bucket()">Add to List</button>
                        </div>
                    </div>
                </div>

                <div class="added_items">
                    <table class="small_table">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(bucket_task, index, key) in tasks_in_bucket" :key="key" v-if="tasks_in_bucket.length > 0">
                                <td>{{index + 1}}</td>
                                <td v-if="isEditMode"><input type="text" :value="bucket_task.name" @keyup="(e) => changeTaskName(e,bucket_task)"/></td>
                                <td v-if="!isEditMode">{{bucket_task.name}}</td>
                                <td><i class="fa fa-trash-alt cursor-pointer" @click="remove_from_bucket(index)"></i></td>
                            </tr>
                        </tbody>
                    </table>
                    <button @click="add_todo()">{{(isEditMode) ? "Update": "Add"}} Todo</button>
                </div>

            </div>
        </div>
    </div>
    

</template>
<style>
    @import '../../layouts/base.css';
    .input-todo{
        margin-bottom : 0px;
        margin-right: 20px;
    }
    .input-container {
        display: flex;
        align-items: center;
        margin-top:15px;
    }

    .input-container label {
        margin-right: 10px;
    }

</style>