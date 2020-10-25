<template>
  <v-app>
    <!-- el componente Navbar aparecerá en todas las vistas de la aplicación, se inserta aquí para no hacerlo manualmente
    en todas las vistas independientes -->
    <Navbar></Navbar>

    <!-- v-content contendrá las vistas de la aplicación -->
    <v-content>
      <v-container>
        <router-view />
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
//importamos componente Navbar para poder usarlo y utensilios de vuex con informaciónn que será requerida
import Navbar from '@/components/Navbar.vue'
import {mapMutations, mapActions} from 'vuex'
export default {
  name: 'App',
  components: {
    Navbar
  },
  data: () => ({
    //
  }),
  methods : {
    ...mapMutations(['setUser']),
    ...mapActions(['cargarImgPerfil'])
  },
  /**
   * cuando se crea la vista (sea cual sea), valida si hay una sesión iniciado, sea como usuario común o como administrador
   */
  async created () {
    // si en el localStorage está guardada la sesión de usuario (username y el email)...
    if (localStorage.getItem('username')!=null && localStorage.getItem('email')!=null) {
      // utilizamos las mutaciones de vuex para guardar y utilizar la información de usuario
      this.setUser({username : localStorage.getItem('username'), email:  localStorage.getItem('email')})
      /* utilizamos el id de usuario almacenado en el localStorage para usar la mutación de vuex que llama al servidor
       para cargar la foto de perfil del usuario */
      this.cargarImgPerfil(localStorage.getItem("userId"))
    }
    // si no se ha iniciado sesión como usuario, puede que un administrador haya iniciado sesión previamente
    else if (localStorage.getItem('admin')!=null) {
      //utilizamos una mutación de vuex para tratar la info de administrador
      this.setUser({username : localStorage.getItem('admin'), email : 'administrador@gmail.com'})
    }
  }
};
</script>
