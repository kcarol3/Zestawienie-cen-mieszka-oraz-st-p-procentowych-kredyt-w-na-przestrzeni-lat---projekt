<template>
  <div>
    <h2>Login</h2>
    <form @submit.prevent="login">
      <label for="username">Username:</label>
      <input type="text" id="username" v-model="username" required>
      <label for="password">Password:</label>
      <input type="password" id="password" v-model="password" required>
      <button type="submit">Log in</button>
    </form>
  </div>
</template>

<script>
export default {
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
      // Przykładowy kod zapytania axios:
      // this.$http.post('/api/login', { username: this.username, password: this.password })
      //   .then(response => {
      //     // Obsłuż odpowiedź serwera po udanym zalogowaniu
      //   })
      //   .catch(error => {
      //     // Obsłuż błędy logowania
      //   });
    }
  }
};
</script>
