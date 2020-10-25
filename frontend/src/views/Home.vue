//html de la página de bienvenida
<template>
  <v-container>
    <v-row class="home">
      <h1>Welcome to FUT STATS!</h1>
    </v-row>
    <v-row>
      <v-img src="../assets/prueba logo.png" lazy-src></v-img>
    </v-row>
    <v-row>
      <v-col md="7" xs="12">
        <v-carousel>
          <v-carousel-item
            v-for="(item,i) in carousel"
            :key="i"
            :src="'http://localhost/FUT%20STATS/backend/img/home/'+item.foto"
            reverse-transition="fade-transition"
            transition="fade-transition"
            cycle
          ></v-carousel-item>
        </v-carousel>
      </v-col>
      <v-col md="5" xs="12" class="text-center">
        <h2>Find your favorite player stats here!</h2>
        <p class="mt-5">
          FUT STATS provides you all the info about your favorite fifa player. You'll be able to look for in-game stats, position,
          nacionality and way more data. Also, create your own account and leave a comment in every card comments sections, let other
          players see if some card is as good or bad as it seems. Create a community, play, enjoy. 
        </p>
        <iframe width="460" height="270" src="https://www.youtube.com/embed/vgQNOIhRsV4"
        frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: "Home",
  data() {
    return {
      /* array con las imágenes del carrousel, si se añade el nombre una imagen que exista en el directorio correspondiente, aparecerá
      automáticamente en el carrousel */
      carousel: [
        {
          foto: "carro1.jpg"
        },
        {
          foto: "carro2.jpg"
        },
        {
          foto: "carro3.jpg"
        }
      ]
    };
  }
};
</script>
