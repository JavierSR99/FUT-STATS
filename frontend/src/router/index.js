import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'

Vue.use(VueRouter)

  const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/search',
    name: 'Busqueda',
    component: () => import( '../views/Busqueda.vue')
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import( '../views/Register.vue')
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import( '../views/Login.vue')
  },
  {
    path: '/player-card/:id',
    name: 'Card',
    component: () => import( '../views/Card.vue')
  },
  {
    path: '/administración',
    name: 'Admin',
    component: () => import( '../views/Admin.vue')
  },
  {
    path: '/mi-perfil',
    name: 'Perfil',
    component: () => import( '../views/Perfil.vue')
  },
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
