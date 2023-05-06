<script setup>
    import {reactive, ref} from 'vue';
    import {useRouter} from 'vue-router';

    const router = useRouter();
    let form = reactive({
        name : '',
        email : '',
        password : ''
    })
    let error = ref('');
    
    const register = async() => {
        return axios.post('/api/auth/register', form)
        .then(response => {
            if(response.status === 200){
                localStorage.setItem('register_status', 'true');
                

                if(response.data.status == true){
                  router.push('/');
                }
            }else{

            }
        })
        .catch(function (error) {
          // handle error
          error.value = error?.response?.data?.message;
        });
    }
</script>
<template>
    <div class="register-container">
      <p class="error" v-if="error">{{error}}</p>
      <p class="error" v-if="error">{{error}}</p>
      <h1>Register</h1>
      <form @submit.prevent="register">
        <p class="error" v-if="error">{{error}}</p>
        <label for="name">Name</label>
        <input type="name" id="name" name="name" v-model="form.name" required>

        <p class="error" v-if="error">{{error}}</p>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" v-model="form.email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" v-model="form.password" required>

        <button type="submit">Register</button>
      </form>
    </div>
</template>
<style>
body {
  background-color: #f1f1f1;
}
.error {
  color:#ff8e8e;
}
.register-container {
  width: 400px;
  margin: auto;
  margin-top: 100px;
  background-color: #fff;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3);
}

h1 {
  text-align: center;
  color: #555;
}

form {
  display: flex;
  flex-direction: column;
}

label {
  font-weight: bold;
  margin-bottom: 5px;
  color: #555;
}

input {
  padding: 10px;
  border-radius: 3px;
  border: 1px solid #ccc;
  margin-bottom: 20px;
}

button {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 3px;
  font-size: 16px;
  cursor: pointer;
}

button:hover {
  background-color: #3e8e41;
}

</style>