<template>
<div class="container">
  <button @click="exportAllData" class = "button2" >Pobierz dane wszystkich miast</button>
  <div class = "form">
    <button  @click="uploadFile(1)" class = "button2">Zaimportuj dane mieszkań(json)</button>
    <input type="file" ref="fileInput1" @change="handleFileChange(1, $event)" />
  </div>
  <div class = "form">
    <button @click="uploadFile(2)" class = "button2">Zaimportuj dane stóp (xml)</button>
    <input type="file" ref="fileInput2" @change="handleFileChange(2, $event)" />
  </div>
  <button @click="deleteAllData" class = "button2" style = "background: red" >Usuń dane z bazy danych</button>
  <button @click="soap" class = "button2">Liczba domów</button>
  <div style="margin: 50px 50px 10px 0px; font-size: 34px">Liczba domów: {{count}}</div>
</div>
  <button @click="this.$router.push('/dashboard')">Powrót do dashboard</button>
</template>

<script>
import axios from "axios";

export default {
  name: "adminPanel",

  data() {
    return {
      selectedFiles: [],
      count: null,
    }
  },

  created() {
    this.verifyToken();
  },
  methods: {
    async soap(){
      const soapEndpoint = 'https://localhost:8000/soap';

      const soapRequest = `
    <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="https://localhost:8000/soap">
        <soapenv:Header/>
        <soapenv:Body>
            <web:getRecordCount>
                <web:tableName>houses</web:tableName>
            </web:getRecordCount>
        </soapenv:Body>
    </soapenv:Envelope>
`;

      await axios.post(soapEndpoint, soapRequest, {
        headers: {
          'Content-Type': 'text/xml',
        },
      })
          .then(response => {
            const parser = new DOMParser();
            const xmlDoc = parser.parseFromString(response.data, 'application/xml');
            this.count = xmlDoc.querySelector('return').textContent;

          })
          .catch(error => {
            console.error(error);
          });
    },

    async deleteAllData(){
      const token = localStorage.getItem('token');
      await axios.delete('/api/delete-all', {
        headers: {
          Authorization: `Bearer ${token}`
        }})
          .then(response => {
            console.log(response);
            this.$toast.info("Wyczyszczone dane z bazy danych")
          })
          .catch(error => {
            console.log(error)
          });
    },

    handleFileChange(inputNumber, event) {
      this.selectedFiles[inputNumber] = event.target.files[0];
    },

    async uploadFile(inputNumber) {
      const token = localStorage.getItem('token');
      const selectedFile = this.selectedFiles[inputNumber];

      if (!selectedFile) {
        this.$toast.warning("Nie wybrano pliku!")
        return
      }

      const formData = new FormData();

      let endpoint = '';
      if (inputNumber === 1) {
        endpoint = '/api/import/houses';
        formData.append('houses', selectedFile);
      } else if (inputNumber === 2) {
        endpoint = '/api/import/interestRates';
        formData.append('rates', selectedFile);
      }

      await axios.post(endpoint, formData, {
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'multipart/form-data'
        }
      })
          .then(response => {
            console.log(response.data);
            this.$toast.info("Dodano dane do bazy danych.");
          })
          .catch(error => {
            console.error(error);
          });
    },

    async exportAllData(){
      const token = localStorage.getItem('token');
      await axios.get('/api/dashboard/allData-export', {
        headers: {
          Authorization: `Bearer ${token}`
        }})
          .then(response => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'allData.xml');
            document.body.appendChild(link);
            link.click();
          })
          .catch(error => {
            if (error.response.status === 401) {
              this.$router.push('/')
            }
          });
    },

    async verifyToken(){
      const token = localStorage.getItem('token');
      await axios.get('/api/admin', {
        headers: {
          Authorization: `Bearer ${token}`
        }})
          .then(response => {
            console.log(response)
            this.displayContent = true
          })
          .catch(error => {
            console.log(error);
            if (error.response.status === 403) {
              this.$router.push('/dashboard')
            }
            if (error.response.status === 401) {
              this.$router.push('/')
            }
          });
    },
  }

}
</script>
<style scoped>
  .form{
  display: flex;
  justify-content: center;
  flex-direction: column;
}
  .button2 {
    margin-left: 20px;
  background: #030358;
}
  .button2:hover{
  background: #010123;
}


  .container {
    display: flex;
    flex-direction: row;
    margin: auto;
    justify-content: center;
    width: 100%;
  }

  button {
  margin: 50px 50px 10px 50px;
  padding: 10px 20px;
  background: linear-gradient(#007bff, #0056b3);
  color: #fff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

  button:hover {
  background: linear-gradient(#0056b3, #003280);
}

  button:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.5);
}
</style>