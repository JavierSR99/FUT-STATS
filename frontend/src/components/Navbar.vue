<template>
  <div>
  <!-- la app bar contiene los enlaces a las diferentes vistas de la aplicación -->
    <v-app-bar color="verdesito" dense dark>
      <v-app-bar-nav-icon @click="drawer = !drawer">
        <font-awesome-icon :icon="['fas', 'bars']" size="2x"  />
      </v-app-bar-nav-icon>

      <v-toolbar-title class="px-5">
        <router-link id="enlace" :to="{name : 'Home'}">Home</router-link>
      </v-toolbar-title>

      <v-toolbar-title>
        <router-link id="enlace" :to="{name : 'Busqueda'}">Búsqueda</router-link>
      </v-toolbar-title>

      <v-toolbar-title class="px-5">
        <router-link id="enlace" :to="{name : 'Playlist'}">Playlist</router-link>
      </v-toolbar-title>

      <v-spacer></v-spacer> <!-- separador entre enlaces y el icono-->
 
      <!-- los iconos funcionan con etiqueta especial, al estar instalados como un componente -->
       <font-awesome-icon :icon="['fas', 'futbol']" size="lg" />

    </v-app-bar>

    <!-- el navigation drawer es el menú lateral izquierdo que aparece al hacer click en el icono superior de la derecha -->
    <v-navigation-drawer v-model="drawer" :color="color" absolute dark app>
      <!-- el v-list; es decir, el contenido del menú, es dinámico. Varía según se esté registrado o no-->
      <v-list dense nav class="py-0" v-if="username === 'Visitante'">
        <v-list-item two-line class="px-0">
          <v-list-item-avatar>
            <img src="../../../backend/img/perfil/defecto.png" />
          </v-list-item-avatar>

          <v-list-item-content>
            <v-list-item-title>{{username}}</v-list-item-title>
          </v-list-item-content>

          <v-list-item-avatar @click="drawer = !drawer">
            <font-awesome-icon :icon="['fas', 'angle-double-left']" size="lg" class="icono" />

            
            <!-- <v-icon class="icono">fas fa-angle-double-left</v-icon> -->
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

      <!-- v-list que aparece cuando se ha iniciado sesión, con sus enlaces correspondientes -->
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

        <!-- v-list de administrador, solo aparecerá cuando inicie sesión un administrador -->
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
      drawer: false, // el drawer sirve para mostrar el menú hamburguesa
      // en editperfil se pueden añadir enlaces a vistas de la app para que aparezcan dinámicamente
      editperfil: [{ title: "Editar perfil", to: 'Perfil' ,icon: "fas fa-user-circle" }],
      // visitante cumple la misma función que editperfil, pero en caso de usuario no registrado
      visitante: [
        { title: "Inicia sesión", icon: "fas fa-user-circle", to: "Login" },
        { title: "Registrate", icon: "far fa-smile", to: "Register" }
      ],
      color: "black"
    };
  },
  computed: {
    //datos importados de vuex
    ...mapState(["username", "email", "profilePic"])
  },
  methods: {
    ...mapMutations(['setUser', 'setProfilePic']),
    /**
     * cierra la sesión del usuario activo, destruyendo la sesión del localStorage
     */
    cerrarSesion() {
      this.setUser({username : 'Visitante', email : null}); //el username de vuex pasa a "visitante"
      this.$router.push({ name: 'Register' }); //el usuario vuelve a la vista de "registro"

      //eliminamos la sesión del localStorage
      localStorage.removeItem('username');
      localStorage.removeItem('email');
      localStorage.removeItem('userId');

      //en caso de que el que cierre la sesión sea un administrador, eliminamos su información exclusiva
      if (localStorage.getItem('admin')) {localStorage.removeItem('admin');}
      this.drawer = false; //cerramos el menú hamburguesa
      this.setProfilePic('defecto.png');
    }
  }
};
</script>


<style scoped>
/* #enlace corrresponde a los elementos del menú superior (enlaces a vistas) */
#enlace {
  color: white;
  text-decoration: none;
}

/* elementos del menú lateral izquierdo */
.menu {
  color: white;
  text-decoration: none;
}

/* hover al pasar por iconos del menú lateral izquierdo */
.icono:hover {
  cursor: pointer;
  color: blue;
}
</style>