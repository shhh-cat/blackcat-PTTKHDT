<template>
        <button @click.prevent="addToCart">Add to cart</button>
</template>
<style>
.colored-toast.swal2-icon-success {
    background-color: rgba(55, 65, 81, 1) !important;
}
.text-white {
    color: white !important;
  /*background-color: #a5dc86 !important;*/
}
/*

 
.colored-toast.swal2-icon-error {
  background-color: #f27474 !important;
}
 
.colored-toast.swal2-icon-warning {
  background-color: #f8bb86 !important;
}
 
.colored-toast.swal2-icon-info {
  background-color: #3fc3ee !important;
}
 
.colored-toast.swal2-icon-question {
  background-color: #87adbd !important;
}
 
.colored-toast .swal2-title {
  color: white;
}
 
.colored-toast .swal2-close {
  color: white;
}
 
.colored-toast .swal2-html-container {
  color: white;
}*/
</style>
<script>
    export default {
        props: ['product_id','quantity'],
        data() {
            return {
                data: {
                    product_id: this.product_id,
                    quantity: this.quantity,
                    secret: document.querySelector("meta[name='api-token']").getAttribute('content'),
                }
            }
        },
        mounted() {
            this.$root.$on("updateQuantity", (qty) => {
                this.data.quantity = qty;
            });
        },
        methods: {
            addToCart() {
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
                var data = app.data;
                if (app.data.secret === '') 
                    window.location.href = '/login'
                axios.post('/carts/add', data,{
                    _token: app.$csrfToken,
                })
                    .then(function (resp) {
                        app.$root.$emit("updateCount")
                        Toast.fire({
                          icon: 'success',
                          title: 'Item has been added to your cart'
                        })
                        //console.log(resp);
                    })
                    .catch(function () {
                        alert("Could not load your product")
                    });
            }
        },
    }
</script>
