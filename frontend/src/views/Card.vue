<template>
  <v-container v-if="card != null">
    <v-row>
      <v-col cols="12" class="text-center">
        <h1>{{card.infoplayer.nombre}} {{card.infoplayer.apellidos}}</h1>
      </v-col>
    </v-row>
    <v-row>
      <v-col xs="12" md="5">
        <img :src="rutaImg+card.infocard.img_carta" :alt="card.infoplayer.nombre" width="400px" />
        <v-row>
          <v-col cols="12">
            <v-alert type="warning" v-if="errorrate !== null && tiempo == true">{{errorrate}}</v-alert>
          </v-col>
        </v-row>
        <v-row>
          <v-col>
            <font-awesome-icon :icon="['fas', 'thumbs-up']" size="lg" class="dedo" 
                v-if="valoracion == false" @click="sendRate(0)" />

            <font-awesome-icon :icon="['fas', 'thumbs-up']" size="lg" class="dedo" color="blue" 
                v-if="valoracion == true" @click="sendRate(0)" />
            {{mg}}
          </v-col>

          <v-col>
            <font-awesome-icon :icon="['fas', 'thumbs-down']" size="lg" class="dedo" 
                v-if="valoracionN == false" @click="sendRate(1)" />

            <font-awesome-icon :icon="['fas', 'thumbs-down']" size="lg" class="dedo" color="blue" 
                v-if="valoracionN == true" @click="sendRate(1)" />
            <!--<v-icon class="dedo" v-if="valoracionN == false" @click="sendRate(1)">fas fa-thumbs-down</v-icon>
            <v-icon class="dedo" color="blue" v-if="valoracionN == true" @click="sendRate(1)">fas fa-thumbs-down</v-icon> -->
            {{nmg}}
          </v-col>
        </v-row>
      </v-col>
      <v-col xs="12" md="7">
        <v-row>Posición: {{card.other.posicion}} | País: {{card.other.pais}} | Calidad: {{card.other.calidad}}</v-row>
        <v-row>
          <v-simple-table>
            <thead>
              <tr>
                <th class="text-left">Precio PS4</th>
                <th class="text-left">Precio XBOX</th>
                <th class="text-left">Precio PC</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  {{card.infocard.precio_ps}}
                  <i class="fas fa-coins"></i>
                </td>
                <td>
                  {{card.infocard.precio_xbox}}
                  <i class="fas fa-coins"></i>
                </td>
                <td>
                  {{card.infocard.precio_pc}}
                  <i class="fas fa-coins"></i>
                </td>
              </tr>
            </tbody>
          </v-simple-table>
        </v-row>
        <v-row>
          <v-col v-for="(item, index) of card.stats" :key="index" cols="12" class="m-2">
            {{index}} - {{item}}
            <v-progress-linear v-if="item >= 70" :value="item" color="verdesito"></v-progress-linear>
            <v-progress-linear v-if="item < 70 && item >= 60" :value="item" color="warning"></v-progress-linear>
            <v-progress-linear v-if="item < 60" :value="item" color="error"></v-progress-linear>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12">
        <h1>Comments!</h1>
      </v-col>
      <v-col cols="12">
        <v-alert type="warning" v-if="error !== null">{{error}}</v-alert>
        <v-alert type="success" v-if="mensaje != null && tiempo == true">{{mensaje}}</v-alert>
        <v-form @submit.prevent="insertarComment">
          <v-text-field
            v-model="$v.comentario.$model"
            color="verdesito"
            label="Write your comment here!"
            :error-messages="commentErrors"
            required
          ></v-text-field>
        </v-form>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" v-for="(item, index) in comments" :key="index">
        <v-card color="rgba(12, 13, 14, 0.5)" dark>
          <v-avatar>
            <img :src="'http://localhost:80/FUT%20STATS/backend/img/perfil/'+item.IMG" />
          </v-avatar>
          <v-card-title class="headline">{{item.CONTENT}}</v-card-title>
          <v-card-subtitle>{{item.USER}} - {{item.DATE}}
            <small v-if="item.USER === username" class="eliminar" @click="eliminar(item.COD)">ELIMINAR COMENTARIO</small>
            </v-card-subtitle>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>


<script>
import { mapActions, mapState, mapMutations } from "vuex";
import { required, minLength, maxLength } from "vuelidate/lib/validators";
import moment from "moment";

export default {
  name: "Card",
  data() {
    return {
      id: this.$route.params.id,
      mg: null,
      nmg: null,
      valoracion: false,
      valoracionN : false,
      rutaImg: "http://localhost:80/FUT%20STATS/backend/img/cartas/",
      headers: [
        {
          text: "Precio PS4",
          align: "center",
          value: "ps"
        },
        { text: "Precio XBOX ONE", align: "center", value: "xbox" },
        { text: "Precio PC", align: "center", value: "pc" }
      ],
      desserts: [
        {
          ps: "jaja",
          xbox: "hola",
          pc: "hola"
        }
      ],
      comentario: "",
      errorrate : null,
      error: null,
      fecha: Date.now(),
      mensaje: null,
      tiempo: false
    };
  },
  methods: {
    ...mapMutations(["setComments", "setCard", "deleteCard", "addComment"]),
    /* realiza la lógica necesaria para enviar o no enviar un comentario al servidor que se desea introducir */
    insertarComment() {
      //si se cumplen los requisitos para insertar el comentario...
      if (
        this.$v.comentario.required == true &&
        this.$v.comentario.minLength == true &&
        this.$v.comentario.maxLength == true
      ) {
        this.error = null;
        if (localStorage.getItem("username")) {
          moment.locale("en");
          let date = moment(this.fecha).format("MMMM Do YYYY, h:mm a");
          let data = new FormData();
          data.append("func", 4);
          data.append("content", this.comentario);
          data.append("date", date);
          data.append("cod_card", this.id);
          data.append("cod_user", localStorage.getItem("userId"));

          this.axios.post(this.axios.default.baseURL, data).then(res => {
            if (res.data.insercion == "correcta") {
              console.log(res.data);
              this.addComment({
                DATE: date,
                CONTENT: this.comentario,
                USER: localStorage.getItem("username"),
                IMG: this.profilePic
              });
              this.comentario = "";
              this.mensaje = res.data.mensaje;
              this.tiempo = true;
              setTimeout(() => {
                this.tiempo = false;
                this.mensaje = null;
              }, 3000);
            }
          });
        } else {
          this.error =
            "Por favor, inicia sesión para poder introducir comentarios";
        }
      } else {
        this.error =
          "Por favor, introduce un comentario de entre 10 y 255 carácteres";
      }
    },
    async getCardData() {
      let data = new FormData();
      data.append("func", 11);
      data.append("player", this.id);
      await this.axios
        .post(this.axios.default.baseURL, data)
        .then(res => {
          this.setCard(res.data);
          this.setComments(res.data.comments);
        })
        .catch(err => {
          console.log(err);
        });
    },
    getPlayerLikes() {
      let datos = new FormData();
      datos.append("func", 19);
      datos.append("cod_card", this.id);

      this.axios.post(this.axios.default.baseURL, datos).then(res => {
        this.mg = parseInt(res.data.MG);
        this.nmg = parseInt(res.data.NMG);
      });
    },
    //devuelve la valoración (si es que existe del usuario a la carta)
    statusRate() {
      // creamos objeto FormData para que se envíen los datos correctamente
      let datos = new FormData();
      datos.append("func", 20);
      datos.append("cod_card", this.id);
      datos.append("cod_user", localStorage.getItem("userId"));

      this.axios.post(this.axios.default.baseURL, datos).then(res => {
        // si la respuesta es 0, quiere decir que el usuario había dado "me gusta" previamente
        if (res.data.rate == 0) {
          // pasamos valoración a true, para que el botón se vea azul
          this.valoracion = true;
        }
        else if (res.data.rate == 1) {
          this.valoracionN = true;
        }
      });
    },
    sendRate(rating) {
      if (localStorage.getItem("userId")) {
        let datos = new FormData();
        datos.append("func", 5);
        datos.append("rate", rating);
        datos.append("cod_card", this.id);
        datos.append("cod_user", localStorage.getItem("userId"));

        this.axios.post(this.axios.default.baseURL, datos).then(res => {
          if (res.data.mensaje === "OK") {
            switch (rating) {
              case 0:
                if (this.valoracion == true) {
                  this.valoracion = false;
                  this.mg -= 1;
                } else {
                  this.valoracion = true;
                  this.mg += 1;
                  if (this.valoracionN == true) {
                    this.valoracionN = false;
                    this.nmg -= 1;
                  }
                }
                break;
                case 1:
                  if (this.valoracionN == true) {
                    this.valoracionN = false;
                    this.nmg -= 1;
                  } else {
                    if (this.valoracion == true) {
                      this.valoracion = false;
                      this.mg -= 1;
                    }
                    this.valoracionN = true;
                    this.nmg += 1;
                    
                  }
                  break;
            }
          }
        });
      } else {
        this.errorrate = 'Para valorar un jugador, es necesario iniciar sesión como usuario';
              this.tiempo = true;
              setTimeout(() => {
                this.tiempo = false;
                this.errorrate = null;
              }, 3000);
      }
    },
    /**
     * elimina un comentario de la base de datos (solo podrá llamar a esta función el usuario creador de dicho comentario)
     */
    eliminar (cod) {
      let datos = new FormData ();
      datos.append('func', 21);
      datos.append('cod_comment', cod);

      this.axios.post(this.axios.default.baseURL, datos)
      .then( res => {
        console.log(res)
        if (res.data.result == 'error') {
          console.log('true');
          let borrado = this.comments.findIndex(item => item.COD == cod);
          this.comments.splice(borrado, 1);
        } else {
          console.log('false');
        } 
      })
    }
  },
  created() {
    this.getCardData(this.id);
    this.getPlayerLikes();

    if (localStorage.getItem("userId")) {
      this.statusRate();
    }
  },
  destroyed() {
    this.deleteCard();
  },
  computed: {
    ...mapState(["card", "comments", "profilePic", "username"]),
    commentErrors() {
      const errors = [];
      if (!this.$v.comentario.$dirty) return errors;
      !this.$v.comentario.minLength && errors.push("Mínimo 10 carácteres.");
      !this.$v.comentario.maxLength && errors.push("Máximo 255 carácteres.");
      return errors;
    },
    cdedo() {
      return {
        blue: this.valoracion == true,
        grey: this.valoracion == false
      };
    }
  },
  validations: {
    comentario: {
      required,
      minLength: minLength(10),
      maxLength: maxLength(255)
    }
  }
};
</script>

<style scoped>
.dedo:hover, .eliminar:hover {
  cursor: pointer;
}

.eliminar {
  color: red;
}
</style>

