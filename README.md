#后台

##重要文件说明
	* \Application\Home 为后台主要目录,分为MVC三层,其中Model层分为Logic和Model,Logic存放涉及后台系统运营代码(如用户管理、角色管理、节点管理等),Model层主要存放与游戏相关的代码(如游戏数据统计、管理等)。
	* \Application\Common\Conf\config.php  为整个系统数据库、异步通知接口配置文件。
	* \Application\HomeSocketAsyncController.class.php 文件为所有异步处理和系统crontab定时(次/天)执行接口。
	* \Crontab 对于涉及到统计、接口监控等频率较高(小时、分钟)代码,均使用workerman 框架实现。相关代码保存在此目录下,数据库等配置具有单独的\Crontab\Config.php文件。
	>sudo php start.php start


