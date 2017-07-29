# version 3

```php
cd PROJECT_FORDER
docker-compose up
```
### Demo
Easy Demo(just for test and development use):
```php
/demo
```




### Add 3 features for scalability
  1. Plugin
		- The api of this task is one of plugin. (RouteApi),
```php
// Config/config.php
PluginLoader::load('RouteApi',array('config'=>true,'router'=>true,'event'=>true));
```
  2. Event
		- For extra api requirement, like,
			- Inform error
			- Analysis
```php
// Plugin\RouteApi\Config\event.php :
EventEmitter::on('RouteApi.track_failure', function ($data,$errorLog) {
				common::log($errorLog);
			});
      
//in controller :
EventEmitter::emit('RouteApi.track_failure', [$data,$errorLog]);
```
  3. Accept two input body format
```php
//josn text,  use `php://input` on server side to get data :
[[22.33165775850116,114.20339584350586],[22.315142958169385,114.16906356811523]]


//post array :
paths[0][]:22.33165775850116
paths[0][]:114.20339584350586
paths[1][]:22.315142958169385
paths[1][]:114.16906356811523
```
	

		
