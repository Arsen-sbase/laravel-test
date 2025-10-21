<!doctype html>
    <html>
    <head>
      <meta charset="utf-8">
      <!-- Element Plus CSS -->
      <link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css" />
      <title>Property Search</title>
      <style>
        .spinner {
          width:24px;
          height:24px;
          border:4px solid #ccc;
          border-top:4px solid #409EFF;
          border-radius:50%;
          animation:spin 1s linear infinite;
          display:inline-block;
          vertical-align:middle;
        }
        .spinner-wrap{
          position: fixed;
          top: 40%;
          left: 50%;
          transform: translate(-50%, -50%);
          z-index: 1000;
          pointer-events: none;
        }
        @keyframes spin{ to{ transform:rotate(360deg); } }
        table{border-collapse:collapse;width:100%}
        table thead th span { color: #409EFF; }
        th,td{border:1px solid #ddd;padding:8px;text-align:left}

        .gr9 {
            background:
                linear-gradient(rgba(135, 60, 255, 0.4), rgba(135, 60, 255, 0.0) 80%),
                linear-gradient(-45deg, rgba(120, 155, 255, 0.9) 25%, rgba(255, 160, 65, 0.9) 75%);
        }
        .banner {
            text-align: center;
            padding: 36px 12px;
            color: #fff;
            border-radius: 8px;
            margin-bottom: 16px;
        }
        .banner-title { font-size: 28px; margin: 0; font-weight: 600; }

        /* layout for controls */
        .controls {
          display: flex;
          flex-wrap: wrap;
          gap: 8px;
          justify-content: center;
          align-items: center;
          margin-bottom: 16px;
        }
        .controls input {
          padding: 8px;
          min-width: 140px;
          border: 1px solid #ccc;
          border-radius: 4px;
        }

        /* center No results text */
        .no-results {
          position: fixed;
          top: 40%;
          left: 50%;
          transform: translate(-50%, -50%);
          z-index: 1000;
          font-weight: 600;
          text-align: center;
          pointer-events: none;
        }

        /* stronger button hover specificity (Element Plus) */
        .el-button.search:hover,
        .el-button.search.is-hover { background: #409EFF !important; color: #fff !important; }
        .el-button.reset:hover,
        .el-button.reset.is-hover  { background: #E6A23C !important; color: #fff !important; }
        .el-button.search:hover,
        .el-button.reset:hover {
          box-shadow: none !important;
          outline: none !important;
          border-color: transparent !important;
        }
      </style>
    </head>
    <body>
      <div id="app">
        <div class="gr9 banner">
          <h1 class="banner-title">Search properties</h1>
        </div>

        <div class="controls">
          <input v-model="filters.name" placeholder="Name (partial)">
          <input v-model.number="filters.bedrooms" type="number" placeholder="Bedrooms" inputmode="numeric" pattern="\d*" min="0" @keydown="numericOnly" @paste.prevent>
          <input v-model.number="filters.bathrooms" type="number" min="0" placeholder="Bathrooms" inputmode="numeric" pattern="\d*" min="0" @keydown="numericOnly" @paste.prevent>
          <input v-model.number="filters.storeys" type="number" min="0" placeholder="Storeys" inputmode="numeric" pattern="\d*" min="0" @keydown="numericOnly" @paste.prevent>
          <input v-model.number="filters.garages" type="number" min="0" placeholder="Garages" inputmode="numeric" pattern="\d*" min="0" @keydown="numericOnly" @paste.prevent>
          <input v-model.number="filters.price_min" type="number" min="0" placeholder="Price min" inputmode="numeric" pattern="\d*" min="0" @keydown="numericOnly" @paste.prevent>
          <input v-model.number="filters.price_max" type="number" min="0" placeholder="Price max" inputmode="numeric" pattern="\d*" min="0" @keydown="numericOnly" @paste.prevent>

          <!-- Element Plus buttons -->
          <el-button class="search" @click="search">Search</el-button>
          <el-button class="reset" @click="reset">Reset</el-button>
        </div>

        <div class="spinner-wrap">
          <span v-if="loading" class="spinner" aria-hidden="true"></span>
        </div>

        <div v-if="!loading && results.length === 0" class="no-results">
          No results found
        </div>

        <table v-if="results.length>0" style="margin-top:16px">
          <thead>
            <tr>
              <th @click="sortBy('name')">Name <span v-if="sortKey==='name'">@{{ sortDir===1 ? '▲' : '▼' }}</span></th>
              <th @click="sortBy('price')">Price <span v-if="sortKey==='price'">@{{ sortDir===1 ? '▲' : '▼' }}</span></th>
              <th @click="sortBy('bedrooms')">Bedrooms <span v-if="sortKey==='bedrooms'">@{{ sortDir===1 ? '▲' : '▼' }}</span></th>
              <th @click="sortBy('bathrooms')">Bathrooms <span v-if="sortKey==='bathrooms'">@{{ sortDir===1 ? '▲' : '▼' }}</span></th>
              <th @click="sortBy('storeys')">Storeys <span v-if="sortKey==='storeys'">@{{ sortDir===1 ? '▲' : '▼' }}</span></th>
              <th @click="sortBy('garages')">Garages <span v-if="sortKey==='garages'">@{{ sortDir===1 ? '▲' : '▼' }}</span></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="r in displayedResults" :key="r.id">
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
      <!-- Element Plus JS -->
      <script src="https://unpkg.com/element-plus/dist/index.full.js"></script>
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
              sortKey: null,
              sortDir: 1,
            };
          },
          methods: {
            numericOnly(e) {
              // enable navigation buttons
              const allowed = ['Backspace','Tab','ArrowLeft','ArrowRight','Delete','Home','End'];
              if (allowed.includes(e.key)) return;
              // numbers 0-9 only
              if (!/^[0-9]$/.test(e.key)) e.preventDefault();
            },
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
            },
            sortBy(key) {
              if (this.sortKey === key) {
                this.sortDir = -this.sortDir;
              } else {
                this.sortKey = key;
                this.sortDir = 1;
              }
            }
          },
          computed: {
            displayedResults() {
              if (!this.sortKey) return this.results;
              return [...this.results].sort((a,b) => {
                const A = a[this.sortKey];
                const B = b[this.sortKey];
                if (A === B) return 0;
                return (A > B ? 1 : -1) * this.sortDir;
              });
            }
          }
        }).use(ElementPlus).mount('#app');
      </script>
    </body>
</html>
