<template>
	<div class="search-bar">
	    <a 
	      @click="searchToggle" 
	      title="Busca" 
	      class="fas fa-search"
	    ></a>		
	    <transition name="fade" mode="out-in">
		    <div 
		    	class="search-form" 
		    	v-show="this.isVisible"
		    >
		    	<div class="search-form-inner">
			    	<input 
			    		@keyup="_search" 
			    		type="text" 
			    		id="search-form" 
			    		placeholder="Busca" 
			    		v-model="search" 
			    	/>
			    	<a @click="searchToggle" class="fas fa-times"></a>		    		
		    	</div>
		    </div>
		</transition>
	</div>	
</template>

<script>
export default {

  name: 'searchBar',  
  data() {
    return {
    	search: '',
    	timeout: null,
    	isVisible: false
    };
  },  
  methods: {
  	_search(v) {
  		var self = this;
  		clearTimeout(self.timeout);

  		this.$emit('search', this.search);

		self.timeout = setTimeout(function () {
			self.searchToggle();		    
		}, 500);  		
  	},
    searchToggle() {
    	this.isVisible = !this.isVisible;
    }
  }
};
</script>

<style lang="sass">
@import './sass/_searchBar'
</style>