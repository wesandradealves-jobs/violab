<template>
  <section>
    <div class="container">
      <page-header
        :titulo="'Loja'"
        :postType="'produtos'"
        :prevRoute="vm._data.prevRoute"
      />
      <div class="post-content" v-if="'' !== vm._data.posts">
        <sidebar 
          v-on:filter="filtering" 
          :items="$store.state.shop.categories" 
        />
        <div class="post-grid">
          <div
            v-for="post in (vm._data.filteredPosts.length > 0 ? vm._data.filteredPosts : vm._data.posts)"
            :key="post.id"
            v-if="post.acf.habilitar"
            @click="$router.replace(`loja/${post.id}`)"
          >
            <div>
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
              <h3 v-html="post.title.rendered" />
              <h4>
                <span v-if="!post.acf.produto_gratis">
                  R${{ post.acf.preco.replace('.',',') }}
                </span>
                <span v-else>
                  Cortesia
                </span>
              </h4>
              <p
                v-if="'' !== post.excerpt.rendered"
                v-html="post.excerpt.rendered"
              />
              <a class="btn btn-1" :title="post.name">Ver detalhes</a>
            </div>
          </div>
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
import sidebar from "@/components/sidebar/sidebar.vue";
var vm = new Vue({
  data: {
    filteredPosts: [],
    posts: [],
    prevRoute: null
  }
});
export default {
  name: 'Loja',
  components: {
    sidebar,
    pageHeader
  },
  methods: {
    filtering(v) {
      vm._data.filteredPosts = [];

      vm._data.posts.filter(function(post) {
        if(post.categories.filter(value => v.includes(value)).length) {
          vm._data.filteredPosts.push(post);
        }
      });      
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
    },
    fetchData() {
      store.state.isLoading = true;
      fetch(`${store.state.BASE_API}/wp-json/wp/v2/produtos`)
        .then(function(response) {
          return response.json();
        })
        .then(function(res) {
          vm._data.posts = res;
          store.state.shop.products = res;
          store.state.isLoading = false;
        });
    }
  },
  props: ['query'],
  watch: {
    shop_cart(v) {
      return v
    },    
    q(v) {
      vm._data.filteredPosts = [];

      vm._data.posts.filter(function(post) {
        var regex = new RegExp("\\b"+v+"\\b", "i"); 
        if (regex.test(post.title.rendered)) {
          vm._data.filteredPosts.push(post);
        }
      }); 
    }
  },  
  computed: {
    shop_cart() {
      return this.$store.state.shop.app_cart
    },
    q() {
      return this.query;
    },    
    vm() {
      return vm;
    }
  },
  mounted() {
    if (null === this.$store.state.shop.products || !this.$store.state.shop.products.length) {
      this.fetchData();
    } else {
      vm._data.posts = this.$store.state.shop.products;
    }
  },
  beforeRouteEnter(to, from, next) {
    vm._data.prevRoute = from.path;
    next();
  },
  data() {
    return {

    };
  },
};
</script>

<style lang="sass">
@import './sass/_Loja'
</style>