<style>
.colored-toast.swal2-icon-success {
    background-color: rgba(55, 65, 81, 1) !important;
}
.text-white {
    color: white !important;
  /*background-color: #a5dc86 !important;*/
}
</style>
<template>
    <div>
        <loading :active.sync="isLoading" 
        :can-cancel="false"
        :is-full-page="fullPage"></loading>
        <vue-confirm-dialog></vue-confirm-dialog>
        <div class="w-full">
            <div class="p-4 rounded-full flex justify-between">
                <h1 class="ml-2 font-bold uppercase">Cart</h1>
                <h1 class="ml-2 font-bold uppercase">{{ cartItems.length }} items</h1>
            </div>
        </div>
        <table class="w-full text-sm lg:text-base" cellspacing="0">
            <thead class="bg-gray-700 text-white ">
                <tr class="h-12 uppercase">
                    <th class="hidden md:table-cell"></th>
                    <th class="text-left">Product</th>
                    <th class="lg:text-left text-left pl-5 lg:pl-0">
                        <span class="lg:hidden" title="Quantity">Qtd</span>
                        <span class="hidden lg:inline">Quantity</span>
                    </th>
                    <th class="hidden text-left md:table-cell">Unit price</th>
                    <th class="text-left">Total price</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="!(cartItems.length > 0)">
                    <td colspan="5"><div class="py-4 flex justify-center text-3xl font-extrabold text-gray-700">Nothing</div></td>
                </tr>
                <tr v-else v-for="item, index in cartItems">
                    <td class="hidden pb-4 md:table-cell">
                  <a href="#">
                    <img :src="item.product.product_image" class="w-20 rounded" alt="Thumbnail">
                  </a>
                    </td>
                    <td>
                        <a href="#">
                            <p class="mb-2 md:ml-4">{{ item.product.product_name }}</p>
                            <form action="" method="POST">
                                <button type="button" class="text-gray-700 md:ml-4" @click="deleteItem(index,item.product_id,item.product.product_name)">
                                    <small>(Remove item)</small>
                                </button>
                            </form>
                        </a>
                    </td>
                    <td class="justify-start items-center md:flex mt-6">
                        <div class="w-20 h-10">
                            <div class="relative flex flex-row w-full h-8">
                                <input type="number" class="text-center rounded-lg border-transparent flex-1 appearance-none border border-gray-300 w-full py-2 px-0 bg-white text-gray-700 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:ring-gray-600 focus:border-transparent"  v-model="item.quantity" @change="changeQty(item.product_id, item.quantity)" min="1" max="100" step="1"/>
                            </div>
                        </div>
                    </td>
                    <td class="hidden text-left md:table-cell">
                        <span class="text-sm lg:text-base font-medium">
                            {{ item.product.product_price | toCurrency }}
                        </span>
                    </td>
                    <td class="text-left">
                        <span class="text-sm lg:text-base font-medium">
                            {{item.quantity * item.product.product_price | toCurrency}}
                        </span>
                    </td>
                </tr> 
            </tbody>
        </table>
    </div>
</template>

<script>
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
    export default {
        components: {
            Loading,
        },
        data() {
            return {
                cartItems: [],
                isLoading: false,
                fullPage: true,
            }
        },
        mounted() {
            var app = this;
            app.isLoading = true;
            axios.post('/carts', {
                    secret: document.querySelector("meta[name='api-token']").getAttribute('content'),
                },{
                    _token: app.$csrfToken,
                })
                .then(function (resp) {
                    app.cartItems = resp.data;
                    app.$root.$emit("updateTotal",app.cartItems);
                    app.isLoading = false;
                    //console.log(resp);
                })
                .catch(function () {
                    alert("Could not load your product")
                });
        },
        methods: {
            changeQty: function (product_id,quantity) {
                var app = this;
                app.isLoading = true;
                axios.post('/carts/change-quantity', {
                    product_id: product_id,
                    quantity: quantity,
                    secret: document.querySelector("meta[name='api-token']").getAttribute('content'),
                },{
                    _token: app.$csrfToken,
                })
                .then(function (resp) {
                    app.isLoading = false;
                    app.$root.$emit("updateTotal",app.cartItems);
                    //console.log(resp);
                })
                .catch(function () {
                    app.isLoading = false;
                    app.$swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      text: 'Something went wrong!',
                    });
                });
            },
            deleteItem: function (index,product_id, product_name) {
                var app = this;
                const Toast = app.$swal.mixin({
                  toast: true,
                  position: 'center',
                  iconColor: 'white',
                  customClass: {
                    popup: 'colored-toast',
                    title: 'text-white',
                  },
                  showClass: {
                    popup: 'animate__animated animate__jackInTheBox animate__fast'
                  },
                  hideClass: {
                    popup: 'animate__animated animate__fadeOut animate__fast'
                  },
                  showConfirmButton: false,
                  timer: 1500
                })
                this.$confirm({
                    title: 'Are you sure?',
                    message: 'Are you sure you want to remove '+ product_name + ' from the cart ?',
                    button: {
                        no: 'No',
                        yes: 'Yes'
                    },
                    /**
                    * Callback Function
                    * @param {Boolean} confirm 
                    */
                    callback: a => {
                        if (a) {
                            app.isLoading = true;
                            axios.post('/carts/remove-item', {
                                product_id: product_id,
                                secret: document.querySelector("meta[name='api-token']").getAttribute('content'),
                            },{
                                _token: app.$csrfToken,
                            })
                            .then(function (resp) {
                                app.isLoading = false;
                                app.cartItems.splice(index, 1);
                                app.$root.$emit("updateTotal",app.cartItems);
                                Toast.fire({
                                  icon: 'success',
                                  title: 'Item has been deleted to your cart'
                                })
                                //console.log(resp);
                            })
                            .catch(function () {
                                app.isLoading = false;
                                alert("Could not load your product")
                            });
                        }
                    },
                });
            },
        },
        computed: {
            total: function(){
                let sum = 0;
                this.cartItem.forEach(function(item) {
                    sum+= item.quantity;
                });

                return sum;
            }
        },
    }
</script>
