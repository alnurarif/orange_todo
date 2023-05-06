<script setup>
    import {reactive, ref} from 'vue';
    import {useRouter} from 'vue-router';

    const router = useRouter();
    let form = reactive({
        email : '',
        password : ''
    })
    let error = ref('');
    let success = ref(localStorage.getItem('register_status'));
    setTimeout(() => {
        success.value = "";
        localStorage.removeItem('register_status');
    }, 4000);
    const login = async() => {
        return axios.post('/api/auth/login', form)
        .then(response => {
            if(response.status === 200){
                localStorage.setItem('token', response.data.token);
                localStorage.setItem('user_info', JSON.stringify(response.data.user_info));
                
                router.push('/user/home');
            }else{
            }
        })
        .catch(function (err) {
          // handle error
          error.value = err?.response?.data?.message;
        });
    }
</script>
<template>
    <div class="login-container">
      <h1>Login</h1>
      <form @submit.prevent="login">
        <p class="error" v-if="error">{{error}}</p>
        <p class="success" v-if="success == 'true'">Registered Successfully</p>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" v-model="form.email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" v-model="form.password" required>

        <button type="submit">Login</button>
      </form>
      <router-link to="/register" class="sign-up">Sign Up</router-link>
    </div>
</template>
<style>
body {
  background-color: #f1f1f1;
}
.error {
  color:#ff8e8e;
}
.success{
      color: #fff;
    background: #4ba013;
    text-align: center;
    line-height: 33px;
}
.sign-up{
  margin-top: 20px;
  display: block;
  text-decoration: none;
  text-align: center;
}
.login-container {
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