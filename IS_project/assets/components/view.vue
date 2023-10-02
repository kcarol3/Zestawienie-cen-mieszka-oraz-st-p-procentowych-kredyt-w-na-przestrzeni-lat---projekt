<template>
  <div>
    {{data}}
  </div>
</template>
<script>
export default {

  name: "view",
  data() {
    return {
      data: []
    };
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      this.$http.get('/api/houses')
          .then(response => {
            this.data = response.data;
          })
          .catch(error => {
            console.error(error);
          });
    }
  }
};
</script>
<style scoped>
</style>