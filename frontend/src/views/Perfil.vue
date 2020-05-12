<template>
  <v-container v-if="profilePic != null">
    <v-row>
      <v-col cols="12" class="text-center">
        <h1>Personaliza tu perfil</h1>
        <v-alert type="warning" v-if="error != null && tiempo == true">{{error}}</v-alert>
        <v-alert type="success" v-if="mensaje != null && tiempo == true">{{mensaje}}</v-alert>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" class="text-center">
        <v-avatar color="teal" size="48">
          <img :src="'http://localhost:80/FUT%20STATS/backend/img/perfil/'+profilePic" />
        </v-avatar>
        <h3>{{username}}</h3>
      </v-col>
      
      <v-col cols="12">
        <v-form @reset="resetData" @submit.prevent="cambiarFotoDePerfil">
          <v-file-input label="File input" color="verdesito" v-model="fotodeperfil"
           filled prepend-icon="fas fa-camera-retro"></v-file-input>
          <v-btn small class="mr-5" type="submit">Cambiar imagen</v-btn>
        </v-form>
      </v-col>
      <br>
      <v-col class="mt-4">
        <h2>Cambia tu nombre de usuario</h2>
        <v-form @submit.prevent="cambiarUsername">
          <v-text-field
            label="Usuario actual"
            v-model="$v.user.$model"
            color="verdesito"
            :error-messages="userErrors"
          ></v-text-field>
          <v-text-field
            label="Nuevo usuario"
            v-model="$v.newusername.$model"
            color="verdesito"
            :error-messages="newuserErrors"
          ></v-text-field>
          <v-btn small class="mr-5" type="submit">Cambiar usuario</v-btn>
          <v-btn small color="error" type="reset" @click="resetData">Borrar</v-btn>
        </v-form>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { required, minLength, maxLength } from "vuelidate/lib/validators";
import { mapMutations, mapActions, mapState } from "vuex";

export default {
  name: "Perfil",
  data() {
    return {
      user: "",
      newusername: "",
      error: null,
      mensaje: null,
      tiempo: false,
      fotodeperfil : null
    };
  },
  validations: {
    user: { required },
    newusername: {
      required,
      minLength: minLength(10),
      maxLength: maxLength(50)
    }
  },
  computed: {
    ...mapState(["username", "profilePic"]),
    userErrors() {
      const errors = [];
      if (!this.$v.user.$dirty) return errors;
      !this.$v.user.required &&
        errors.push("El nombre de usuario es obligatorio.");
      return errors;
    },
    newuserErrors() {
      const errors = [];
      if (!this.$v.newusername.$dirty) return errors;
      !this.$v.newusername.minLength && errors.push("Mínimo 10 carácteres.");
      !this.$v.newusername.maxLength && errors.push("Máximo 50 carácteres.");
      !this.$v.newusername.required &&
        errors.push("El nuevo nombre de usuario es obligatorio.");
      return errors;
    },
    createFormData() {
      let data = new FormData();
      data.append("func", 2);
      data.append("username", this.user);
      data.append("newusername", this.newusername);

      return data;
    }
  },
  methods: {
    ...mapMutations(["setUser", "setProfilePic"]),
    ...mapActions(["cargarImgPerfil"]),
    resetData() {
      this.user = "";
      this.newusername = "";
      this.fotodeperfil = null;
    },
    cambiarUsername() {
      if (
        this.$v.user.required &&
        this.$v.newusername.required &&
        this.$v.newusername.minLength &&
        this.$v.newusername.maxLength
      ) {
        let data = this.createFormData;

        this.axios.post(this.axios.default.baseURL, data).then(res => {
          if (res.data.cambio === "fallido") {
            this.tiempo = true;
            this.error = res.data.mensaje;
            setTimeout(() => {
              this.tiempo = false;
            }, 4000);
          } else {
            this.setUser({
              username: this.newusername,
              email: localStorage.getItem("email")
            });
            this.tiempo = true;
            this.mensaje = res.data.mensaje;
            setTimeout(() => {
              this.tiempo = false;
            }, 4000);
            this.resetData();
          }
        });
      } else {
        this.tiempo = true;
        this.error =
          "Rellena todos los campos para solicitar un cambio de nombre de usuario.";
        setTimeout(() => {
          this.tiempo = false;
        }, 3000);
      }
    },
    /**
     * solicita al servidor un cambio de foto de perfil del usuario actual
     */
    cambiarFotoDePerfil () {
      if (this.fotodeperfil != null) {
        let datos = new FormData();
        datos.append('func', 3);
        datos.append('username', localStorage.getItem('username'));
        datos.append('picture',this.fotodeperfil);
        datos.append('cod_user', localStorage.getItem('userId'));

        this.axios.post(this.axios.default.baseURL, datos)
        .then(res => {
          if (res.data.cambio == 'correcto') {
            this.setProfilePic(localStorage.getItem('userId')+'.png');
            this.tiempo = true;
            this.mensaje = res.data.mensaje;
            setTimeout(() => {
              this.tiempo = false;
              this.mensaje = null;
            }, 2500);
            this.resetData();
          } else {
            this.tiempo = true;
            this.error = res.data.mensaje;
            setTimeout(() => {
              this.tiempo = false;
              this.error = null;
            }, 2500);
          }
        })
      } else {
        this.tiempo = true;
            this.error = "El formato de imagen no es válido o no has seleccionado una imagen";
            setTimeout(() => {
              this.tiempo = false;
              this.error = null;
            }, 2500);
      }
    }
  },
  created() {
    if (localStorage.getItem('userId')) {
      this.cargarImgPerfil(localStorage.getItem("userId"));
    } else  {
      this.$router.push({ name : 'Home'});
    }
  }
};
</script>