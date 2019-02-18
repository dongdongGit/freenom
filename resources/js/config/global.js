import env from ".././environments/environment.js";

// const baseUri = env.app_env == 'production' ? 'https://freenom.dongdongdoing.tk/' : 'http://localhost:3000/';
const baseUri = env.app_env == 'production' ? process.env.MIX_APP_URL : process.env.MIX_LOCAL_APP_URL;

export default
{
  baseUri,
}
