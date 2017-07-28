http://f23ko.com/656

test2.php

echo “start<BR>\n";
$fp= popen(“nohup php /var/www/test.php > /dev/null &",’w’);
pclose($fp);
echo “OK “.date(“Y-m-d H:i:s");

test.php

$fp = fopen(‘/var/www/temp/output.txt’, ‘w’);
fwrite($fp, “START “.date(“Y-m-d H:i:s")." \n");
sleep(40);
fwrite($fp, “END “.date(“Y-m-d H:i:s")." \n");
fclose($fp);


https://blog.toright.com/posts/3639/php-%E5%88%A9%E7%94%A8-ignore_user_abort-pcntl_fork-%E5%AF%A6%E4%BD%9C%E8%83%8C%E6%99%AF%E5%9F%B7%E8%A1%8C-%E4%BE%86%E4%BE%86%E5%93%A5%E7%AF%84%E4%BE%8B.html



docker 

https://phpdocker.io/generator

http://www.jianshu.com/p/f11ab4e3d84a

https://coding.net/u/liuwill/p/docker-compose-php/git

https://github.com/lalamove/challenge/blob/master/backend.md


TSP
http://rossscrivener.co.uk/blog/travelling-salesman-problem/
https://github.com/wdalmut/tsp-genetic-algorithm

api
AIzaSyDklZABjWfNoMGyIA1z-DQkrQQ7uqE9n1k

cd C:\Users\joe\Documents\testcompose

docker-compose up