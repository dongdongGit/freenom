## Freenom 
freenom auto renew
   
      cp .env.example .env // 配置数据库信息
      composer install --no-interaction --no-dev --prefer-dist
      php artisan migrate
      php artisan config:freenom
      npm install 
      npm run prod
## Todo
 - 前端
   - [ ] image upload
   - [ ] Test
   - [ ] vue middleware
 - 后端
   - image
      - [ ] batch operations
   - freenom auto service 
      - [ ] capture exception
      - [ ] log
         - [ ] generate
         - [ ] level
      - [x] 同步域名优化
      - [ ] create composer package
      - [ ] Mail notifications
   - [ ] Test