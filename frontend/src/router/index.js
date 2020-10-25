/**
* Archivo principal para la configuración de las rutas de la aplicación
*/

import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'

Vue.use(VueRouter)

  //array que guardará todas las rutas
  const routes = [
  //ruta principal de la aplicación, la que aparece al iniciar la misma  
  {
    path: '/',
    name: 'Home',
    component: Home
  },

  // ruta de la vista de búsqueda de cartas de jugadores
  {
    path: '/search',
    name: 'Busqueda',
    component: () => import( '../views/Busqueda.vue')
  },

  //ruta a la vista del formulario de registro
  {
    path: '/register',
    name: 'Register',
    component: () => import( '../views/Register.vue')
  },

  //ruta a la vista del formulario de inicio de sesión
  {
    path: '/login',
    name: 'Login',
    component: () => import( '../views/Login.vue')
  },

  //ruta a la vista de información de una carta, es dinámica según la carta que se esté viendo
  {
    path: '/player-card/:id',
    name: 'Card',
    component: () => import( '../views/Card.vue')
  },

  //ruta a la vista del administrador, estará protegida para que solo el administrador pueda acceder
  {
    path: '/administración',
    name: 'Admin',
    component: () => import( '../views/Admin.vue')
  },

  //ruta a la vista de edición de perfil de usuario
  {
    path: '/mi-perfil',
    name: 'Perfil',
    component: () => import( '../views/Perfil.vue')
  },

  //ruta a la vista de la playlist con paginación
  {
    path: '/playlist',
    name: 'Playlist',
    component: () => import( '../views/Playlist.vue')
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
