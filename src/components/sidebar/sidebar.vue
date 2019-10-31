<template>
	<aside class="sidebar">
		<div class="sidebar-inner">
			<div class="filter">
				<h2>Categorias</h2>
				<ul>
					<li v-for="item in this.items">
						<div class="checkbox">
							<input @click="_filter" type="checkbox" :value="item.slug" v-model="filter">
							<label for=""></label>						
						</div>
						{{item.name}}
					</li>
				</ul>
			</div>			
		</div>
	</aside>
</template>
<script>
import store from "@/store";
export default {
  name: "sidebar",
  data() {
    return {
    	filter: []
    };
  },  
  methods: {
  	_filter() {
  		var self = this;
  		setTimeout(()=>{
  			self.$emit('filter', self.filter);
  		}, 100);
  	}
  },
  props: ["items"]
};
</script>

<style lang="sass">
@import './sass/_sidebar'
</style>


