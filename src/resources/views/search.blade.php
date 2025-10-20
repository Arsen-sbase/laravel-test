<!doctype html>
    <html>
    <head>
      <meta charset="utf-8">
      <title>Property Search</title>
      <style>
        /*вапевыавп*/
        .spinner {
          width:24px;height:24px;border:4px solid #ccc;border-top:4px solid #333;border-radius:50%;
          animation:spin 1s linear infinite; display:inline-block; vertical-align:middle;
        }
        @keyframes spin{to{transform:rotate(360deg)}}
        table{border-collapse:collapse;width:100%}
        th,td{border:1px solid #ddd;padding:8px;text-align:left}
      </style>
    </head>
    <body>
      <div id="app">
        <h2>Search properties</h2>

        <div>
          <input v-model="filters.name" placeholder="Name (partial)">
          <input v-model.number="filters.bedrooms" type="number" min="0" placeholder="Bedrooms">
          <input v-model.number="filters.bathrooms" type="number" min="0" placeholder="Bathrooms">
          <input v-model.number="filters.storeys" type="number" min="0" placeholder="Storeys">
          <input v-model.number="filters.garages" type="number" min="0" placeholder="Garages">
          <input v-model.number="filters.price_min" type="number" min="0" placeholder="Price min">
          <input v-model.number="filters.price_max" type="number" min="0" placeholder="Price max">
          <button @click="search">Search</button>
          <button @click="reset">Reset</button>
          <span v-if="loading" class="spinner" aria-hidden="true"></span>
        </div>

        <div v-if="!loading && results.length === 0" style="margin-top:16px">
          <strong>No results found.</strong>
        </div>

        <table v-if="results.length>0" style="margin-top:16px">
          <thead>
            <tr>
              <th>Name</th><th>Price</th><th>Bedrooms</th><th>Bathrooms</th><th>Storeys</th><th>Garages</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="r in results" :key="r.id">
              <td>@{{ r.name }}</td>
              <td>@{{ r.price }}</td>
              <td>@{{ r.bedrooms }}</td>
              <td>@{{ r.bathrooms }}</td>
              <td>@{{ r.storeys }}</td>
              <td>@{{ r.garages }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- CDN Vue 3 + axios -->
      <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

      <script>
        const { createApp, ref } = Vue;

        createApp({
          data() {
            return {
              filters: {
                name: '',
                bedrooms: null,
                bathrooms: null,
                storeys: null,
                garages: null,
                price_min: null,
                price_max: null
              },
              results: [],
              loading: false,
            };
          },
          methods: {
            buildQuery() {
              const params = {};
              for (const k in this.filters) {
                const v = this.filters[k];
                if (v !== null && v !== '' && v !== undefined) params[k] = v;
              }
              return params;
            },
            async search() {
              this.loading = true;
              this.results = [];
              try {
                const resp = await axios.get('/api/properties', { params: this.buildQuery() });
                this.results = resp.data;
              } catch (e) {
                console.error(e);
                alert('Error fetching results');
              } finally {
                this.loading = false;
              }
            },
            reset() {
              this.filters = {name:'',bedrooms:null,bathrooms:null,storeys:null,garages:null,price_min:null,price_max:null};
              this.results = [];
            }
          }
        }).mount('#app');
      </script>
    </body>
</html>
