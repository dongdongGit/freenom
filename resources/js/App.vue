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
            <el-menu-item index="1" route="/index">
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
.el-header {
  position: fixed;
  color: #333;
  line-height: 60px;
  display: block;
  width: 100%;
  padding: 0;
  z-index: 1040;
  background-color: #fff;
  border-bottom: 1px solid #e9eaec;
  margin-bottom: 0;
  transition: all 0.2s ease;
  -webkit-transition: all 0.2s ease;
  -moz-transition: all 0.2s ease;
  -o-transition: all 0.2s ease;
  -ms-transition: all 0.2s ease;
  .nav-logo {
    float: left;
    width: 270px;
    a {
      color: #fff;
      font-size: 18px;
      padding-left: 0;
    }

    b {
      height: 60px;
      display: inline-block;
      width: 60px;
      line-height: 60px;
      text-align: center;
    }
  }
  .nav-left {
    float: left;
    position: relative;
    list-style: none;
    padding-left: 0;
    margin-bottom: 0;
    li {
      a {
        padding: 0 6px;
        line-height: calc(65px - 3px);
        min-height: calc(65px - 3px);
        color: #8a8a8a;
        display: block;
        transition: all 0.2s ease-in-out;
        -webkit-transition: all 0.2s ease-in-out;
        -moz-transition: all 0.2s ease-in-out;
        -o-transition: all 0.2s ease-in-out;
        -ms-transition: all 0.2s ease-in-out;
        i.lni-menu {
          &::before {
            content: "\e9b9";
          }
        }
        i {
          font-size: 18px;
          vertical-align: middle;
          color: #fff;
          border-radius: 50%;
          border: 1px solid #f1f1f1;
          padding: 8px;
          color: #999;
          transition: all 0.3s ease-in-out;
          -webkit-transition: all 0.3s ease-in-out;
          -moz-transition: all 0.3s ease-in-out;
          -o-transition: all 0.3s ease-in-out;
          -ms-transition: all 0.3s ease-in-out;
          &:hover {
            text-decoration: none;
            background: #1a2942;
            border-color: #1a2942;
            color: #fff;
          }
        }
      }
    }
  }
  .nav-right {
    float: right;
    margin-right: 30px;
    position: relative;
    list-style: none;
    padding-left: 0;
    margin-bottom: 0;
  }
  .right {
    float: right;
  }
}

.nav-folder {
  .el-header {
    .nav-logo {
      width: 70px;
      padding: 0;
      a {
        .logo {
          width: 70px;
          display: none;
        }
      }
    }
  }
  .nav-left {
    li > a > i.lni-menu {
      &::before {
        content: "\e914";
      }
    }
  }
}

.el-aside {
  color: #333;
  z-index: 1000;
  top: 60px;
  bottom: 0;
  position: fixed;
  overflow: hidden;
  transition: all 0.2s ease;
  -webkit-transition: all 0.2s ease;
  .left-menu {
    position: relative;
    list-style: none;
    margin: 0;
    padding-left: 0;
    overflow: auto;
    height: calc(100vh - 60px);
    border-right: 1px solid #e9eaec;
  }
}

.right-menu {
  min-height: 100vh;
  padding-left: 250px;
  transition: all 0.2s ease;
  -webkit-transition: all 0.2s ease;
}

.el-main {
  min-height: 100vh;
  // padding-left: 250px;
  transition: all 0.2s ease;
  -webkit-transition: all 0.2s ease;
  -moz-transition: all 0.2s ease;
  -o-transition: all 0.2s ease;
  -ms-transition: all 0.2s ease;
  padding: calc(50px + 35px) 15px 15px;
  min-height: calc(100vh);
  background: #f1f2f7;
  width: 100%;
  flex-grow: 1;
}

.left-menu:not(.el-menu--collapse) {
  width: 250px;
  height: calc(100vh - 60px);
}

@media only screen and (min-width: 767px) {
  .nav-left > li > a.sidenav-fold-toggler,
  .nav-right > li > a.sidenav-fold-toggler {
    display: block;
  }
  .nav-left > li > a.sidenav-expand-toggler,
  .nav-right > li > a.sidenav-expand-toggler {
    display: none;
  }
}

@media only screen and (min-width: 992px) {
  .nav-folder {
    .right-menu {
      padding-left: 65px;
    }
  }
}
</style>
