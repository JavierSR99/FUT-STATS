<template>
  <v-container>
    <div class="text-center">
      <h1>Página {{page}}</h1>
      <h1 v-if="this.carga == true">CARGANDO</h1>
      <v-row>
        <v-col> <pulse-loader :loading="carga"></pulse-loader> </v-col>
      </v-row>
      <v-row v-if="this.listaC != []">
        <v-col v-for="(item, index) of listaC" :key="index">
          <iframe
            width="360"
            height="215"
            :src="'https://www.youtube.com/embed/'+item.cod"
            frameborder="0"
            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
          ></iframe>
        </v-col>
      </v-row>
      <v-pagination v-model="page" :length="length" color="verdesito"></v-pagination>
    </div>
  </v-container>
</template>

<script>
import PulseLoader from "vue-spinner/src/PulseLoader.vue";

export default {
  name: "Playlist",
  data() {
    return {
      page: 1,
      length: null,
      listaC: [],
      carga: false
    };
  },
  /**
   * Creamos el número de páginas según el número de canciones de la base de datos, haciendo esto dinámico
   */
  async beforeCreate() {
    let datos = new FormData();
    datos.append("func", 17);
    await this.axios.post(this.axios.default.baseURL, datos).then(res => {
      this.length = Math.ceil(res.data.songs / 3);
    });
    await this.infoCanciones(1, 3);
  },
  methods: {
    infoCanciones(min, max) {
      this.listaC = [];
      let datos = new FormData();
      datos.append("func", 18);
      datos.append("min", min);
      datos.append("max", max);

      this.axios.post(this.axios.default.baseURL, datos).then(res => {
        this.carga = true;
        setTimeout(() => {
          for (let i of res.data) {
            this.listaC.push(i);
          }
          this.carga = false;
        }, 1200);
      });
    }
  },
  watch: {
    page(newValue, oldValue) {
      let max = newValue * 3;
      let min = max - 2;

      this.infoCanciones(min, max);
    }
  },
  components: {
    PulseLoader
  }
};
</script>