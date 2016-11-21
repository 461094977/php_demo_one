<?php
//$env类型主要有develop(本地),test(cow 服务器))
$env="develop";
$ret=require("config_".$env.".php");
return $ret;
