<template>
  <div>
    <v-col cols="12" class="text-center">
      <h1>INICIO DE SESIÓN</h1>
    </v-col>
    <v-col cols="12">
      <v-alert type="error" v-if="error != null">{{error}}</v-alert>
    </v-col>
    <v-col>
      <v-form method="post" @submit.prevent="logueo">
        <v-text-field
          label="Email"
          :color="color"
          v-model="$v.email.$model"
          :error-messages="emailErrors"
          required
        ></v-text-field>

        <v-text-field
          label="Contraseña"
          v-model="$v.password.$model"
          :color="color"
          type="password"
          :error-messages="passErrors"
          required
        ></v-text-field>
        <v-btn small class="mr-5" type="submit">Iniciar sesión</v-btn>
        <v-btn small color="error" type="reset" @click="reset">Borrar</v-btn>
      </v-form>
    </v-col>
  </div>
</template>

<script>
import { required, email } from "vuelidate/lib/validators";
import { mapMutations } from 'vuex'

export default {
  name: "Login",
  data() {
    return {
      email: "",
      password: "",
      color: "verdesito",
      error: null
    };
  },
  methods: {
      ...mapMutations(["setUser", "setProfilePic"]),
    reset() {
      this.username = "";
      this.clave = "";
      error = "";
    },
    logueo() {
      if (
        this.$v.email.required &&
        this.$v.password.required &&
        this.$v.email.email
      ) {
        let data = new FormData();
        data.append("func", 1);
        data.append("email", this.email);
        data.append("password", this.password);
        this.axios
          .post(this.axios.default.baseURL, data)
          .then(res => {
            if (res.data.logueo === "correcto") {
              if (res.data.usuario.usuario === "ADMIN") {
                localStorage.setItem('admin', res.data.usuario.usuario);
                this.$router.push({ name: "Admin" });
                this.setUser({
              username : res.data.usuario.usuario,
              email : 'administrador@gmail.com'
            });
            this.setProfilePic(res.data.usuario.img)
              } else {
                localStorage.setItem("username", res.data.usuario.username);
              localStorage.setItem("email", res.data.usuario.email);
              localStorage.setItem("userId", res.data.usuario.cod)
              this.setUser({
              username : res.data.usuario.username,
              email : res.data.usuario.email
            });
            this.setProfilePic(res.data.usuario.img)
            this.$router.push({ name: "Home" });
              }
            } else {
              this.error = res.data.mensaje;
            }
          })
          .catch(err => {
            console.log(err);
          });
      } else {
        this.error = "Faltan datos o el formato es erróneo";
      }
    }
  },
  validations: {
    email: { required, email },
    password: { required }
  },
  computed: {
    emailErrors() {
      const errors = [];
      if (!this.$v.email.$dirty) return errors;
      !this.$v.email.email && errors.push("El formato del email es erróneo");
      !this.$v.email.required && errors.push("El email es obligatorio");
      return errors;
    },
    passErrors() {
      const errors = [];
      if (!this.$v.password.$dirty) return errors;
      !this.$v.password.required &&
        errors.push("La contraseña es obligatoria.");
      return errors;
    }
  },
  created () {
    if (localStorage.getItem('username')) {
      this.$router.push({ name: "Home" });
    }
  }
};
</script>