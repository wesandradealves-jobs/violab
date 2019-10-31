<template>
  <nav
    class="navigation"
    :class="[this.mode ? '-' + this.mode : '', this.expand ? 'is-active' : '']"
  >
    <ul>
      <li v-for="item in $store.state.header.navigation" :key="item.slug">
        <a
          class="nav-anchor"
          @click="scrollSection($event)"
          :data-anchor="item.post_name"
          href="javascript:void(0)"
          :title="item.title"
        >
          {{ item.title }}
        </a>
      </li>
      <li v-if="!this.mode">
        <button
          class="hamburger hamburger--collapse"
          :class="{ 'is-active': this.isActive }"
          type="button"
          @click="openMenu"
        >
          <span class="hamburger-box">
            <span class="hamburger-inner"></span>
          </span>
        </button>
      </li>
    </ul>
    <shop-cart v-if="!this.mode" />
    <search-bar
    	v-on:search="search" 
    	v-if="(this.page === 'loja' || this.page === 'artigos') && !this.mode" 
    />
    <!-- <language-switcher /> -->
  </nav>
</template>
<script>
import ShopCart from "@/components/ShopCart/ShopCart.vue";
import searchBar from "@/components/searchBar/searchBar.vue";
import languageSwitcher from "@/components/languageSwitcher/languageSwitcher.vue";
import vuescroll from 'vuescroll';
export default {
  name: "navigation",
  methods: {
    search(v) {
      this.$emit('search', v);
    },
    scrollSection: function() {
      var self = this;

      var anchor = (self.$store.state.selectedAnchor = event.target.attributes[0].value);

      if(anchor === 'loja') { 
        self.$router.push('/loja');
      } else {
        if(!document.querySelectorAll(`#${anchor}`).length) {
          self.$router.push('/');
          setTimeout(function() {
            self.$emit('scrollSection', `#${anchor}`);
          }, 1000);          
        } else {
          self.$emit('scrollSection', `#${anchor}`);
        }
      }
    },
    onResize(event) {
      this.isActive = event.target.outerWidth > 880 ? false : this.isActive;
      this.$emit("openMenu", this.isActive);
    },
    openMenu: function() {
      this.isActive = !this.isActive;
      this.$emit("openMenu", this.isActive);
    }
  },
  created() {
    window.addEventListener("resize", this.onResize);
  },
  data() {
    return {
      page: '',
      isActive: false
    };
  },
  watch: {
    '$route' (to, from) {
    	this.page = to.meta.slug;
    }
  },  
  props: ["mode", "expand"],
  components: {
    languageSwitcher,
    searchBar,
    ShopCart
  }
};
</script>
<style lang="sass">
@import './sass/_navigation'
</style>
