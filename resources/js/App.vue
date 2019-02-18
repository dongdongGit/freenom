<template>
  <div id="app">
    <el-container v-bind:class="isCollapse ? 'nav-folder' : ''">
      <el-header>
        <div class="nav-logo">
          <router-link to="/">
            <b>
              <img :src="path + 'assets/img/logo.png'" alt>
            </b>
            <span class="logo">
              <img :src="path + 'assets/img/logo-text.png'" alt>
            </span>
          </router-link>
        </div>
        <ul class="nav-left">
          <li>
            <a class="sidenav-fold-toggler" v-on:click="toggle()">
              <i class="lni-menu"></i>
            </a>
            <a class="sidenav-expand-toggler" v-on:click="open()">
              <i class="lni-menu"></i>
            </a>
          </li>
        </ul>
        <el-dropdown class="nav-right">
          <i class="el-icon-setting" style="margin-right: 15px"></i>
          <span>{{user}}</span>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item>
              <a v-on:click="logout()">登出</a>
            </el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </el-header>
      <el-container>
        <el-aside width>
          <el-menu
            background-color="#1a2942"
            text-color="#99abb4"
            active-text-color="#ffd04b"
            :collapse="isCollapse"
            class="left-menu"
            router
          >
            <el-menu-item index="0" route="/">
              <i class="el-icon-menu"></i>
              <span slot="title">首页</span>
            </el-menu-item>
            <el-menu-item index="1" route="/freenom">
              <i class="el-icon-setting"></i>
              <span slot="title">域名</span>
            </el-menu-item>
          </el-menu>
        </el-aside>
        <el-container class="right-menu">
          <el-main>
            <transition name="fade" mode="out-in">
              <router-view></router-view>
            </transition>
          </el-main>
        </el-container>
      </el-container>
    </el-container>
  </div>
</template>

<script>
export default {
  name: "app",
  data() {
    return {
      user: null,
      isCollapse: false,
      path: this.GLOBAL.baseUri
    };
  },
  created() {
    this.init()
  },
  methods: {
    init () {
      console.log('init');
    },
    toggle() {
      this.isCollapse = !this.isCollapse;
    },
    open() {
      console.log('open');
    },
    logout() {
      console.log('logout');
    }
  }
};
</script>

<style scoped lang="scss">
@import '~@/admin/freenom';
</style>
