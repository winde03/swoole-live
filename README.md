# swoole-based-live-room
##直播、聊天室功能

学习重点为Swoole，代码省去了与数据库交互的内容

体验地址 
```
http://ltfnevergiveup.cn:8811/live/detail.html
http://ltfnevergiveup.cn:8811/admin/live.html
```

开启websocket服务
```
php /swoole-live/server/ws.php 
```
开启监控（若不需要进行监控可不开）
```
php /swoole-live/server/monitor.php
```
***.com:8811/admin/live.html为直播员页面

***.com:8811/live/detail.html为用户查看的页面，可接收直播员即时发送的数据，并有聊天室功能

环境依赖：php版本 > 7， swoole, redis

####后期优化内容：日志追踪、用户行为记录、优化http（减少资源损耗)，服务器平滑重启、负载均衡（详见/server/负载均衡.md）
