// import axios from 'axios';
import global from '.././config/global.js';
import qs from 'qs'
import {
  Loading,
  Message
} from 'element-ui';
require('../bootstrap');

const axiosInstance = axios.create();
axiosInstance.defaults.timeout = 6000;
axiosInstance.defaults.baseURL = global.baseUri;

var elementLoading = Loading.service
var loading;

function startLoading() {
  loading = elementLoading({
    lock: true,
    target: document.querySelector('.loading')//设置加载动画区域
  });
}

function endLoading() {
  loading.close();
}

let needLoadingRequestCount = 0;
function showFullScreenLoading() {
  if (needLoadingRequestCount === 0) {
    startLoading();
  }
  needLoadingRequestCount++;
};
function tryHideFullScreenLoading() {
  if (needLoadingRequestCount <= 0) return;
  needLoadingRequestCount--;
  if (needLoadingRequestCount === 0) {
    endLoading();
  }
};

axiosInstance.interceptors.request.use(function (config) {
  showFullScreenLoading();
  return config;
}, function (error) {
  tryHideFullScreenLoading();
  return Promise.reject(error);
});
axiosInstance.interceptors.response.use(function (res) {
  tryHideFullScreenLoading();
  return res.data;
}, function (error) {
  tryHideFullScreenLoading();
  var result = JSON.parse(error.request.response);
  switch (error.request.status) {
    case 401:
      Message({
        showClose: true,
        message: '登录信息已经过期',
        type: "info",
      });

      break;
    case 422:
      let message = '';
      result.data.forEach(element => {
        message = message + '<p>' + element['content'] + "</p>";
      });

      Message({
        showClose: true,
        dangerouslyUseHTMLString: true,
        message: message,
        type: "error",
        duration: 10000
      });

      break;
    case 500:
      Message({
        showClose: true,
        message: result.message || "请求错误",
        type: "error"
      });
      break;
    default:
      break;
  }

  return Promise.reject(error);
});

const METHOD = Array.of(
  'request',
  'get',
  'post',
  'put',
  'patch',
  'head',
  'options',
  'delete'
);

export default {
   req(url, ...params) {
    console.log(axiosInstance);
    // if (!METHOD.indexOf(methodName.toLowerCase())) {
    //   return;
    // }

    return new Promise((resolve,reject) => {
      axiosInstance.call(url, ...params)
        .then(res => {
          resolve(res)
        })
        .catch(error => {
          reject(error)
        });
      })
  },
  get(url, ...params) {
    return new Promise((resolve,reject) => {
      axiosInstance.get(url)
        .then(res => {
          resolve(res)
        }, err => {
          reject(err)
        })
    }).then(response => {
      console.log(response);
    });
  }
}