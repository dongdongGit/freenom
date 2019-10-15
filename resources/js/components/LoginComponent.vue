<template>
  <div class="loading">
    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
      <div class="container">
        <a class="navbar-brand">test</a>
        <button
          class="navbar-toggler"
          type="button"
          data-toggle="collapse"
          data-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent"
          aria-expanded="false"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <!-- Left Side Of Navbar -->
          <ul class="navbar-nav mr-auto"></ul>

          <!-- Right Side Of Navbar -->
          <ul class="navbar-nav ml-auto"></ul>
        </div>
      </div>
    </nav>

    <main class="py-4">
      <div class="wrapper-page">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-5 col-md-12 col-xs-12">
              <div class="card">
                <div class="card-header border-bottom text-center">
                  <h4 class="card-title">Login</h4>
                </div>
                <div class="card-body">
                  <el-form
                    :model="ruleForm"
                    status-icon
                    :rules="rules"
                    ref="ruleForm"
                    class="demo-ruleForm"
                  >
                    <el-form-item prop="email" :error="ruleErrors.email">
                      <el-input
                        type="string"
                        v-model="ruleForm.email"
                        autocomplete="off"
                        placeholder="E-Mail Address..."
                      ></el-input>
                    </el-form-item>
                    <el-form-item prop="password" :error="ruleErrors.password">
                      <el-input
                        type="password"
                        v-model="ruleForm.password"
                        autocomplete="off"
                        placeholder="password..."
                      ></el-input>
                    </el-form-item>
                    <el-form-item>
                      <button class="btn btn-common btn-block" @click="submitForm('ruleForm')">提交</button>
                    </el-form-item>
                  </el-form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
export default {
  data() {
    return {
      ruleForm: {
        email: "",
        password: ""
      },
      rules: {
        email: [ 
          { required: true, message: '请输入账号', trigger: 'blur' },
          { type: 'email', message: '输入邮箱格式错误', trigger: 'blur'}
        ],
        password: [
          { required: true, message: '请输入密码', trigger: 'blur' }
        ],
      },
      ruleErrors: {
        email: "",
        password: ""
      }
    };
  },
  methods: {
    submitForm(formName) {
      let self = this;
      this.clearErrorsForm();
      this.$refs[formName].validate(valid => {
        if (valid) {
          let form = self.$refs[formName].$el;
          let formData = new FormData(form);
          for (const [key, value] of Object.entries(self.ruleForm)) {
            formData.append(key, value);
          }

          return self.$http
            .post("api/admin/login", formData)
            .then(function(response) {
              let token = response.data.token
              self.$http.defaults.headers.common['Authorization'] = 'Bearer ' + token;
              self.$unit.setCache('auth_token', self.count, response.data.expires_in);
              self.$router.push('/');
            })
            .catch(function(error) {
              let result = JSON.parse(error.request.response);

              if (error.request.status === 422) {
                result.data.forEach(function (item, index) {
                  let field = item.field;
                  self.ruleErrors[field] = item.content;
                });
                console.log(self.ruleErrors);
              }
            });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },

    resetForm(formName) {
      this.$refs[formName].resetFields();
    },

    clearValidateForm(formName) {
      this.$refs[formName].clearValidate();
    },

    clearErrorsForm() {
      for (const key in this.ruleErrors) {
        this.ruleErrors[key] = "";
      }
    }
  }
};
</script>