<template>
  <v-container>
    <span class="bg"></span>
    <v-row>
      <v-form @submit.prevent="buscarJugador">
        <v-text-field v-model="pname" v-on:keyup="buscarJugador" label="Busca un jugador..."></v-text-field>
      </v-form>
    </v-row>
    <v-row>
      <v-card class="mx-auto" max-width="1000" tile two-line v-if="player != null && pname!== ''">
        <v-list-item v-for="p in player" :key="p.COD">
          <v-list-item-avatar>
            <v-img :src="'http://localhost/FUT%20STATS/backend/img/jugadores/'+p.IMG"></v-img>
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title>
              <router-link
                class="enlace"
                :to="{ name : 'Card', params : { id : p.COD }}"
              >{{p.NICKNAME}}</router-link>
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-card>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: "Busqueda",
  data() {
    return {
      pname: "",
      player: null
    };
  },
  methods: {
    buscarJugador() {
      if (this.pname != "") {
        let data = new FormData();
        data.append("func", 10);
        data.append("player", this.pname);

        this.axios
          .post(this.axios.default.baseURL, data)
          .then(res => {
            this.player = [];
            let players = this.llenarJugadores(res.data);
            this.player = players;
          })
          .catch(err => {
            console.log(err);
          });
      }
    },
    llenarJugadores(payload) {
      let player = [];
      for (let jugador of payload) {
        player.push(jugador);
      }
      return player;
    }
  },
  computed: {}
};
</script>


<style scoped>
.enlace {
  text-decoration: none;
  color: black;
  font-size: 1rem;
}

.bg {
    width: 100%;
    height: 100%;
    position: absolute;
    left: 0;
    background: url( '../assets/prueba logo.png') no-repeat center center;
    
    opacity: 0.5;
  }

</style>