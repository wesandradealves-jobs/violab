<template>
  <section>
    <div class="container">
      <page-header :titulo="'Conteúdo'" :prevRoute="vm._data.prevRoute" />
      <div class="post-content" v-if="'' !== vm._data.posts">
        <sidebar 
          v-on:filter="filtering" 
          :items="$store.state.blog.categories" 
        />
        <div class="post-grid">
          <div
            v-for="post in (vm._data.filteredPosts.length > 0 ? vm._data.filteredPosts : vm._data.posts)"
            :key="post.id"
            @click="$router.replace(`conteudo/${post.id}`)">
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
            <p
              v-if="'' !== post.excerpt.rendered"
              v-html="post.excerpt.rendered"
            />
            <a href="javascript:void(0)" title="Continuar Lendo">
              Continuar Lendo &#8594;
            </a>
          </div>            
        </div>                
      </div>
    </div>
  </section>
</template>
<script>
import Vue from "vue";
import store from "@/store";
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
  name: "Artigos",
  methods: {   
    filtering(v) {
      vm._data.filteredPosts = [];

      vm._data.posts.filter(function(post) {
        if(post.categories.filter(value => v.includes(value)).length) {
          vm._data.filteredPosts.push(post);
        }
      });      
    },
    fetchData() {
      store.state.isLoading = true;
      fetch(`${store.state.BASE_API}/wp-json/wp/v2/posts`)
        .then(function(response) {
          return response.json();
        })
        .then(function(res) {
          vm._data.posts = res;
          store.state.blog.posts = res;
          store.state.isLoading = false;
        });
    }
  },
  mounted() {
    if (null === this.$store.state.blog.posts || !this.$store.state.blog.posts.length) {
      this.fetchData();
    } else {
      vm._data.posts = this.$store.state.blog.posts;
    }
  },
  beforeRouteEnter(to, from, next) {
    vm._data.prevRoute = from.path;
    next();
  },
  props: ['query'],
  watch: {
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
    q() {
      return this.query;
    },
    vm() {
      return vm;
    }
  },
  components: {
    sidebar,
    pageHeader
  }
};
</script>
<style lang="sass">
@import './sass/_Artigos'
</style>
