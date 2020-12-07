<template>
  <v-row>
    <v-col cols="12" class="text-center">
      <h1>¡REGISTRATE AHORA!</h1>
    </v-col>
    <v-col cols="12">
      <v-alert type="error" v-if="error != null">{{error}}</v-alert>
    </v-col>
    <v-col>
      <v-form ref="form" method="post" @submit.prevent="validate()">
        <v-text-field
          label="Nombre de usuario"
          :color="color"
          v-model="$v.username.$model"
          :error-messages="nameErrors"
          required
        ></v-text-field>

        <v-text-field
          type="email"
          label="Email"
          required
          v-model="$v.email.$model"
          :error-messages="emailErrors"
          :color="color"
        ></v-text-field>

        <v-text-field
          label="Contraseña"
          v-model="$v.password.$model"
          :error-messages="passwordErrors"
          :color="color"
          type="password"
          required
        ></v-text-field>

        <v-text-field
          label="Confirma la contraseña"
          v-model="$v.repeatPassword.$model"
          :error-messages="repeatPassErrors"
          type="password"
          required
          :color="color"
        ></v-text-field>
        <v-btn small class="mr-5" type="submit">Registrarse</v-btn>
        <v-btn small color="error" type="reset" @click="resetData">Borrar</v-btn>
      </v-form>
    </v-col>
  </v-row>
  
</template>

<script>
import {
  required,
  email,
  minLength,
  maxLength,
  sameAs
} from "vuelidate/lib/validators";

import { mapMutations } from 'vuex'

export default {
  name: "Register",
  data() {
    return {
      color: "verdesito",
      username: "",
      email: "",
      password: "",
      repeatPassword: "",
      error: null
    };
  },
  validations: {
    email: { required, email },
    username: { required, minLength: minLength(6), maxLength: maxLength(20) },
    password: { required, minLength: minLength(8) },
    repeatPassword: { required, sameAsPassword: sameAs("password") }
  },
  computed: {
    emailErrors() {
      const errors = [];
      if (!this.$v.email.$dirty) return errors;
      !this.$v.email.email && errors.push("El formato del email es erróneo");
      !this.$v.email.required && errors.push("El email es obligatorio");
      return errors;
    },
    nameErrors() {
      const errors = [];
      if (!this.$v.username.$dirty) return errors;
      !this.$v.username.minLength && errors.push("Mínimo 6 carácteres.");
      !this.$v.username.maxLength && errors.push("Máximo 20 carácteres.");
      !this.$v.username.required &&
        errors.push("El nombre de usuario es obligatorio.");
      return errors;
    },
    passwordErrors() {
      const errors = [];
      if (!this.$v.password.$dirty) return errors;
      !this.$v.password.minLength &&
        errors.push("La contraseña debe tener mínimo 8 caracteres.");
      !this.$v.password.required &&
        errors.push("La contraseña es obligatoria.");
      return errors;
    },
    repeatPassErrors() {
      const errors = [];
      if (!this.$v.repeatPassword.$dirty) return errors;
      !this.$v.repeatPassword.sameAsPassword &&
        errors.push("Las contraseñas no coinciden.");
      return errors;
    }
  },
  methods: {
      ...mapMutations(["setUser"]),
    validate() {
      if (
        this.$v.password.minLength &&
        this.$v.repeatPassword.sameAsPassword &&
        this.$v.username.minLength &&
        this.$v.username.maxLength &&
        this.$v.username.required &&
        this.$v.email.email &&
        this.$v.email.required
      ) {
        this.sendRegister();
      } else {
        this.error = 'Faltan datos';
      }
    },
    sendRegister() {
      this.axios
        .post(
          this.axios.default.baseURL,
          this.createFormData()
        )
        .then(res => {
            console.log(res.data);
          if (res.data.insercion == "correcta") {
              localStorage.setItem('username', res.data.mensaje.user);
              localStorage.setItem('email', res.data.mensaje.email);
              localStorage.setItem('userId', res.data.mensaje.cod);
            this.setUser({
              username : res.data.mensaje.user,
              email : res.data.mensaje.email
            });
            this.$router.push({ name: "Home" });
          } else {
            this.error = res.data.mensaje;
          }
        })
        .catch(err => {
          console.log(err);
        });
    },
    createFormData() {
      let data = new FormData();
      data.append("username", this.username);
      data.append("email", this.email);
      data.append("password", this.password);
      data.append("func", 0);

      return data;
    },
    resetData () {
        this.username = '';
        this.email = '';
        this.password = '';
        this.repeatPassword = '';
        this.error = null;
    }
  }
};
</script>