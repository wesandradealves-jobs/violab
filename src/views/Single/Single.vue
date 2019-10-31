<template>
  <section v-if="vm._data.isInit">
    <div class="container">
      <page-header
        :titulo="vm._data.post.title.rendered"
        :postType="$router.history.current.meta.post_type"
        :prevRoute="vm._data.prevRoute"
      />
      <div class="post-content" :class="'template-' + $router.history.current.meta.post_type">
        <img
          :src="vm._data.post.thumbnail"
          v-if="vm._data.post.thumbnail&&vm._data.post.categories[0]!=='videoaula'"
          :alt="vm._data.post.title.rendered"
        />

        <div v-else class="preview">
          <iframe :src="vm._data.videoPreview" frameborder="0"></iframe>
        </div>

        <div v-if="'' !== vm._data.post.content.rendered">
          <h3 v-if="$router.history.current.meta.post_type === 'produtos'" v-html="vm._data.post.title.rendered" />
          <h5 v-if="vm._data.post.acf.album">
            Autor: {{vm._data.post.acf.album_info.autor}}<br>
            Gravadora: {{vm._data.post.acf.album_info.gravadora}} - Ano de Lançamento: {{vm._data.post.acf.album_info.ano_de_lancamento}}

            <br><br>
          </h5>
          <h4 
            v-if="$router.history.current.meta.post_type === 'produtos'" 
            class="product-price">
            <span v-if="!vm._data.post.acf.produto_gratis">
              <small>Por apenas</small> R${{ vm._data.post.acf.preco.replace('.',',') }}
            </span>
            <span v-else>
              Cortesia
            </span>
          </h4>

          <div :class="($router.history.current.meta.post_type === 'produtos') ? 'description' : ''" v-html="post_content"/>

          <div v-if="vm._data.post.acf.album&&vm._data.tracklist">
            <span class="tracklist-title">
              <strong>Tracklist (.{{ext}}):</strong> 
              <span class="extensionSwitcher">
                <span>
                  <input checked="checked" type="radio" v-model="extension" value="wav">
                  <label>WAV</label>
                </span>
                <span>
                  <input type="radio" v-model="extension" value="mp3">
                  <label>MP3</label>
                </span>
              </span>
            </span>
            <ul class="tracklist">
              <li :key="track.id" :id="'track_' + track.id" :class="!track.audios[ext].previa ? 'disabled' : ''" v-for="track in vm._data.tracklist">
                <h4 class="track-title">{{track.title}}</h4>
                <audio :class="!track.audios[ext].previa ? 'disabled' : ''" controls :src="!track.audios[ext].previa ? 'disabled' : track.audios[ext].previa">
                  {{track.audios[ext].previa}}
                </audio>
                <span class="track-price" v-if="track.audios[ext].preco">
                  R$ {{track.audios[ext].preco}}
                </span>
                <a 
                  @click="addToCart(track.id, track.title + '(.'+ext+')', track.audios[ext].preco, track.audios[ext].arquivo)"
                  class="track-to-cart"
                  v-if="track.audios[ext].preco"
                ><i class="fas fa-shopping-cart"></i></a>
              </li>
            </ul>
          </div>
          <a
            :title="vm._data.post.title.rendered"
            class="btn btn-1"
            :class="vm._data.inCart ? '-disabled' : ''"
            v-if="!vm._data.post.acf.album&&$router.history.current.meta.post_type === 'produtos'&&!vm._data.post.acf.produto_gratis"
            @click="addToCart(vm._data.post.id, vm._data.post.title.rendered, (vm._data.post.acf.produto_gratis ? '0,00' : vm._data.post.acf.preco), vm._data.post.acf.arquivo)"
            >Adicionar <span v-if="vm._data.post.acf.album">álbum</span> ao carrinho
          </a>
          <a 
            :title="vm._data.post.title.rendered"
            class="btn btn-1"
            id="emailFieldHandler"
            @click="_emailField"
            v-if="$router.history.current.meta.post_type === 'produtos'&&vm._data.post.acf.produto_gratis"
          >Quero receber o conteúdo</a>
          <form 
            @mouseleave="_emailField" 
            v-if="emailField" 
            class="emailField" 
            @submit.prevent="_sendMail(vm._data.post)"
          >
            <input @keyup="_sendMail(vm._data.post)" @blur="_emailField" v-model="emailProductField" type="text" placeholder="email@domínio.com" />
          </form>
        </div>
      </div>
    </div>
  </section>
</template>
<script>
import Vue from "vue";
import store from "@/store";
import router from "@/router";
import pageHeader from "@/components/pageHeader/pageHeader.vue";
import axios from 'axios';
import VueAxios from 'vue-axios';

var emojiStrip = require('emoji-strip');

const VueHead = require("vue-head");

Vue.use(VueHead, VueAxios, axios);

var vm = new Vue({
  data: {
    post: [],
    tracklist: [],
    videoPreview: [],
    prevRoute: null,
    inCart: false,
    isInit: false
  }
});
export default {
  name: "Single",
  data() {
    return {
      emailField: false,
      emailProductField: "",
      title: "",
      timeout: null,
      id: this.$router.history.current.params.id,
      extension: 'wav',
      meta: {
        name: "",
        description: "",
        ogImage: ""
      }
    };
  },
  methods: {
    setMessage(code, msg) {
      var self = this;

      self.$store.state.status_message.message = msg;
      self.$store.state.status_message.code = code;
    },    
    _sendMail(data) {
      var self = this;

      if(data) {
        clearTimeout(self.timeout);

        self.timeout = setTimeout(function () {
            self.$store.state.isLoading = true;

            var prod = {
              'title':data.title.rendered,
              'info':data.acf.album_info,
              'url':data.acf.arquivo_personalizado
            };

            axios.get(`http://dev.uppercreative.com.br/phpmailer/send.php?prod=${JSON.stringify(prod)}`)
            .then(function(res) {
              self.$store.state.isLoading = false;
              self.setMessage(1, 'Seus arquivos foram enviados com sucesso.');
            })
            .catch(function(err) {
              self.$store.state.isLoading = false;
              self.setMessage(0, 'Seu formulário não pode ser enviado.');
            }); 
        }, 1000);
      }
    },
    _emailField(err) {
        this.emailField = !this.emailField;

        if(this.$store.state.isLoading)
          this.$store.state.isLoading = false;
        if(this.emailProductField) 
          this.emailProductField = '';
    },
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
    addToCart(itemId, itemDescription, itemAmount, itemUrl, itemQuantity = 1) {
      var paypalObj = {
        name: itemDescription,
        sku: itemId,
        price: parseFloat(itemAmount.replace(/,/g, '.')).toFixed(2),
        currency: 'BRL',
        quantity: itemQuantity
      };

      console.log(this.$store.state.shop.paypal_cart);

      var prod = {
        itemId: itemId,
        itemDescription: itemDescription,
        itemAmount: parseFloat(itemAmount.replace(/,/g, '.')).toFixed(2),
        itemQuantity: itemQuantity 
      };

      if (this.$store.state.shop.app_cart.some(prod => prod.itemId === itemId)) {
        this.$store.state.status_message.message = "Você já adicionou este produto.";

        this.$store.state.status_message.code = 0;
      } else {
        this.$store.state.shop.app_cart.push(prod);
        this.$store.state.shop.paypal_cart.push(paypalObj);

        this.$store.state.shop.mailingFiles.push({
          itemId: itemId,
          itemDescription: itemDescription,
          itemAmount: parseFloat(itemAmount.replace(/,/g, '.')).toFixed(2),
          itemQuantity: itemQuantity,
          itemUrl: itemUrl
        });  

        if(document.querySelector(`#track_${itemId}`)&&this.$store.state.shop.app_cart.some(prod => prod.itemId === itemId)) {
          document.getElementById(`track_${itemId}`).classList.add('incart');
        }

        this.inCart = this.$store.state.shop.app_cart.some(el => el.itemId === itemId);
      }
    },
    getAsyncData: function() {
      var self = this;
      window.setTimeout(function() {
        self.title = "Violab - " + vm._data.post.title.rendered;
        self.meta.name = vm._data.post.title.rendered;
        self.meta.description =
          " | " + vm._data.post.excerpt.rendered.replace(/(<([^>]+)>)/gi, "");
        self.meta.ogImage = vm._data.post.thumbnail;
        self.$emit("updateHead");
      });
    },
    fetchData() {
      store.state.isLoading = true;
      
      vm._data.tracklist = [];

      var self = this;
      fetch(
        this.$store.state.BASE_API + "/wp-json/wp/v2/" + router.history.current.meta.post_type + '/' + router.history.current.params.id
      )
        .then(function(response) {
          return response.json();
        })
        .then(function(res) {
          vm._data.post = res;
          vm._data.isInit = true;
          store.state.isLoading = false;

          var tracks = [];

          if(res.acf.album)
          {
            for (const [key, value] of Object.entries(res.acf.arquivo)) {
              tracks.push(self.$store.state.BASE_API + "/wp-json/wp/v2/tracks/" + value.ID);
            }

            let promise = tracks.map(l => fetch(l).then(res => res.json()));

            Promise.all(promise).then(res => {
              for (const [key, value] of Object.entries(res)) {
                var track = {
                  'id':value.id,
                  'title':value.title.rendered,
                  'audios':{
                    'mp3':{
                      'arquivo':value.acf.mp3.arquivo,
                      'previa':value.acf.mp3.previa.url,
                      'preco':value.acf.mp3.preco
                    },
                    'wav':{
                      'arquivo':value.acf.wav.arquivo,
                      'previa':value.acf.wav.previa,
                      'preco':value.acf.wav.preco
                    }
                  }
                }                
                vm._data.tracklist.push(track);
              }
            });
          } else if(res.categories[0] === 'videoaula') {
            var videoID = res.acf.arquivo[0].ID;
            fetch(
              self.$store.state.BASE_API + "/wp-json/wp/v2/videos/" + videoID
            )
              .then(function(response) {
                return response.json();
              })
              .then(function(res) {
                vm._data.videoPreview = res.acf.previa.url;
              });
          }

          self.getAsyncData();
        });
    }
  },
  mounted() {
    this.fetchData();

    vm._data.inCart = this.$store.state.shop.app_cart.some(el => el.itemId === parseInt(this.id));
  },
  beforeRouteEnter(to, from, next) {
    vm._data.prevRoute = from.path;
    vm._data.post = [];
    next();
  },
  watch: {
    cart() {
      vm._data.inCart = this.$store.state.shop.app_cart.some(el => el.itemId === parseInt(this.id));
    }
  },
  computed: {
    cart() {
      return this.$store.state.shop.app_cart;
    },    
    inCart() {
      return vm._data.inCart;
    },
    ext() {
      return this.extension;
    },
    post_content() {
      return emojiStrip(vm._data.post.content.rendered)
    },
    vm() {
      return vm;
    }
  },
  components: {
    pageHeader
  },
  head: {
    title: function() {
      return {
        inner: this.title,
        separator: " ",
        complement: this.meta.description
      };
    },
    meta: function() {
      return [
        { name: "application-name", content: this.meta.name },
        { name: "description", content: this.meta.description },
        { name: "twitter:title", content: this.meta.name },
        { n: "twitter:description", c: this.meta.description },
        { itemprop: "name", content: this.meta.name },
        { itemprop: "description", content: this.meta.description },
        { property: "fb:app_id", content: "123456789" },
        { property: "og:title", content: this.meta.name },
        { p: "og:image", c: this.meta.ogImage }
      ];
    },
    links: function() {
      return [
        { rel: "canonical", href: window.location.href, id: "canonical" }
      ];
    }
  }
};
</script>

<style lang="sass">
@import './sass/_Single'
</style>
