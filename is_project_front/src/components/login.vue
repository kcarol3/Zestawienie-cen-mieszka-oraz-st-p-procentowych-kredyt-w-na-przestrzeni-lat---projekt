<template>
  <div class="con">
    <h2 class="login-title">Logowanie</h2>
    <form @submit.prevent="login" class="login-form">
      <input type="text" class="login-input" placeholder="Nazwa użytkownika" v-model="username"/>
      <input type="password" class="login-input" placeholder="Hasło" v-model = "password"/>
      <button class="login-button" type="submit">Log in</button>
      <button class="login-button" @click="this.$router.push('/signup')" style="background: grey">Zarejestruj</button>
    </form>
  </div>
</template>

<script>
import axios from "axios";
axios.defaults.baseURL = 'https://localhost:8000';
export default {
  name: "loginView",
  data() {
    return {
      username: '',
      password: ''
    };
  },
  methods: {
    login() {
      // Wywołaj odpowiednie zapytanie do serwera Symfony, aby sprawdzić dane logowania
      console.log('Login form:', {
        username: this.username,
        password: this.password
      });
      axios.post('/api/login_check', { username: this.username, password: this.password })
        .then(response => {
          console.log('Successful login:',response.status );
          localStorage.setItem('token', response.data.token);
          this.$router.push('/dashboard')
        })
        .catch(error => {
          console.error('Login error:', error.response.data);
          this.$toast.error("Nie udało się zalogować!")
        });
    }
  }
};
</script>
<style scoped>
.con {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.login-title {
  text-align: center;
  font-size: 24px;
  margin-bottom: 20px;
}

.login-form {
  display: flex;
  flex-direction: column;
  width: 300px;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: #f5f5f5;
}

.login-input {
  padding: 10px;
  margin-bottom: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.login-button {
  padding: 10px;
  margin: 20px;
  background-color: #007bff;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>
