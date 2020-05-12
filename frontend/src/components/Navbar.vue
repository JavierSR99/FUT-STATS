<template>
  <div>
    <v-app-bar color="verdesito" dense dark>
      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>

      <v-toolbar-title class="px-5">
        <router-link id="enlace" :to="{name : 'Home'}">Home</router-link>
      </v-toolbar-title>

      <v-toolbar-title>
        <router-link id="enlace" :to="{name : 'Busqueda'}">Búsqueda</router-link>
      </v-toolbar-title>

      <v-toolbar-title class="px-5">
        <router-link id="enlace" :to="{name : 'Playlist'}">Playlist</router-link>
      </v-toolbar-title>

      <v-spacer></v-spacer>

      <v-btn icon>
        <v-icon>fab fa-google</v-icon>
      </v-btn>
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" :color="color" absolute dark app>
      <v-list dense nav class="py-0" v-if="username === 'Visitante'">
        <v-list-item two-line class="px-0">
          <v-list-item-avatar>
            <img src="../../../backend/img/perfil/defecto.png" />
          </v-list-item-avatar>

          <v-list-item-content>
            <v-list-item-title>{{username}}</v-list-item-title>
          </v-list-item-content>

          <v-list-item-avatar @click="drawer = !drawer">
            <v-icon class="icono">fas fa-angle-double-left</v-icon>
          </v-list-item-avatar>
        </v-list-item>

        <v-divider></v-divider>

        <v-list-item v-for="item in visitante" :key="item.title" link>
          <v-list-item-icon>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title>
              <router-link class="menu" :to="{ name : item.to}">{{ item.title }}</router-link>
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>

      <v-list dense nav class="py-0" v-if="username !== 'Visitante'">
        <v-list-item two-line class="px-0">
          <v-list-item-avatar>
            <img :src="'http://localhost:80/FUT%20STATS/backend/img/perfil/'+profilePic" />
          </v-list-item-avatar>

          <v-list-item-content>
            <v-list-item-title>{{username}}</v-list-item-title>
          </v-list-item-content>

          <v-list-item-avatar @click="drawer = !drawer">
            <v-icon class="icono">fas fa-angle-double-left</v-icon>
          </v-list-item-avatar>
        </v-list-item>

        <v-divider></v-divider>
        <div v-if="username != 'Visitante' && username != 'ADMIN'">
        <v-list-item  v-for="item in editperfil" :key="item.title" link>
          <v-list-item-icon>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title>
              <router-link class="menu" :to="{ name : item.to}">{{ item.title }}</router-link>
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        </div>

        <v-divider></v-divider>

        <v-list-item>
          <v-list-item-content v-if="email === 'administrador@gmail.com'">
            <v-list-item-title>
              <router-link class="menu" :to="{ name : 'Admin'}">Administración</router-link>
            </v-list-item-title>
          </v-list-item-content>

          <v-list-item-content>
            <v-list-item-title class="menu icono" @click="cerrarSesion()">Cerrar sesión</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
  </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
  data() {
    return {
      drawer: false,
      editperfil: [{ title: "Editar perfil", to: 'Perfil' ,icon: "fas fa-user-circle" }],
      visitante: [
        { title: "Inicia sesión", icon: "fas fa-user-circle", to: "Login" },
        { title: "Registrate", icon: "far fa-smile", to: "Register" }
      ],
      color: "black"
    };
  },
  computed: {
    ...mapState(["username", "email", "profilePic"])
  },
  methods: {
    ...mapMutations(['setUser', 'setProfilePic']),
    cerrarSesion() {
      this.setUser({username : 'Visitante', email : null});
      this.$router.push({ name: 'Register' });
      localStorage.removeItem('username');
      localStorage.removeItem('email');
      localStorage.removeItem('userId');
      if (localStorage.getItem('admin')) {localStorage.removeItem('admin');}
      this.drawer = false;
      this.setProfilePic('defecto.png');
    }
  }
};
</script>

<style scoped>
#enlace {
  color: white;
  text-decoration: none;
}

.menu {
  color: white;
  text-decoration: none;
}

.icono:hover {
  cursor: pointer;
  color: blue;
}
</style>