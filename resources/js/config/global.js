import env from ".././environments/environment.js";

const baseUri = env.production ? 'https://freenom.dongdongdoing.tk/' : 'http://localhost:3000/';

export default
{
  baseUri,
}
