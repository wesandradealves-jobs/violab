<template>
  <div v-if="!isInit">
    <section v-if="'' !== $store.state.home.acf.banner" id="webdoor" class="webdoor">
      <div class="container">
        <carousel
          :paginationEnabled="false"
          :navigationEnabled="false"
          :perPage="1"
          :autoplay="true"
          :autoplayTimeout="6000"
          :autoplayHoverPause="true"
          :mouse-drag="false">
          <slide
            class="inner-item"
            v-for="banner in $store.state.home.acf.banner"
            v-bind:key="banner.id"
          >
            <img width="100%" :src="banner.imagem" />
          </slide>
        </carousel>
      </div>
    </section>
    <section
      v-if="'' !== $store.state.shop.products"
      class="loja"
      id="loja"
      data-anchor="loja">
      <div class="container">
        <h2>Loja</h2>
        <div class="product-list">
          <div
            v-for="product in $store.state.shop.products"
            v-if="product.acf.destaque&&product.acf.habilitar"
            v-bind:key="product.id">
            <div
              v-if="product.thumbnail"
              :style="{ backgroundImage: 'url(' + product.thumbnail + ')' }"
              class="thumbnail"
            />
            <div>
              <h3 v-html="product.title.rendered" />
              <h5 v-if="product.acf.album">
                Autor: {{product.acf.album_info.autor}}<br>
                Gravadora: {{product.acf.album_info.gravadora}} - Ano de Lançamento: {{product.acf.album_info.ano_de_lancamento}}

                <br><br>
              </h5>              
              <h4 class="product-price">
                <span v-if="!product.acf.produto_gratis">
                  R${{ product.acf.preco.replace('.',',') }}
                </span>
                <span v-else>
                  Cortesia
                </span>
              </h4>
              <p
                v-html="product.excerpt.rendered"
                v-if="'' !== product.excerpt.rendered"
              />
              <router-link class="btn btn-1" :title="product.name" :to="{ path: `loja/${product.id}` }">
                Ver detalhes
              </router-link>              
            </div>
          </div>
        </div>
        <div v-if="'' !== $store.state.header.bloginfo.album_id">
          <iframe
            style="width:100%;height:100%"
            :src="
              'https://open.spotify.com/embed/album/' +
                $store.state.header.bloginfo.album_id
            "
            width="300"
            height="380"
            frameborder="0"
            allowtransparency="true"
            allow="encrypted-media"
          />
        </div>
      </div>
    </section>
    <section
      v-if="$store.state.home.acf.movimento"
      data-anchor="o-que-e"
      id="o-que-e"
      class="movimento"
      :style="{
        backgroundImage: 'url(' + $store.state.home.acf.movimento.background + ')'
      }">
      <div class="container">
        <div class="movimento-text-wrap">
          <h2>O que é</h2>
          <div
            class="movimento-text"
            v-if="'' !== $store.state.home.acf.movimento.texto"
            v-html="$store.state.home.acf.movimento.texto"
          />
        </div>
        <div
          class="video-list"
          v-if="'' !== $store.state.home.acf.movimento.videos"
        >
          <iframe v-bind:key="video.id" v-for="video in $store.state.home.acf.movimento.videos" :src="`https://www.youtube.com/embed/${video.video_id}`" frameborder="0"></iframe>
        </div>
      </div>
    </section>
    <section 
      id="conteudo" 
      v-if="'' !== $store.state.blog.posts" 
      data-anchor="conteudo" 
      class="artigos">
      <div class="container">
        <h2>Conteúdo</h2>
        <carousel
          :paginationEnabled="false"
          :navigationEnabled="true"
          :perPageCustom="[[1024, 4], [814, 2], [737, 1]]"
          :mouse-drag="false"
        >
          <slide
            class="inner-item"
            v-for="post in $store.state.blog.posts"
            v-if="post.destaque"
            :key="post.id"
          >
            <div class="inner-info" @click="$router.replace(`conteudo/${post.id}`)">
              <div
                class="thumbnail"
                v-if="post.thumbnail"
                :style="{ backgroundImage: 'url(' + post.thumbnail + ')' }"
              >
                <div
                  class="thumbnail -zoom"
                  :style="{ backgroundImage: 'url(' + post.thumbnail + ')' }"
                />
              </div>
              <h3>{{ post.title.rendered }}</h3>
            </div>
          </slide>
        </carousel>
        <router-link :to="{ path: '/conteudo' }">
          Ver tudo &#8594;
        </router-link>
      </div>
    </section>
  </div>
</template>
<script>
import Vue from "vue";
import router from "@/router";
import { Carousel, Slide } from "vue-carousel";
export default {
  name: "Home",
  components: {
    Carousel,
    Slide
  },
  props: ["isInit","content"],
  methods: {
    formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
      try {
        decimalCount = Math.abs(decimalCount);
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

        const negativeSign = amount < 0 ? "-" : "";

        let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
        let j = (i.length > 3) ? i.length % 3 : 0;

        return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
      } catch (e) {
        console.log(e)
      }
    },
    addToCart(itemId, itemDescription, itemAmount, itemQuantity = 1) {
      var prod = {
        itemId: itemId,
        itemDescription: itemDescription,
        itemAmount: this.formatMoney(itemAmount),
        itemQuantity: itemQuantity
      };

      if (this.$store.state.shop.app_cart.some(prod => prod.itemId === itemId)) {
        this.$store.state.status_message.message = "Você já adicionou este produto.";

        this.$store.state.status_message.code = 0;
      } else {
        this.$store.state.shop.app_cart.push(prod);
      }
    }
  },
  computed: {
    vm() {
      return vm;
    }
  }
};
</script>
<style lang="sass">
@import './sass/_Home'
</style>
