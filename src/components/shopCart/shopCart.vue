<template>
  <div class="shopCart">
    <shop-checkout v-on:collapse="checkout" :isExpanded="isExpanded" :cartTotal="$store.state.shop.cart_total" />
    <a href="javascript:void(0)" class="fas fa-shopping-cart" :title="shopCart.length === 0 ? 'Não há itens adicionados.' : ''">
      <small v-if="shopCart.length > 0" class="qti">{{ shopCart.length }}</small>
      <ul v-if="shopCart.length > 0" class="cart-list">
        <li :data-prod="product.itemId" v-for="product in shopCart" :key="product.itemId">
          <span>{{ product.itemDescription }}</span>
          <i @click="_removeItem(product.itemId)" class="far fa-trash-alt"></i>
        </li> 
        <li class="cart-list-total">
          <i class="fas fa-guitar"></i>
          <small>Total: </small>
          <span>
            <strong>R$ {{ $store.state.shop.cart_total.replace('.',',') }}</strong>
          </span>
        </li>   
        <li @click="checkout" class="cart-list-checkout">
          <span>Checkout</span>
        </li>     
        <li class="cart-list-checkout -paypal">
          <div id="paypal-button"></div>
          <PayPal
            :amount="$store.state.shop.cart_total"
            currency="BRL"
            env="production"
            :items="$store.state.paypal_cart"
            :experience="this.experienceOptions"
            :button-style="this.paypalBtn"
            v-on:payment-completed="paymentCompleted"
            :client="this.paypal">
          </PayPal>     
        </li>  
      </ul> 
    </a>
  </div>
</template>
<script>
import Vue from 'vue';
import store from "@/store";
import shopCheckout from "./modules/shopCheckout/shopCheckout.vue";
import PayPal from 'vue-paypal-checkout'
import axios from 'axios';
import VueAxios from 'vue-axios';

Vue.use(VueAxios, axios);

export default {
  name: 'shopCart',
  data () {
    return {
      paypal: {
        sandbox: 'ARMRe8t3gpneNINKOmM1UmuHpj7pFHJ8TMaO5CtLeH3_9pEvo4YW2cGpT4-UhLCkktdmUrbY32nUKltS',
        // producton: 'AXKJxHZWxnL8bUqlb6DTYkkN1TyjlbMV412N4e4-14e1vDCW_CWD6ZewiACVG0d6z1rxoXSlqRmERf__'
        production: 'AeXQeHmSOFQ4Ccifg_Pa1XgEeWjaeBSXkLLdcRULGmjNasgBTbRfVcaggEwyOKX0gFxZdrmjutIXSSp8'
      },
      paypalBtn: {
        label: 'checkout',
        size:  'responsive',
        shape: 'pill',
        color: 'gold'
      },      
      experienceOptions: {
        input_fields: {
          no_shipping: 1
        }
      },      
      isExpanded: false
    }
  },
  methods: {
    setMessage(code, msg, timeout) {
      var self = this;

      self.$store.state.status_message.message = msg;
      self.$store.state.status_message.code = code;
      self.$store.state.status_message.timeout = timeout;
    },    
    refreshApp() {
      this.$store.state.shop.cart_total = 0;
      this.$store.state.shop.app_cart = [];
      this.$store.state.shop.paypal_cart = [];
      this.$store.state.shop.mailingFiles = [];

      window.scrollTo({
        top: 0,
        behavior: "smooth"
      });
    },    
    paymentCompleted(res){
      var self = this;

      var incart = document.querySelectorAll('.incart');

      for (var i = 0; i < incart.length; i++) {
        incart[i].classList.remove('incart')
      }

      // -

      if(res.state === 'approved') {
        var data = new FormData();

        console.log(res);

        var mailingFiles = JSON.stringify(this.$store.state.shop.mailingFiles);
        var senderData = JSON.stringify(res.payer.payer_info);
        var CID = res.id;

        axios.get(`http://dev.uppercreative.com.br/phpmailer/paypalConfirmMail.php?cid=${CID}&mailingFiles=${mailingFiles}&senderData=${senderData}`)
        .then(function(result) {
            console.log(result);
          })
          .catch(function (err) {
            self.setMessage(0, 'O formuário não pode ser enviado.');
            // console.log(res);
          });      

        self.setMessage(1, 'Seu pedido foi efetuado com sucesso.', 2000);

        localStorage.setItem("checkout", JSON.stringify(res.data));          
      } else {
        self.setMessage(0, 'Seu pedido não foi aprovado pela operadora do cartão.', 2000);
      }


    },
    // paymentAuthorized(res){
    //   var self = this;

    //   var incart = document.querySelectorAll('.incart');

    //   for (var i = 0; i < incart.length; i++) {
    //     incart[i].classList.remove('incart')
    //   }

    //   // -

    //   // console.log(res);
    //   console.log(this.$store.state.shop);

    //   // var data = new FormData();

    //   // data.append('mailingFiles', this.$store.state.shop.mailingFiles);

    //   // axios.post("http://dev.uppercreative.com.br/pagseguro/paypalMailing.php", mailingFiles)
    //   //   .then(function (response) {
    //   //     console.log(response);
    //   //   })
    //   //   .catch(function (res) {
    //   //     self.setMessage(0, 'O formuário não pode ser enviado.');
    //   //     // console.log(res);
    //   //   });      

    //   // 

    //   self.setMessage(1, 'Seu pedido foi efetuado com sucesso.', 2000);

    //   // setTimeout(()=>{
    //   //   self.refreshApp();
    //   // }, 2000);      

    //   localStorage.setItem("checkout", JSON.stringify(res.data));
    // },    
    formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
      try {
        decimalCount = Math.abs(decimalCount);
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

        const negativeSign = amount < 0 ? "-" : "";

        let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
        let j = (i.length > 3) ? i.length % 3 : 0;

        return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
      } catch (e) {
        // console.log(e)
      }
    },
    collapse: function() {
      alert();
    },
    checkout: function(s) {
        this.isExpanded = !this.isExpanded;

        if(this.isExpanded) {
          document.getElementsByTagName('body')[0].classList.add('hidden');
        } else {
          document.getElementsByTagName('body')[0].classList.remove('hidden');
        }
    },    
    _removeItem: function(id) {
      var self = this;
      const cartItem = document.querySelector(`[data-prod="${id}"]`);

      cartItem.parentNode.removeChild(cartItem);

      for (var i = self.$store.state.shop.app_cart.length - 1; i >= 0; --i) {
          if (self.$store.state.shop.app_cart[i].itemId === id) {
              self.$store.state.shop.app_cart.splice(i, 1);
          }
      }

      for (var i = self.$store.state.shop.paypal_cart.length - 1; i >= 0; --i) {
          if (self.$store.state.shop.paypal_cart[i].sku === id) {
              self.$store.state.shop.paypal_cart.splice(i, 1);
          }
      }     

      for (var i = self.$store.state.shop.mailingFiles.length - 1; i >= 0; --i) {
          if (self.$store.state.shop.mailingFiles[i].itemId === id) {
              self.$store.state.shop.mailingFiles.splice(i, 1);
          }
      }     

      if(document.querySelector(`#track_${id}`)&&!this.$store.state.shop.app_cart.some(prod => prod.itemId === id)) {
        document.getElementById(`track_${id}`).classList.remove('incart');
      }
    },
    sum(input){
                 
    if (toString.call(input) !== "[object Array]")
    return false;

    var total =  0;

    for(var i=0;i<input.length;i++)
      {                  
        if(isNaN(input[i])){
        continue;
         }
          total += Number(input[i]);
       }
     return total;
    }
  },
  watch: {
    shopCart(newData) {
        var self = this;

        this.$store.state.isLoading = true; 

        this.$store.state.shop.app_cart = newData;

        var total = [];

        setTimeout(()=>{
          for (const [key, value] of Object.entries(newData)) {
            total.push(value.itemAmount);
          }

          this.$store.state.shop.cart_total = this.sum(total).toFixed(2);

          this.$store.state.isLoading = false;

        }, 500);
    }
  },
  components: {
    PayPal,
    shopCheckout
  },  
  computed: {
    shopCart() {
      return this.$store.state.shop.app_cart;
    },
    vm() {
      return vm;
    }
  }
};
</script>
<style lang="sass">
@import "./sass/_shopCart"
</style>
