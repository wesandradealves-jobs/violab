 <template>
  <div
    id="wrap"
    :class="[
      ($route.meta.slug !== undefined) ? 'pg-' + $route.meta.slug : '',
      this.$route.meta.slug && this.$route.meta.slug !== 'home' ? 'pg-interna' : ''
    ]"
  >
    <scroll-area @handle-scroll="handleScroll" ref="vs">
      <status-message />
      <app-header 
        v-on:scrollSection="scrollSection"  
        v-on:search="search"
        :scrollPosition="this.scrollPos" 
      />
      <main class="main">
        <transition name="fade" mode="out-in">
          <router-view :query="query" :isInit="isInit" />
        </transition>
      </main>
      <app-footer />
      <back-to-top 
        v-if="this.$route.meta.slug !== 'page-not-found'"
        v-on:backToTop="backToTop"  
        :scrollPosition="this.scrollPos" 
      />
    </scroll-area>
  </div>
</template>
<script>
import Vue from 'vue';
import AppHeader from "@/components/header/header.vue";
import AppFooter from "@/components/footer/footer.vue";
import NProgress from "nprogress";
import "@/../node_modules/nprogress/nprogress.css";
import backToTop from "@/components/backToTop/backToTop.vue";
import statusMessage from "@/components/statusMessage/statusMessage.vue";
import vuescroll from 'vuescroll';
import router from "@/router";
Vue.use(vuescroll, {
  ops: {
    vuescroll: {
      wheelScrollDuration: 500,
      detectResize: true
    },
    scrollPanel: {
      scrollingX: false,
      scrollingY: true,
      speed: 1500,
      easing: 'easeInOutCubic'
    },
    rail: {
      background: '#000000',
      keepShow: false,
      specifyBorderRadius: '0px'
    },
    bar: {
      onlyShowBarOnScroll: false,
      keepShow: true,
      background: '#FFFFFF',
      specifyBorderRadius: '0px'
    }
  },
  name: 'scrollArea' // customize component name, default -> vueScroll
});
export default {
  name: "App",
  data() {
    return {
      scrollPos: 0,
      q: ''
    };
  }, 
  methods: {
    search(v) {
      this.q = v;
    },
    fetchData() {
      var self = this;
      self.$store.state.isLoading = true;

      const fetchArr = [
        this.$store.state.BASE_API + "/wp-json/wp/v2/pages",
        this.$store.state.BASE_API + "/wp-json/wp/v2/posts",
        this.$store.state.BASE_API + "/wp-json/wp/v2/produtos",
        this.$store.state.BASE_API + "/wp-json/customizer/v1/social",
        this.$store.state.BASE_API + "/wp-json/customizer/v1/bloginfo",
        this.$store.state.BASE_API + "/wp-json/menus/v1/menus/header-pt",  
        this.$store.state.BASE_API + "/wp-json/wp/v2/categories",
        this.$store.state.BASE_API + "/wp-json/wp/v2/produtos_categorias_pt"      
      ];

      let promise = fetchArr.map(l => fetch(l).then(res => res.json()));

      Promise.all(promise).then(res => {
        self.$store.state.pages = res[0];
        self.$store.state.blog.posts = res[1];
        self.$store.state.shop.products = res[2];
        self.$store.state.header.social_networks = res[3];
        self.$store.state.header.bloginfo = res[4];
        self.$store.state.header.navigation = res[5].items;
        self.$store.state.blog.categories = res[6];
        self.$store.state.shop.categories = res[7];


        if (self.$store.state.header.bloginfo.favico) {
          var favico = document.createElement("link");
          favico.rel = "shortcut icon";
          favico.href = self.$store.state.header.bloginfo.favico;
          document.head.appendChild(favico);
        }        

        self.$store.state.isLoading = self.$store.state.isInit = false;

        for (const [key, value] of Object.entries(res[0])) {
          if(value.slug === 'home') {
              self.$store.state.home = value;
          }
        }
      });
    },    
    backToTop: function() {
      this.$refs['vs'].scrollTo(
        {
          y: 0
        },
        1500
      );
    },   
    scrollSection: function(data) {
      var self = this;
      if(document.querySelectorAll(data).length) {
        self.$refs['vs'].scrollIntoView(data, 1500);
      }
    },
    handleScroll(vertical, horizontal, nativeEvent) {
      this.scrollPos = vertical.scrollTop;

      const slug = this.$router.history.current.meta.slug;
      const nav = document.getElementsByClassName("nav-anchor");
      const sections = document.querySelectorAll(
        "[data-anchor]:not(.nav-anchor)"
      );

      function addClass() {
        nav[i].classList.add("active");
        if (nav[i].attributes[0].value === sections[i].attributes[0].value) {
          nav[i].classList.add("active");
        }
      }

      function removeClass() {
        nav[i].classList.remove("active");
        if (nav[i].attributes[0].value === sections[i].attributes[0].value) {
          nav[i].classList.remove("active");
        }
      }

      if (slug === "home") {
        for (var i = 0; i < nav.length; i++) {
          if (sections[i]) {
            if (vertical.scrollTop > sections[i].offsetTop) {
              addClass();
              if (vertical.scrollTop > sections[i].offsetTop + sections[i].offsetHeight) {
                removeClass();
              }
            } else {
              removeClass();
            }
          }
        }
      }      
    } 
  },
  watch: {
    isLoading(value) {
      if (value) {
        NProgress.start();
      } else {
        NProgress.done();
      }
    }
  },
  computed: {
    query() {
      return this.q;
    },
    isInit() {
      return this.$store.state.isInit;
    },    
    isLoading() {
      return this.$store.state.isLoading;
    }
  },
  mounted() {
    if (this.$store.state.isInit) {
      this.fetchData();
    }

    router.afterEach((to, from) => {
      this.$refs['vs'].scrollTo(
        {
          y: 0
        },
        1500
      );
    });    
  },
  created() {
    NProgress.start();
  },
  components: {
    statusMessage,
    backToTop,
    AppHeader,
    AppFooter
  }
};
</script>

<style lang="sass">
@import './_style'
</style>
