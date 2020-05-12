<template>
  <v-container>
    <v-row>
      <v-btn
        small
        color="verdesito"
        dark
        class="mx-2"
        @click="mostrar = 'liga'; allNations()"
      >Añadir liga</v-btn>
      <v-btn
        small
        color="verdesito"
        dark
        class="mx-2"
        @click="mostrar = 'equipo', allLeagues()"
      >Añadir equipo</v-btn>
      <v-btn
        small
        color="verdesito"
        dark
        class="mx-2"
        @click=" mostrar = 'jugador',allTeams(), allNations()"
      >Añadir jugador</v-btn>
      <v-btn small color="verdesito" dark class="mx-2">Añadir carta</v-btn>
    </v-row>
    <v-row class="text-center m-5">
      <v-alert v-if="error != null" type="error">{{error}}</v-alert>
      <v-alert v-if="mensaje != null" type="success">{{mensaje}}</v-alert>
    </v-row>
    <v-row v-if="mostrar === 'liga'">
      <v-col cols="12">
        <h1>Añade una liga a la base de datos</h1>
      </v-col>
      <v-col>
        <v-form @submit.prevent="guardarLiga">
          <v-row>
            <v-col md="4">
              <small>Fecha de creación</small>
              <v-date-picker v-model="fecha" color="verdesito"></v-date-picker>
            </v-col>
            <v-col md="8">
              <v-text-field
                label="Nombre de la liga"
                color="verdesito"
                v-model="$v.liga.$model"
                :error-messages="ligaErrors"
              ></v-text-field>
              <v-text-field
                v-model="nequipos"
                hide-details
                single-line
                type="number"
                label="Número de equipos"
                class="my-5"
                max="50"
                min="10"
                :error-messages="nequiposErrors"
              />
              <v-select
                :items="paises"
                filled
                label="País"
                color="verdesito"
                v-model="$v.pais.$model"
                :error-messages="paisesErrors"
              ></v-select>
              <v-btn big color="accent" dark class="mx-2" type="submit">Añadir liga</v-btn>
              <v-btn big color="danger" dark class="mx-2" type="reset" @click="resetear">Resetar</v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-col>
    </v-row>
    <v-row v-if="mostrar === 'equipo'">
      <v-col cols="12">
        <h1>Añade un equipo a la base de datos</h1>
      </v-col>
      <v-col>
        <v-form @reset="resetear" @submit.prevent="guardarEquipo">
          <v-row>
            <v-col md="4">
              <small>Fecha de fundación</small>
              <v-date-picker v-model="fecha" color="verdesito"></v-date-picker>
            </v-col>
            <v-col md="8">
              <v-text-field
                label="Nombre del equipo"
                color="verdesito"
                v-model="$v.equipo.$model"
                :error-messages="equipoErrors"
              ></v-text-field>
              <v-text-field
                label="Presidente"
                color="verdesito"
                v-model="$v.presidente.$model"
                :error-messages="presidenteErrors"
              ></v-text-field>
              <v-select
                :items="listaLigas"
                filled
                label="Liga"
                color="verdesito"
                v-model="$v.liga.$model"
              ></v-select>
              <v-btn big color="accent" dark class="mx-2" type="submit">Añadir equipo</v-btn>
              <v-btn big color="danger" dark class="mx-2" type="reset" @click="resetear">Resetar</v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-col>
    </v-row>
    <v-row v-if="mostrar === 'jugador'">
      <v-col cols="12">
        <h1>Añade un jugador a la base de datos</h1>
      </v-col>
      <v-col>
        <v-form @reset="resetear" @submit.prevent="guardarJugador">
          <v-row>
            <v-col md="4">
              <small>Fecha de nacimiento</small>
              <v-date-picker v-model="fecha" color="verdesito"></v-date-picker>
            </v-col>
            <v-col md="8">
              <v-text-field
                label="Nombre del jugador"
                color="verdesito"
                v-model="$v.nombre.$model"
                :error-messages="nombreErrors"
              ></v-text-field>
              <v-text-field
                label="Apellidos del jugador"
                color="verdesito"
                v-model="$v.apellidos.$model"
                :error-messages="apellidosErrors"
              ></v-text-field>
              <v-text-field
                label="Apodo del jugador"
                color="verdesito"
                v-model="$v.apodo.$model"
                :error-messages="apodoErrors"
              ></v-text-field>
              <v-select
                :items="listaEquipos"
                filled
                label="Equipo"
                color="verdesito"
                v-model="$v.equipo.$model"
              ></v-select>
              <v-select
                :items="paises"
                filled
                label="País"
                color="verdesito"
                v-model="$v.pais.$model"
                :error-messages="paisesErrors"
              ></v-select>
              <v-file-input
                label="Imagen jugador"
                color="verdesito"
                v-model="$v.fotojugador.$model"
                filled
                prepend-icon="fas fa-camera-retro"
              ></v-file-input>
              <v-btn big color="accent" dark class="mx-2" type="submit">Añadir jugador</v-btn>
              <v-btn big color="danger" dark class="mx-2" type="reset" @click="resetear">Resetar</v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-col>
    </v-row>
  </v-container>
</template>


<script>
import { required, minLength, maxLength } from "vuelidate/lib/validators";

export default {
  name: "Admin",
  created() {
    if (!localStorage.getItem("admin")) {
      this.$router.push({ name: "Home" });
    }
  },
  data() {
    return {
      error: null,
      mensaje: null,
      mostrar: "",
      paises: [],
      listaLigas: [],
      listaEquipos: [],
      liga: "",
      pais: "",
      fecha: new Date().toISOString().substr(0, 10),
      nequipos: "",
      equipo: null,
      presidente: null,
      nombre: null,
      apellidos: null,
      apodo: null,
      fotojugador: null
    };
  },
  methods: {
    allNations() {
      this.paises = [];
      let datos = new FormData();
      datos.append("func", 13);

      this.axios.post(this.axios.default.baseURL, datos).then(res => {
        this.paises = res.data;
      });
    },
    allLeagues() {
      this.listaLigas = [];
      let datos = new FormData();
      datos.append("func", 15);

      this.axios.post(this.axios.default.baseURL, datos).then(res => {
        this.listaLigas = res.data;
      });
    },
    allTeams() {
      this.listaEquipos = [];
      let datos = new FormData();
      datos.append("func", 16);

      this.axios.post(this.axios.default.baseURL, datos).then(res => {
        this.listaEquipos = res.data;
      });
    },
    guardarLiga() {
      if (
        this.$v.liga.required &&
        this.$v.liga.minLength &&
        this.$v.pais.required &&
        this.$v.nequipos.required &&
        this.$v.fecha.required
      ) {
        this.error = null;
        let datos = new FormData();
        datos.append("func", 6);
        datos.append("league", this.liga);
        datos.append("date", this.fecha);
        datos.append("teams", this.nequipos);
        datos.append("nacio", this.pais);

        this.axios.post(this.axios.default.baseURL, datos).then(res => {
          if (res.data.insercion == "error") {
            this.error = res.data.mensaje;
          } else {
            this.mensaje = res.data.mensaje;
          }
        });
      } else {
        this.error = "Faltan campos por completar o no cumples los requisitos";
      }
    },
    guardarEquipo() {
      if (
        this.$v.fecha.required &&
        this.$v.presidente.required &&
        this.$v.presidente.minLength &&
        this.$v.equipo.required &&
        this.$v.equipo.minLength &&
        this.$v.liga.required
      ) {
        let datos = new FormData();
        datos.append("func", 7);
        datos.append("league", this.liga);
        datos.append("date", this.fecha);
        datos.append("teamname", this.equipo);
        datos.append("president", this.presidente);

        this.axios.post(this.axios.default.baseURL, datos).then(res => {
          if (res.data.insercion == "error") {
            this.error = res.data.mensaje;
          } else {
            this.mensaje = res.data.mensaje;
            this.resetear();
          }
        });
      } else {
        this.error = "Faltan campos por completar o no cumples los requisitos";
      }
    },
    guardarJugador() {
      if (
        this.$v.nombre.required &&
        this.$v.nombre.minLength &&
        this.$v.apellidos.required &&
        this.$v.apellidos.minLength &&
        this.$v.apodo.required &&
        this.$v.apodo.minLength &&
        this.$v.equipo.required &&
        this.$v.fecha.required &&
        this.$v.pais.required &&
        this.$v.fotojugador.required
      ) {
        let datos = new FormData();
        datos.append("func", 8);
        datos.append("player", this.nombre);
        datos.append("splayer", this.apellidos);
        datos.append("nickname", this.apodo);
        datos.append("date", this.fecha);
        datos.append("teamname", this.equipo);
        datos.append("nacio", this.pais);
        datos.append("playerimg", this.fotojugador);

        this.axios.post(this.axios.default.baseURL, datos).then(res => {
          if (res.data.insercion === "ok") {
            this.mensaje = res.data.mensaje;
            this.resetear();
          } else {
            this.error = res.data.mensaje;
          }
        });
      } else {
        this.error = "Faltan campos por completar o no cumples los requisitos";
      }
    },
    resetear() {
      this.error = null;
      this.mensaje = null;
      this.liga = "";
      this.pais = "";
      this.nequipos = "";
      this.equipo = null;
      this.presidente = null;
      this.nombre = null;
      this.apellidos = null;
      this.apodo = null;
      this.fotojugador = null;
    }
  },
  validations: {
    liga: { required, minLength: minLength(6) },
    pais: { required },
    fecha: { required },
    nequipos: { required },
    equipo: { required, minLength: minLength(10) },
    presidente: { required, minLength: minLength(10) },
    nombre: { required, minLength: minLength(5) },
    apellidos: { required, minLength: minLength(5) },
    apodo: { required, minLength: minLength(5) },
    fotojugador: { required }
  },
  computed: {
    ligaErrors() {
      const errors = [];
      if (!this.$v.liga.$dirty) return errors;
      !this.$v.liga.minLength && errors.push("Mínimo 6 carácteres.");
      !this.$v.liga.required && errors.push("Este campo es obligatorio");
      return errors;
    },
    nequiposErrors() {
      const errors = [];
      if (!this.$v.nequipos.$dirty) return errors;

      !this.$v.nequipos.required && errors.push("Este campo es obligatorio");
      return errors;
    },
    paisesErrors() {
      const errors = [];
      if (!this.$v.pais.$dirty) return errors;
      !this.$v.pais.required && errors.push("Este campo es obligatorio");
      return errors;
    },
    equipoErrors() {
      const errors = [];

      if (!this.$v.equipo.$dirty) return errors;
      !this.$v.equipo.minLength && errors.push("Mínimo 10 carácteres.");
      !this.$v.equipo.required && errors.push("Este campo es obligatorio");

      return errors;
    },
    presidenteErrors() {
      const errors = [];

      if (!this.$v.presidente.$dirty) return errors;
      !this.$v.presidente.minLength && errors.push("Mínimo 10 carácteres.");
      !this.$v.presidente.required && errors.push("Este campo es obligatorio");

      return errors;
    },
    nombreErrors() {
      const errors = [];

      if (!this.$v.nombre.$dirty) return errors;
      !this.$v.nombre.minLength && errors.push("Mínimo 5 carácteres.");
      !this.$v.nombre.required && errors.push("Este campo es obligatorio");

      return errors;
    },
    apellidosErrors() {
      const errors = [];

      if (!this.$v.apellidos.$dirty) return errors;
      !this.$v.apellidos.minLength && errors.push("Mínimo 5 carácteres.");
      !this.$v.apellidos.required && errors.push("Este campo es obligatorio");

      return errors;
    },
    apodoErrors() {
      const errors = [];

      if (!this.$v.apodo.$dirty) return errors;
      !this.$v.apodo.minLength && errors.push("Mínimo 5 carácteres.");
      !this.$v.apodo.required && errors.push("Este campo es obligatorio");

      return errors;
    }
  }
};
</script>