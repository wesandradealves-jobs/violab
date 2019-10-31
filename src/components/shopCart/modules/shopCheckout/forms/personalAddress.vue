<template>
    <p v-show="this.currentstep === 2" data-title="Endreço Pessoal" data-step="step-2">
		<span>
		<input
		  v-model="value.cep" 
		  placeholder="CEP" 
		  v-mask="['#####-###']"
		  type="text" 
		  name="cep"
		  @blur="cepSeeking"
		>
		</span>
		<span v-show="value.cep" class="siders">
		<input 
		  v-model="value.endereco" 
		  readonly="readonly" 
		  placeholder="Endereço" 
		  type="text" 
		  name="endereco"
		>
		<input 
		  v-model="value.numero" 
		  placeholder="Número" 
		  type="number" 
		  min="0"
		  name="numero"
		>
		</span>
		<span v-show="value.cep">
		<input 
		  v-model="value.complemento" 
		  placeholder="Complemento" 
		  type="text" 
		  name="complemento"
		>
		</span>
		<span v-show="value.cep">
		<input 
		  v-model="value.bairro" 
		  readonly="readonly" 
		  placeholder="Bairro" 
		  type="text" 
		  name="bairro"
		>
		</span>
		<span v-show="value.cep">
		<input 
		  v-model="value.cidade" 
		  placeholder="Cidade" 
		  readonly="readonly" 
		  type="text" 
		  name="cidade"
		>
		</span>
		<span v-show="value.cep">
		<input 
		  v-model="value.estado" 
		  placeholder="Estado" 
		  readonly="readonly" 
		  type="text" 
		  name="estado"
		  @keyup="next('keypress')"
		>
		</span>
<!-- 	    <button class="btn btn-1" v-show="value.numero" @click.prevent="next(false)" >
	    	Próximo Passo
	    </button> -->
	    <button :class="'btn btn-1 ' + ((!value.numero) ? '-disabled' : '')" @click.prevent="next(false)" >
	    	Próximo Passo
	    </button>
    </p>
</template>

<script>
import {mask} from 'vue-the-mask';
import store from "@/store";
export default {
	directives: {mask},
	name: 'personalAddress',
	methods: {
		cepSeeking() {
			this.$emit('cepSeeking', 'personalAddress');
		},
	    next(s) {
	    	this.$emit('nextStep', s);
	    }
	},
	props: {
		currentstep: {
			type: Number
		},
	    value: {
	        type: Object,
	        required: true
	    }
	},
	watch: {
	    value() {
	        this.$emit('input', this.value);
	    }
	}
};
</script>