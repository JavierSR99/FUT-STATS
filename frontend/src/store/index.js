import Vue from 'vue'
import Vuex from 'vuex'
var axios = require('axios')

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    username: 'Visitante',
    email: null,
    formData: null,
    card : null,
    comments : null,
    profilePic : null
  },
  mutations: {
    setUser(state, payload) {
      state.username = payload.username;
      state.email = payload.email;
    },
    setFormData(state, payload) {
      state.formData = payload;
    },
    setCard (state, payload) {
      state.card = payload;
    },
    setComments (state,payload) {
      state.comments = payload;
    },
    addComment (state, payload) {
      if (state.comments != null) {
        state.comments.unshift(payload);
      } else {
        state.comments = [];
        state.comments.push(payload);
      }
    },
    deleteCard (state) {
      state.card = null;
    },
    setProfilePic (state, payload) {
      state.profilePic = payload;
    }
  },
  actions: {
    createFormData({ commit }, payload) {
      let data = new FormData();
      for (const [index, item] in payload) {
        data.append(index, item);
      }
      commit('setFormData', data);
    },
    cargarImgPerfil ( {commit}, payload) {
      let datos = new FormData();
      datos.append('func', 14);
      datos.append('cod_user', payload);

      axios.post( axios.default.baseURL ,datos)
      .then (res => {
        commit('setProfilePic', res.data.pic);
      })
    }
  },
  modules: {
  }
})
