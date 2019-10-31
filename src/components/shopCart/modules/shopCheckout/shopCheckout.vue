<template>
  <div class="shopCheckout" :class="{'uncollapsed':isExpanded}"  v-if="$store.state.shop.cart_total !== 0">
    <div class="shopCheckoutInner">
      <h3 class="screen-title">Checkout <a href="javascript:void(0)" @click="collapse">&#8594;</a>
      </h3>
      <a v-if="prePayment" @click="prePayment = false"><small>&#8592; Voltar</small></a>
      <steps-navigator 
        :switchCurrent="_stepsNav.current"
        v-on:current="current" 
        v-on:steps="steps" 
        v-if="!prePayment"
        />
          <form @submit.prevent="_pay" id="checkout">
            <div v-if="!prePayment" class="inputs">
              <h4 
                v-if="nav.step 
                  === _stepsNav.current"
                v-for="nav in this.navigator.steps"
              >
                {{ nav.title }}
              </h4>
              <personal-data
                  v-on:nextStep="nextStep"
                  :currentstep="_stepsNav.current"
                  :value="this.billForm.personalData"
                  @input="(newPersonalData) => {
                    this.billForm.personalData = newPersonalData
                  }"
              />
              <personal-address
                  v-on:cepSeeking="cepSeeking"
                  v-on:nextStep="nextStep"
                  :currentstep="_stepsNav.current"
                  :value="this.billForm.address"
                  @input="(newAddress) => {
                    this.billForm.address = newAddress
                  }"
              />  
              <billing-address
                  v-on:cepSeeking="cepSeeking"
                  v-on:nextStep="nextStep"
                  :currentstep="_stepsNav.current"
                  :value="this.billForm.billingAddress"
                  @input="(newBillingAddress) => {
                    this.billForm.billingAddress = newBillingAddress
                  }"
              />  
              <credit-card
                  v-on:confirm="saveForm"
                  :currentstep="_stepsNav.current"
                  :value="this.billForm.card"
                  @input="(newCreditCard) => {
                    this.billForm.card = newCreditCard
                  }"
              /> 
            </div>
            <div v-else class="prePayment">
              <ul class="product-checklist">
                <li v-for="product in $store.state.shop.app_cart" :key="product.itemId">
                  <span>#{{ product.itemId }}</span>
                  <span>{{ product.itemQuantity }}</span>
                  <span>{{ product.itemDescription }}</span>
                  <span>R$ {{ (product.itemAmount !== '') ? product.itemAmount.replace(".",",") : 'Produto Grátis' }}</span>
                </li>
              </ul>
              <p class="checkout-total">
                <span v-if="$store.state.shop.cart_total !== ''">
                  <small>Total:</small>
                  R$ {{ $store.state.shop.cart_total.replace(".",",") }}              
                </span>
                <span v-else>
                  Produto Grátis
                </span>
              </p>
              <button class="btn btn-3">Efetuar Pagamento</button>
            </div>           
          </form>
<!--       <form v-if="null !==  vm._data.shop.proccess.paymentMethods" name="papaymentMethods">
        <h4>Escolha uma Forma de Pagamento 
          <a v-if="vm._data.shop.proccess.paymentChosen" 
          @click="setPayment('')">&#8592; Voltar</a></h4>
        <div class="methods-list" v-if="!vm._data.shop.proccess.paymentChosen">
            <div
              class="method" 
              @click="setPayment(method)"
              v-if="method.name === 'BOLETO' || 
              method.name === 'CREDIT_CARD'" 
              v-for="method in vm._data.shop.proccess.paymentMethods"
            >
              <div class="method-inner">
                <img :src="$store.state.shop.pagseguro._public_path + (method.name === 'BOLETO' ? method.options.BOLETO.images.MEDIUM.path : method.options.MASTERCARD.images.MEDIUM.path)" :alt="method.name">

                <p v-text="method.name === 'BOLETO' ? 'Boleto' : 'Cartão de crédito'"></p>
              </div>
            </div>
        </div>
        <div v-if="vm._data.shop.proccess.paymentChosen" class="chosen-method">
          <div v-if="vm._data.shop.proccess.paymentChosen.name === 'CREDIT_CARD'">
            <div v-for="card in vm._data.shop.proccess.paymentChosen.options">
              {{ card }}
            </div>
          </div>
          <div v-else>
            
          </div>
        </div>
      </form> -->
    </div>
  </div>
</template>

<script>
import store from "@/store";
import Vue from 'vue';
import axios from 'axios';
import VueAxios from 'vue-axios';
import stepsNavigator from "./modules/stepsNavigator/stepsNavigator.vue";
import personalData from './forms/personalData.vue';
import personalAddress from './forms/personalAddress.vue';
import billingAddress from './forms/billingAddress.vue';
import creditCard from './forms/creditCard.vue';

var buscaCep = require('busca-cep');

Vue.use(VueAxios, axios);

var vm = new Vue({
  data: {
    shop: {
      proccess: {
        formData: [],
        chosenMethod: null,
        response: null,
        checkout: null,
        buyer: {
          senderHash: null,
          SSID: null,
          paymentData: {
            card: null,
            eToken: null,
            installments: null
          }          
        },
        seller: {
          email: null
        }
      },
      cart: {
        items: null,
        mailingFiles: store.state.shop.mailingFiles
      },
      credentials: {
        token: null,
        email: null     
      }
    }
  }
});

export default {
  name: "shopCheckout",
  props: ["isExpanded"],
  methods: {
    refreshApp() {
      this.$store.state.shop.cart_total = 0;
      this.$store.state.shop.app_cart = [];
      this.$store.state.shop.paypal_cart = [];
      this.$store.state.shop.mailingFiles = [];
      this.collapse();

      for(var i in this.billForm) {
        if (Object.hasOwnProperty.call(this.billForm, i)) {
          this.billForm[i] = {};
        }
      }

      window.scrollTo({
        top: 0,
        behavior: "smooth"
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
    setMessage(code, msg, timeout) {
      var self = this;

      self.$store.state.status_message.message = msg;
      self.$store.state.status_message.code = code;
      self.$store.state.status_message.timeout = timeout;

      // self._stepsNav.current = 1;
      self.prePayment = store.state.isLoading = false;      
    },
    setPayment(data) {
      vm._data.shop.proccess.paymentChosen = '' !== data ? data : null;
    },
    cepSeeking(data) {
      var self = this;
      var cep = (data === 'personalAddress') ? self.billForm.address.cep : self.billForm.billingAddress.cartao_cep;

      function clearCep() {
        if (data === 'personalAddress') {
          self.billForm.address.endereco = '';
          self.billForm.address.bairro = '';
          self.billForm.address.estado = '';
          self.billForm.address.cidade = '';
        } else {
          self.billForm.billingAddress.cartao_endereco = '';
          self.billForm.billingAddress.cartao_bairro = '';
          self.billForm.billingAddress.cartao_estado = '';
          self.billForm.billingAddress.cartao_cidade = '';
        }
      }

      clearTimeout(self.timeout);

      self.timeout = setTimeout(function () {
          self.$store.state.isLoading = true;
          buscaCep(cep, {sync: false, timeout: 1000})
            .then(response => {
              self.$store.state.isLoading = false;

              if (data === 'personalAddress') {
                self.billForm.address.endereco = response.logradouro;
                self.billForm.address.bairro = response.bairro;
                self.billForm.address.estado = response.uf;
                self.billForm.address.cidade = response.localidade;
              } else {
                self.billForm.billingAddress.cartao_endereco = response.logradouro;
                self.billForm.billingAddress.cartao_bairro = response.bairro;
                self.billForm.billingAddress.cartao_estado = response.uf;
                self.billForm.billingAddress.cartao_cidade = response.localidade;
              }         
            })
            .catch(erro => {
              self.setMessage(0, `CEP inválido.`);
              self.$store.state.isLoading = false;
              clearCep();
            });  
      }, 1000);    
    },
    nextStep(s) {
      var self = this;

      function next() {
        var edge = self._stepsNav.steps.length;
        var current = self._stepsNav.current;

        self._stepsNav.current++;        
      }

      if(s!=='') {
        clearTimeout(self.timeout);

        self.timeout = setTimeout(function () {
          next();
        }, 1000);          
      } else if(s!==false) {
        next();
      }
    },
    steps(data) {
      this.navigator.steps = data;
    },
    current(data) {
      this.navigator.current = data;
    },
    collapse() {
      this.isCollapsed = !this.isCollapsed;

      this.$emit('collapse', this.isCollapsed);
    },
    _sendForm(json) {
      var self = this;

      axios.get(`http://dev.uppercreative.com.br/pagseguro/http/req.php?json=${json}`)
      .then(function(res) {
        store.state.isLoading = false;
        if (res.data.error) {
          self.setMessage(0, 'Ocorreram erros ao enviar seu formulário.');
        } else {
          if (res.data.code) {
            vm._data.shop.proccess.checkout = res.data;

            console.log(res.data);

            localStorage.setItem("checkout", JSON.stringify(res.data));

            store.state.isLoading = false;

            var msg = '';
            // var emsg = '';

            if(parseInt(vm._data.shop.proccess.checkout.status) === 3) {
              msg = 'Seu pedido foi efetuado com sucesso.';
              // emsg = 'Obrigado por comprar na Violab! O código de sua compra é: '+ vm._data.shop.proccess.checkout.code +'. Segue seu(s) link(s) para download: ';

              // for (const [key, item] of Object.entries(vm._data.shop.proccess.checkout.items)) {
              //   for (const [k, i] of Object.entries(self.$store.state.shop.products)) {
              //     if(item.description === i.title.rendered) {
              //       emsg += i.title.rendered + ' - <a href="'+ i.acf.url +'">('+ i.acf.url +')</a> ';
              //     }
              //   }                
              // }      
            } else {
              msg = 'Seu pedido foi efetuado com sucesso e encontra-se em análise.';
            }            

            self.setMessage(1, msg, 2000);

            var incart = document.querySelectorAll('.incart');

            for (var i = 0; i < incart.length; i++) {
              incart[i].classList.remove('incart')
            }

            self.refreshApp();

            console.log(res);
          }
        }
      })
      .catch(function(err) {
        // console.log(err);
        self.setMessage(0, 'Ocorreram erros ao enviar seu formulário.');
      }); 
    },
    _pay() {
      var self = this;

      store.state.isLoading = true;

      vm._data.shop.proccess.formData = self.billForm;
      vm._data.shop.cart.items = self.$store.state.shop.app_cart;
      vm._data.shop.cart.cart_total = self.$store.state.shop.cart_total;
      vm._data.shop.credentials.token = this.$store.state.shop.pagseguro._credentials._access_token;
      vm._data.shop.credentials.email = this.$store.state.shop.pagseguro._credentials._email;

      vm._data.shop.proccess.seller.email = self.$store.state.shop.pagseguro._app._email; 

      var data = new FormData();

      data.append('email', self.$store.state.shop.pagseguro._credentials._email);

      data.append('token', self.$store.state.shop.pagseguro._credentials._access_token);

      function toDash(str) {
          return str.replace(/[^a-z0-9]+/gi, '-').replace(/^-*|-*$/g, '').toLowerCase();
      }     

      axios.post("http://dev.uppercreative.com.br/pagseguro/session.php", data)
        .then(function (response) {
          console.log(response);

          // console.log(self.$store.state.shop);

          if(response.data.id) {
            // - Setar sessão Pagseguro
            PagSeguroDirectPayment.setSessionId(response.data.id);
            vm._data.shop.proccess.buyer.SSID = response.data.id;
            sessionStorage.setItem('SSID', response.data.id);
            // console.log(self.$store.state.shop);

            // - Identificar métodos de pagamento
            // PagSeguroDirectPayment.getPaymentMethods({
            //   amount: self.$store.state.shop.cart_total,
            //   success: function(response) {
                // console.log(response);

            //     if (response.paymentMethods.CREDIT_CARD.options) {
            //       vm._data.shop.proccess.chosenMethod = response.paymentMethods.CREDIT_CARD.code;

            //       var cards = response.paymentMethods.CREDIT_CARD.options;

            //     }
            //   },
            //   error: function(res) {
            //     self.setMessage(0, 'O formuário não pode ser enviado.');
            //     // console.log(res);
            //   }
            // });
            var cardBin = self.billForm.card.numero_cartao.substring(0,7).replace(" ", "");
            // - Identificar cartão
            PagSeguroDirectPayment.getBrand({
                cardBin: cardBin,
                success: function(response) {
                  console.log(response);
                  var brand = response.brand.name;
                  vm._data.shop.proccess.buyer.paymentData.card = response.brand;

                  if (brand) {
                    // - Gerar dados especificos da bandeira
                    // for (const [key, card] of Object.entries(cards)) {
                    //   if (toDash(card.name) === toDash(brand)) {
                    //     if (card.status === "AVAILABLE") {
                    //       var cardType = card;
                    //       var cardCode = cardType.code;

                         
                    //     }
                    //   }
                    // }   
                    // - Gerar hash do comprador
                    PagSeguroDirectPayment.onSenderHashReady(function(response){
                        console.log(response);

                        if(response.status == 'error') {
                            console.log(response);
                            self.setMessage(0, 'O formuário não pode ser enviado.');
                            return false;
                        }
                        vm._data.shop.proccess.buyer.senderHash = response.senderHash;

                        // - Gerando dados de parcela
                        PagSeguroDirectPayment.getInstallments({
                          amount: self.$store.state.shop.cart_total,
                          maxInstallmentNoInterest: 1,
                          brand: brand,
                          success: function(response){
                            if (response.installments[brand]) {
                              for (const [key, installment] of Object.entries(response.installments[brand])) {
                                if (installment.quantity === 1) {
                                  var installmentData = installment;
                                  var totalAmount = self.formatMoney(installment.totalAmount);

                                  if (totalAmount) {
                                    vm._data.shop.proccess.buyer.paymentData.installments = installmentData;

                                    var form = vm._data.shop.proccess.formData;
                                    var cardNum = String(form.card.numero_cartao.replace(/ /g, ""));
                                    var expiration = form.card.expiracao.split('/');
                                    var cvv = form.card.cvv; 

                                    PagSeguroDirectPayment.createCardToken({
                                      cardNumber: cardNum, // Número do cartão de crédito
                                      brand: brand, // Bandeira do cartão
                                      cvv: cvv, // CVV do cartão
                                      expirationMonth: String(expiration[0]), // Mês da expiração do cartão
                                      expirationYear: String(expiration[1]), // Ano da expiração do cartão, é necessário os 4 dígitos.
                                      success: function(response) {

                                        if (response.card.token) {

                                          console.log(response);

                                          vm._data.shop.proccess.buyer.paymentData.eToken = response.card.token;

                                          // store.state.isLoading = false;
                                          var json = JSON.stringify(vm._data.shop);

                                          self._sendForm(json);
                                        }
                                      },
                                      error: function(response) {
                                        self.setMessage(0, 'Ocorreram erros ao enviar seu formulário.');
                                        console.log(response);
                                      },
                                      complete: function(response) {
                                        // Callback para todas chamadas.
                                      }
                                    });

                                  }
                                }
                              }
                            }
                          },
                          error: function(response) {
                            self.setMessage(0, 'Ocorreram erros ao enviar seu formulário.');
                            console.log(response);
                         },
                         complete: function(response){
                              // Callback para todas chamadas.
                         }
                        });   

                    });                            
                  }
                },
                error: function(response) {
                  self.setMessage(0, 'O formuário não pode ser enviado.');
                  console.log(response);
                  //tratamento do erro
                },
                complete: function(response) {
                  //tratamento comum para todas chamadas
                }
            });              
          }
        })
        .catch(function (res) {
          self.setMessage(0, 'O formuário não pode ser enviado.');
          // console.log(res);
        });
    },
    saveForm: function (event) {
      store.state.isLoading = true;

      var steps = Object.values(this.billForm),
          filled = 0,
          fields = 0;

      for (var i = 0; i < steps.length; i++) {
        for (let value of Object.keys(steps[i])) {
          if(!value.match(/complemento/g)) fields++
        }
        for (let value of Object.values(steps[i])) {
          if (value) filled++;
        }
      }


      setTimeout(()=>{
        if (filled >= fields) {
          this.prePayment = true;
        } else {
          this.$store.state.status_message.message = "Existem campos para serem preenchidos.";
          this.$store.state.status_message.code = 0; 
        }

        console.log(filled + ' ' + fields);

        store.state.isLoading = false;
      }, 2000);
    }
  },
  data () {
    return {
      billForm: {
        personalData: {
          nome: '',
          nascimento: '',
          email: '',
          telefone: '',
          cpfcnpj: ''
        },
        address: {
          cep: '',
          endereco: '',
          numero: '',
          complemento: '',
          bairro: '',
          cidade: '',
          estado: ''
        },
        billingAddress: {
          cartao_complemento: '',
          cartao_bairro: '',
          cartao_cep: '',
          cartao_cidade: '',
          cartao_estado: '',
          cartao_endereco: '',
          cartao_numero: ''
        },
        card: {
          cartao_telefone: '',
          cartao_nome: '',
          nascimento_cartao: '',
          cartao_cpf_cnpj: '',
          numero_cartao: '',
          cvv: '',
          expiracao: ''
        }
      },
      navigator: {
        steps: [],
        current: 1
      },    
      timeout: null,
      isCollapsed: true,
      prePayment: false
    }
  },
  computed: {
    _checkout() {
      return vm._data.shop.proccess.checkout;
    },
    _stepsNav() {
      return this.navigator;
    },
    vm() {
      return vm;
    }
  },
  components: {
    creditCard,
    billingAddress,
    personalAddress,
    personalData,
    stepsNavigator
  }
};
</script>
<style lang="sass">
@import "./sass/_shopCheckout"
</style>