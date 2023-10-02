<template>
  <div class="con">
    <h2 class="login-title">Rejestracja</h2>
    <form @submit.prevent="signup" class="login-form">
      <input type="text" class="login-input" placeholder="Nazwa użytkownika" v-model="username"/>
      <input type="password" class="login-input" placeholder="Hasło" v-model = "password"/>
      <button class="login-button" type="submit">Zarejestruj</button>
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "registerComponent",
  data() {
    return {
      username: '',
      password: ''
    };
  },
  methods: {
    signup() {
      console.log('Signup form:', {
        username: this.username,
        password: this.password
      });
      axios.post('/api/register', { username: this.username, password: this.password })
          .then(response => {
            console.log('Successful register:',response.status );
            this.$toast.success("Zarejestrowano użytkownika");
            this.$router.push('/dashboard')
          })
          .catch(error => {
            console.error('Register error:', error.response.data);
            this.$toast.error("Nie udało się zarejestrwoać!")
          });
    }
  }
}
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