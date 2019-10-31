<template>
	<p v-show="this.currentstep === 3" data-title="Endreço de Cobrança" data-step="step-3">
		<span>
			<input
			v-model="value.cartao_cep"
			placeholder="CEP"
			v-mask="['#####-###']"
			type="text"
			name="cartao_cep"
			@blur="cepSeeking"
			>
		</span>
		<span v-show="value.cartao_cep" class="siders">
			<input
			v-model="value.cartao_endereco"
			readonly="readonly"
			placeholder="Endereço"
			type="text"
			name="cartao_endereco"
			>
			<input
			v-model="value.cartao_numero"
			placeholder="Número"
			type="number"
			min="0"
			name="cartao_numero"
			>
		</span>
		<span v-show="value.cartao_cep">
			<input
			v-model="value.cartao_complemento"
			placeholder="Complemento"
			type="text"
			name="cartao_complemento"
			>
		</span>
		<span v-show="value.cartao_cep">
			<input
			v-model="value.cartao_bairro"
			readonly="readonly"
			placeholder="Bairro"
			type="text"
			name="cartao_bairro"
			>
		</span>
		<span v-show="value.cartao_cep">
			<input
			v-model="value.cartao_cidade"
			placeholder="Cidade"
			readonly="readonly"
			type="text"
			name="cartao_cidade"
			>
		</span>
		<span v-show="value.cartao_cep">
			<input
			v-model="value.cartao_estado"
			placeholder="Estado"
			readonly="readonly"
			type="text"
			name="cartao_estado"
			@keyup="next('keypress')"
			>
		</span>
<!-- 		<button class="btn btn-1" v-show="value.cartao_numero" @click.prevent="next(false)" >
		Próximo Passo
		</button> -->
		<button :class="'btn btn-1 ' + ((!value.cartao_numero) ? '-disabled' : '')" @click.prevent="next(false)" >
		Próximo Passo
		</button>		
	</p>
</template>

<script>
import {mask} from 'vue-the-mask';
import store from "@/store";
export default {
	directives: {mask},
	name: 'billingAddress',
	methods: {
		cepSeeking() {
			this.$emit('cepSeeking', 'billingAddress');
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