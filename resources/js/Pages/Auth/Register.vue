<template>
  <div class="login-box">
    <form class="w-full form-signin md:w-1/2" @submit.prevent="login">
      <h3 class="login-title">Create an account</h3>

      <div
        class="form-group"
        :class="{ 'form-group--error': user.email }"
      >
        <label for="email">Email</label>
        <p :class="{ control: true }">
          <input
            v-model.trim="user.email"
            class="form-control input"
            :class="{ 'is-danger': false }"
            name="email"
            type="text"
            required
            @keydown.enter="login"
          />
        </p>
      </div>

      <div
        class="form-group"
        :class="{ 'form-group--error': user.email.$error }"
      >
        <label for="email">name</label>
        <p :class="{ control: true }">
          <input
            v-model.trim="user.name"
            class="form-control input"
            :class="{ 'is-danger': false }"
            name="name"
            type="text"
            required
            @keydown.enter="login"
          />
        </p>
      </div>

      <div class="form-group"
       :class="{ 'form-group--error': user.password.$error }"
      >
        <label for="password" class="password-label"
          ><span>Password</span></label
        >
        <p :class="{ control: true }">
          <input
            type="password"
            id="password"
            v-model="user.password"
            class="form-control input"
            :class="{ 'is-danger': false }"
            name="password"
            required
            @keydown.enter="login"
          />
          <small v-if="user.password.$error"> password have to be at least 8 </small>
        </p>
      </div>

      <div class="form-group"
       :class="{ 'form-group--error': user.password_confirmation.$error }">
        <label for="password" class="password-label"
          ><span>Confirm Password</span></label
        >
        <p :class="{ control: true }">
          <input
            type="password"
            id="password-confirm"
            v-model="user.password_confirmation"
            class="form-control input"
            :class="{ 'is-danger': false }"
            name="password-confirm"
            required
            @keydown.enter="login"
          />
          <small v-if="user.password_confirmation.$error"> password and confirm password have to be the same </small>
        </p>
      </div>

      <button
        class="btn btn-action"
        type="submit"
        @click.prevent="login"
      >
        Sign Up
        <i v-if="isLoading" class="ml-2 fa fa-spinner fa-pulse"></i>
      </button>
      <p class="text-center">
        <small>
          Already have an account?
          <inertia-link href="login">Login</inertia-link>
        </small>
      </p>
      <p class="text-center copyrights">&copy; 2020-{{ currentYear }}</p>
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      user: {
        email: "",
        name: "",
        password: "",
        password_confirmation: ""
      },
      isLoading: false
    };
  },

  computed: {
    currentYear() {
      const date = new Date();
      return date.getFullYear();
    }
  },
  methods: {
    login() {
      if (!this.isLoading) {
        this.isLoading = true;
        axios
          .post("/register", this.user)
          .then(() => {
               this.$inertia.visit('dashboard')
          })
          .catch(err => {
              console.log(err);
          })
          .finally(() => {
            this.isLoading = false;
          });
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.login-body {
    background: #fdfdff;
    background-size: cover;
}

.login-box {
    min-height: 100vh;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(#1fa1d0, #087a9c);
    background: url(../../../img/clouds.jpg);
    background-size: cover;
    filter: blur(5px), grayscale(70%);
    position: relative;

    &::after {
        content: "";
        display: block;
        position: absolute;
        width: 100%;
        top: 0;
        background: rgba(0, 0, 0, 0.5);
        left: 0;
        height: 100%;
    }

    form {
        color: white;
        padding: 15px;
        max-width: 350px;
        border-radius: 4px;
        // box-shadow: 0 0 10px 4px rgba($color: #000000, $alpha: 0.2);
        z-index: 2;
    }

    div {
        text-align: left;
    }

    .btn-action {
        background:#087a9c;
        width: 100%;
        color: white;
        border: none;
        margin: 10px 0;
        border-radius: 0 0 0 0 !important;
        transition: all ease 0.3s;
        border: 2px solid white;
        height: 37px;

        &:hover {
            background: #1fa1d0;
        }
    }

    input {
        border-radius: 0 0 0 0;
        font-weight: bolder;
        min-width: 250px;
        width: 100%;
        height: 37px;

        &:hover,
        &:focus {
            //
        }
    }

    .login-title {
        @apply text-3xl;
        text-align: center;
        margin-top: 15px;
        margin-bottom: 20px;
    }

    .password-label {
        display: flex;
        justify-content: space-between;
    }
}

.copyrights {
    @apply text-gray-100;
    height: 10vh;
    text-align: center;
    margin: 5px 0;
    p {
        margin: 0;
    }
}

.splash-screen {
    top: 0;
    left: 0;
    color: #fff;
    background: var(--primary-color);
    position: absolute;
    height: 100vh;
}

.splash-logo {
    width: 300px;
}

.form-group {
    margin: 15px 0;

    label {
        margin: 0.5rem 0;
    };

    &--error {
        @apply text-red-400;
        input {
            @apply shadow-sm border-2 border-red-300;
        }
    }
}
.form-control {
    @apply text-gray-400 px-2;

    &:focus {
        outline: none;
    }
}

@keyframes fadeSplash {
    0% {
        opacity: 1;
    }
    60% {
        opacity: 1;
    }
    100% {
        opacity: 0;
    }
}

@keyframes loading-circle {
    0% {
        transform: rotate(0deg);
    }
    60% {
        transform: rotate(90deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

@media (max-width: 768px) {
    .login-body .login-box div {
        margin-right: 0;
    }
}
</style>
