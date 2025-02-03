<template>
  <body class="hold-transition login-page">
    <div class="login-box">
      <div class="card card-outline card-primary">
        <div class="card-header text-center">
          <a href="#" class="h1"><b>Login</b></a>
        </div>
        <div class="card-body">
          <p class="login-box-msg">Sign in to start your session</p>
          <form @submit.prevent="login">
            <div class="input-group mb-3">
              <input
                name="email"
                v-model="user.email"
                type="email"
                class="form-control"
                placeholder="Email"
                autocomplete="email"
                required
              />
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input
                name="password"
                v-model="user.password"
                type="password"
                class="form-control"
                placeholder="Password"
                autocomplete="current-password"
              />
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">
                  Submit
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>
</template>

<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import axios from "../../../plugins/axios";
import { useAuthStore } from "../../../stores/auth";
import { useProductsStore } from "../../../stores/products";
import { useSalesStore } from "../../../stores/sales";
import { useReportsStore } from "../../../stores/reports";
import { usePurchasesStore } from "../../../stores/purchases";
import { useCategoriesStore } from "../../../stores/categories";

//inisialisasi vue router on Composition API
const router = useRouter();

//inisialisasi store
const store = useAuthStore();
const productsStore = useProductsStore();
const salesStore = useSalesStore();
const reportsStore = useReportsStore();
const purchasesStore = usePurchasesStore();
const categoriesStore = useCategoriesStore();

//state user
const user = reactive({
  email: "",
  password: "",
});

const goBack = () => {
  if (window.history.length > 1) {
    window.history.back();
  } else {
    router.push({ name: "home" });
  }
};

const login = async () => {
  // Define variables
  const email = user.email;
  const password = user.password;

  try {
    // Send login request to the server with axios
    const response = await axios.post("/login", {
      email,
      password,
    });
    store.login(response.user, response.token);
    const role = response.user.roles[0].name;
    if (role == "Admin") {
      await purchasesStore.getPurchases();
    }
    await reportsStore.getSales2();
    await store.getAuth();
    await productsStore.getProducts();
    await salesStore.getSales();
    await categoriesStore.getCategories();

    // Show success message
    swal({
      title: "SUCCESS!",
      text: response.message,
      icon: "success",
      timer: 5000,
      buttons: false,
    });
    const redirectTo = router.currentRoute.value.query.redirect || {
      name: "home",
    };
    router.push(redirectTo);
  } catch (err) {
    console.log("Failed to Login", err);

    // Determine the error message
    let error;
    if (err.message) {
      error = err.message;
    } else {
      error = err.data.message || "An unknown error occurred";
    }

    // Show error message
    swal({
      title: "ERROR!",
      text: error,
      icon: "error",
      timer: 5000,
      buttons: false,
    });
  }
};
</script>
