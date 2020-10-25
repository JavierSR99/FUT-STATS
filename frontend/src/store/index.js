import Vue from 'vue'
import Vuex from 'vuex'
var axios = require('axios') //importamos axios para hacer llamadas al servidor

Vue.use(Vuex)

export default new Vuex.Store({
  // los state son equivalentes a la data
  state: {
    username: 'Visitante', // almacenará el nombre de usuario, asignado a visitante por defecto (al no haber sesión iniciada)
    email: null, // email del usuario que inicie sesión
    formData: null, // objeto formdata para poder mandar datos al servidor
    card : null, // información de la carta a la que el usuario quiere acceder
    comments : null, // array de comentarios de la carta a la que el usuario ha accedido
    profilePic : null // imagen de perfil del usuario que inicia sesión
  },

  // las mutations son equivalentes a computed
  mutations: {
    /**
     * asigna valores al nombre de usuario e email
     * @param {*} state username, email
     * @param {*} payload 
     */
    setUser(state, payload) {
      state.username = payload.username;
      state.email = payload.email;
    },

    /**
     * asigna valores que conformarán el objeto formData
     * @param {*} state formData
     * @param {*} payload 
     */
    setFormData(state, payload) {
      state.formData = payload;
    },

    /**
     * asigna la información de carta específica
     * @param {*} state card
     * @param {*} payload 
     */
    setCard (state, payload) {
      state.card = payload;
    },

    /**
     * asigna los comentarios a su state correspondiente
     * @param {*} state comments
     * @param {*} payload 
     */
    setComments (state,payload) {
      state.comments = payload;
    },

    /**
     * añade un comentario al array de commentarios
     * @param {*} state comments
     * @param {*} payload 
     */
    addComment (state, payload) {
      //si ya hay comentarios en el array...
      if (state.comments != null) {
        //se añade el comentario nuevo al inicio del array
        state.comments.unshift(payload);
      }
      // si el array estaba vacío o era null 
      else {
        //declaramos el array y añadimos los comentarios que han sido pasados como parámetros
        state.comments = [];
        state.comments.push(payload);
      }
    },

    /**
     * elimina la información de carta que estaba guardada
     * @param {*} state card
     */
    deleteCard (state) {
      state.card = null;
    },

    /**
     * asigna la imagen de perfil del usuario que ha iniciado sesión
     * @param {*} state profilePic
     * @param {*} payload nombre de la imagen de perfil
     */
    setProfilePic (state, payload) {
      state.profilePic = payload;
    }
  },

  // actions equivalentes a methods
  actions: {
    /**
     * crea el objeto formData con la información que se desea enviar al servidor
     * @param {*} param0
     * @param {*} payload 
     */
    createFormData({ commit }, payload) {
      let data = new FormData();
      for (const [index, item] in payload) {
        data.append(index, item);
      }
      commit('setFormData', data);
    },

    /**
     * llama al servidor para que le sea devuelto la información sobre la imagen de perfil del usuario que ha iniciado sesión
     * @param {*} param0 
     * @param {*} payload 
     */
    cargarImgPerfil ( {commit}, payload) {
      //creamos objeto formData y le añadimos la información a ser enviada
      let datos = new FormData();
      datos.append('func', 14);
      datos.append('cod_user', payload);

      // llamada asíncrona a servidor por medio de axios (en post)
      axios.post( axios.default.baseURL ,datos)
      //cuando hay respuesta...
      .then (res => {
        // se llama a la mutación setProfilePic para guardar la imagen de perfil
        commit('setProfilePic', res.data.pic);
      })
    }
  },
  modules: {
  }
})
