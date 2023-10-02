<template>
  <div></div>
</template>

<script>
import axios from 'axios';

export default {
  name: "importComponent"
  ,
  data() {
    return {
      housingData: Object // Tablica na pobrane dane
    };
  },
  created() {

    this.fetchData();
  },
  methods: {
    fetchData() {
      const token = localStorage.getItem('token');
      axios.get('/api/house/import', {
        headers: {
          Authorization: `Bearer ${token}`
        }
      })
          .then(response => {
            this.housingData = response.data;
          })
          .catch(error => {
            console.error('Błąd podczas pobierania danych:', error);
          });
    }
  }
};
</script>

<style scoped>

</style>