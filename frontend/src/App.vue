<template>
  <v-app>
    <Navbar></Navbar>
    <v-content>
      <v-container>
        <router-view />
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
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
  async created () {
    if (localStorage.getItem('username')!=null && localStorage.getItem('email')!=null) {
      this.setUser({username : localStorage.getItem('username'), email:  localStorage.getItem('email')})
      this.cargarImgPerfil(localStorage.getItem("userId"))
    } else if (localStorage.getItem('admin')!=null) {
      this.setUser({username : localStorage.getItem('admin'), email : 'administrador@gmail.com'})
    }
  }
};
</script>
