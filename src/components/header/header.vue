<template>
  <header id="header" :class="[this.scrollPosition > 0 ? 'sticky' : '']">
    <div class="topbar">
      <div class="container">
        <social-networks v-if="'' !== $store.state.header.social_networks" />
      </div>
    </div>
    <div class="header">
      <div class="container">
        <h1 class="logo">
          <router-link :to="{ path: '/#' }">
            <img
              :src="$store.state.header.bloginfo.logo"
              alt="Violab"
              v-if="$store.state.header.bloginfo.logo"
            />
            <span v-else>Violab</span>
          </router-link>
        </h1>
        <navigation 
          v-on:scrollSection="scrollSection" 
          v-on:openMenu="onChildClick" 
          v-on:search="search"
          v-if="'' !== $store.state.header.navigation" 
        />
        <navigation
          v-on:scrollSection="scrollSection"
          :expand="isExpanded"
          :mode="'mobile'"
          v-if="'' !== $store.state.header.navigation"
        />
      </div>
    </div>
  </header>
</template>
<script>
import socialNetworks from "@/components/socialNetworks/socialNetworks.vue";
import navigation from "@/components/navigation/navigation.vue";

export default {
  name: "AppHeader",
  components: {
    socialNetworks,
    navigation
  },
  data() {
    return {
      isExpanded: false
    };
  },
  props: ["scrollPosition"],
  methods: {
    search(v) {
      this.$emit('search', v);
    },    
    scrollSection: function(data) {
      this.$emit('scrollSection', data);
    },
    openMenu: function() {
      this.isActive = !this.isActive;
      this.$emit("openMenu", this.isActive);
    },
    onChildClick(data) {
      this.isExpanded = data;
    }
  }
};
</script>
<style lang="sass">
@import './sass/_header'
</style>
