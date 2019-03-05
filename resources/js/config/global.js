import env from ".././environments/environment.js";

const baseUri = env.env == 'production' ? process.env.MIX_APP_URL : process.env.MIX_LOCAL_APP_URL;

export default {
    baseUri,
}
