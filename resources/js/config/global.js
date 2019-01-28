import env from ".././environments/environment.js";

const baseUri = env.production ? 'http://test.freenom.local/' : 'http://localhost:3000/';

export default
{
  baseUri,
}
