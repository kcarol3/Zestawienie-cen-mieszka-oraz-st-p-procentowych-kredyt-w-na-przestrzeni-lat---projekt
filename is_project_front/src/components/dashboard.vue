<template >
  <div v-if="displayContent">
  <div class="dashboard" >
    <h2>Dashboard</h2>
  </div>
  <div class = "menu">
    <div class="input-layout">
      <label for="start-date">Rok początku:</label>
      <div class="input-container">
        <input type="number" id="start-date" min="1998" max="2022" v-model="startDate" placeholder="Rok początku" class="custom-input" />
      </div>
    </div>
    <div class="input-layout">
      <label for="end-date">Rok końca:</label>
      <div class="input-container">
        <input type="number" id="end-date" min="1998" max="2022" v-model="endDate" placeholder="Rok końca" class="custom-input" />
      </div>
    </div>
    <select class="dropdown-select" v-model="type">
      <option v-for="item in tableType" :key="item.id" :value="item.name">{{ item.name }}</option>
    </select>
    <button @click="fetchData">Stwórz Tabele</button>
    <button @click="exportDataTable">Pobierz dane tabeli</button>
    <select class="dropdown-select" v-model="selectedItem">
      <option v-for="item in items" :key="item.id" :value="item.name">{{ item.name }}</option>
    </select>
    <button @click="fetchChartData">Stwórz wykres</button>
    <button @click="adminPanel" class = "button2">Admin Panel</button>
    <button @click="logout" class = "button2">Wyloguj</button>
  </div>

  <div>
    <div class="data" >
      <table  class="table" v-if="displayTable">
        <thead>
        <tr>
          <th style="width: 15%">Rok </th>
          <th style="width: 15%">Stopa procentowa</th>
          <th style="width: 15%">{{ keys[1] }}</th>
          <th style="width: 15%">{{ keys[2] }}</th>
          <th style="width: 15%">{{ keys[3] }}</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(yearData, year) in housingData" :key="year">
          <td>{{ year }}</td>
          <td >{{ format(yearData.rate)}}</td>
          <td >{{ format(yearData[ keys[1]].avgHousePrice)}}</td>
          <td >{{ format(yearData[ keys[2]].avgHousePrice)}}</td>
          <td >{{ format(yearData[ keys[3]].avgHousePrice)}}</td>
        </tr>
        </tbody>
      </table>
      <line-chart xtitle="Wartość stopy [%]" ytitle="Cena mieszkania [pln]" :data="chartData" :options="chartOptions" v-if="displayChart"></line-chart>
    </div>
  </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'DashboardComponent',
  data() {
    return {
      housingData: null, // Tablica na pobrane dane
      chartOptions: {
        responsive: true,
      },
      chartData: null,
      displayChart:false,
      displayTable:false,
      keys: null,
      selectedItem: "Warszawa",
      items: [
        { id: 1, name: 'Warszawa' },
        { id: 2, name: 'Kraków' },
        { id: 3, name: 'Poznań' },
      ],
      tableType: [
        { id: 1, name: 'Miasta' },
        { id: 2, name: 'Rozmiar' },
      ],
      startDate: 1998,
      endDate: 2022,
      displayContent: false,
      type: 'Miasta',

    };
  },

  created() {
    this.verifyToken();
  },

  methods: {
    logout(){
      localStorage.clear();
      this.$router.push('/');
    },
    async adminPanel(){
      const token = localStorage.getItem('token');
      await axios.get('/api/admin', {
        headers: {
          Authorization: `Bearer ${token}`
        }})
          .then(response => {
            console.log(response)
            this.$router.push('/adminPanel')
          })
          .catch(error => {
            if (error.response.status === 401) {
              this.$router.push('/')
            }
            if (error.response.status === 403) {
              this.$toast.error("Nie masz odpowiednich uprawnień do tej strony!")
            }
          });
    },

    format(value) {
      let val = (value/1).toFixed(2).replace(' ', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    },

    async fetchChartData() {
      const token = localStorage.getItem('token');

      try {
        const response = await axios.get('/api/dashboard/chart', {
          params: {
            city: this.selectedItem,
          },
          headers: {
            Authorization: `Bearer ${token}`
          },
        });
        this.chartData = response.data;
        this.displayTable = false
        this.displayChart = true// Otrzymane dane z serwera
      } catch (error) {
        console.error(error);
        if (error.response.status === 401) {
          this.$router.push('/')
        }
      }
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
      formData.append('rates', selectedFile);

      let endpoint = '';
      if (inputNumber === 1) {
        endpoint = '/upload1';
      } else if (inputNumber === 2) {
        endpoint = '/api/import/interestRates';
      }

      await axios.post(endpoint, formData, {
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'multipart/form-data'
        }
      })
          .then(response => {
            console.log(response.data);
            // Obsłuż odpowiedź serwera
          })
          .catch(error => {
            console.error(error);
            if (error.response.status === 401) {
              this.$router.push('/')
            }
          });
    },

    async verifyToken(){
      const token = localStorage.getItem('token');
      await axios.get('/api/auth', {
        headers: {
          Authorization: `Bearer ${token}`
        }})
          .then(response => {
            console.log(response)
            this.displayContent = true
          })
          .catch(error => {
            if (error.response.status === 401) {
              this.$router.push('/')
            }
          });
    },

    async exportDataTable(){
      const token = localStorage.getItem('token');
      await axios.get('/api/dashboard/customTable-export', {
        params: {
          type: this.type,
          startDate: this.startDate,
          endDate: this.endDate
        },
        headers: {
          Authorization: `Bearer ${token}`
        }})
          .then(response => {
            const jsonData = response.data;

            // Tworzenie linku do pobrania pliku JSON
            const blob = new Blob([jsonData], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'tableData.json';

            // Kliknięcie linku w celu pobrania pliku
            link.click();
          })
          .catch(error => {
            if (error.response.status === 401) {
              this.$router.push('/')
            }
          });
    },



    async fetchData() {
      const token = localStorage.getItem('token');

      await axios.get('/api/dashboard/customTable', {
        params: {
          type: this.type,
          startDate: this.startDate,
          endDate: this.endDate
        },
        headers: {
          Authorization: `Bearer ${token}`
        }})
          .then(response => {
            this.housingData = response.data;
            this.keys = Object.keys(response.data[Object.keys(response.data)[0]])
            this.displayChart = false
            this.displayTable = true
          })
          .catch(error => {
            console.error('Błąd podczas pobierania danych:', error);
            if (error.response.status === 401) {
              this.$router.push('/')
            }
          });
    }
  }
};
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
.menu {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-content: center;
  margin-bottom: 50px;
}
.input-layout {
  margin-right: 20px;
  margin-top: 3px
}
.custom-input {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  width: 100px;
}
.data{
  width: 80%;
  margin: auto;
}

.dropdown-select {
  width: 100px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: #fff;
  cursor: pointer;
  height: 37px;
  margin: 20px;
}

.dropdown-select:focus {
  outline: none;
  box-shadow: 0 0 4px rgba(0, 0, 0, 0.3);
}

.selected-item {
  margin-top: 8px;
}
.dashboard {
  display: flex;
  flex-direction: column;
  align-items: center;
  color: #0056b3;
  font-size: 28px;
  justify-content: center;
  width: 100%;
}
.table {
  border-collapse: collapse;
  font-family: Arial, sans-serif;
}

.table th,
.table td {
  padding: 20px;
  border: 1px solid #ddd;
}

.table th {
  background-color: #f5f5f5;
  text-align: center;
}

.table tr:nth-child(even) {
  background-color: #f2f2f2;
}

.table tr:hover {
  background-color: #e0e0e0;
}

.table td {
  text-align: center;
}
button {
  margin: 20px;
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