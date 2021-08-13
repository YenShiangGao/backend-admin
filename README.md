# 環境
* php: 7.4.*
* laravel: 8.*

# local建置流程
1. 在 `docker/www/*` 目錄下git clone backend-admin專案
   * 需對應nginx設定, 資料夾名稱不可變動
   * ex. `fubo-docker/www/backend-admin`
2. 複製 `backend-admin/env/.env.local` 設定到api專案根目錄 `backend-admin/*` 下, 並將檔案更名為 `.env`
3. 進到 docker php7.4 container內的api專案目錄
   * 開啟Terminal, 在docker目錄底下執行指令 `docker ps -a`查看php7.4 container id
      * ex. `container_id=674a87c9e6c8   image=fubo-docker_fubo-php74`
   * 執行指令 `docker exec -it 674a87c9e6c8 bash` 進入 php7.4 container
   * 執行指令 `cd /var/www/backend-admin` 進入api專案目錄
      * `fubo-docker/www/*` 一律對應到 container 內的 `/var/www/`
4. 在`container`內執行指令建置api環境
   * **所有php artisan 及 composer指令一律都在`container`內執行**
   * 安裝composer套件
     * 執行指令 `composer install`
   * 建立db migration table
     * 須先進入phpmyadmin建立`fubo_admin` database
        * 編碼 `utf8mb4` & `utf8mb4_unicode_ci` 
     * 建立 migration log db table  
        * 執行指令 `php artisan migrate:install`
     * 建立所有業務邏輯db table   
        * 執行指令 `php artisan migrate`
   * 建立admin account
     * 執行指令 `php artisan db:seed --class=CreateAdminSeeder`
     * 帳號/密碼: admin // qwe123
   * 建立權限且賦予給高權限角色, 且將`高權限角色`綁定在admin帳號
     * 執行指令 `php artisan db:seed --class=BasePermissionSeeder`
5. 檢查
   * 開啟 api domain `http://localhost:51805/` 是否能正確顯示
      * ex.顯示 `fubo admin, 2021-06-23 14:03:25`
   * 使用`admin`帳號 call login api是否能正確登入
      * 可在本機terminal測試
      * 正確的話api會回應json response  
      * `curl -d 'account=admin&password=qwe123' -X POST 'http://localhost:51805/v1/login'`


    
    
