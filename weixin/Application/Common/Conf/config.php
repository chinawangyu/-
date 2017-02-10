<?php

$gm_notice_ip  = '139.129.221.101';
//$gm_login_port= '33001';
$gm_game_port = '33002';

return array(

	'DB_TYPE'               =>  'mysql',
    'DB_HOST'               =>  '139.129.221.101',
    'DB_NAME'               =>  'TexasPoker_GM',
    'DB_USER'               =>  'test',
    'DB_PWD'                =>  'test.2016',
    'DB_PORT'               =>  '3306',
    'DB_PREFIX'             =>  'st_',

    'JSON_ADD_GOLD_ADDR'     => 'http://'.$gm_notice_ip.':'.$gm_game_port.'/wx_recharge',


);