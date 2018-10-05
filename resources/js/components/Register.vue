<template>
  <div>
    <div 
      v-if="success"       class="notification is-success">      <p>Registration successful. You can now <router-link :to="{name:'login'}">login.</router-link></p>
    </div>
    <form   class="form-signin"    v-if="!success"       autocomplete="on"       method="post"       @submit.prevent="register">
      <div class="field">
        <label           for="name"           class="label">Username</label>
        <input           id="name"           v-model="name"           :class="[{ 'is-danger': $root.error && $root.errors.name }, 'input']" type="text" 
          required>
      </div>
      <div class="field">
        <label           for="email"           class="label">E-mail</label>
        <input           id="email"           v-model="email"           :class="[{ 'is-danger': $root.error && $root.errors.email }, 'input']" 
          type="email"           placeholder="user@example.com"           required>
      </div>
      <div class="field">
        <label 
          for="password" 
          class="label">Password</label>
        <input 
          id="password" 
          v-model="password" 
          :class="[{ 'is-danger': $root.error && $root.errors.password }, 'input']" 
          type="password" 
          required>
      </div>
      <button 
        type="submit" 
        class="button">Register</button>
    </form>
  </div>
</template>
<script>
    export default {
        data(){
            return {
                name: '',
                email: '',
                password: '',
                success: false
            };
        },
        methods: {
            register(){
                var app = this
                this.$auth.register({
                    params: {
                        name: app.name,
                        email: app.email,
                        password: app.password
                    },
                    success: function () {
                        app.success = true
                    },
                    error: function () {},
                    redirect: null
                });
            }
        }
    }
</script>
<style scoped>
    .form-wrapper {
        min-height: 100%;
        min-height: 100vh;
        display: flex;
        align-items: center;
    }
    .form-signin {
        width: 100%;
        max-width: 330px;
        padding: 15px;
        margin: 0 auto;
    }
    .form-signin .form-control {
        position: relative;
        box-sizing: border-box;
        height: auto;
        padding: 10px;
        font-size: 16px;
    }
    .form-signin .form-control:focus {
        z-index: 2;
    }
    .form-signin input[type="email"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }
    .form-signin input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
</style>