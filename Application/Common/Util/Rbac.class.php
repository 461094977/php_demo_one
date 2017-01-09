<?php
namespace Common\Util;
use Common\Logic\User;

class Rbac {

    //用户当前方法是否具有权限
    public static function accessUserByUrl($model = MODULE_NAME,$controller = CONTROLLER_NAME,$action = ACTION_NAME){
        $rodeId = User::sessionUserRodeId();
        if(!$rodeId){
            return false;
        }
        $nodeListIndex = User::sessionUserAccess();
        if(!$nodeListIndex){
            $nodeList = static::accessList($rodeId);
            list($nodeListLeveL1,$nodeListLeveL2,$nodeListLevel3) = $nodeList;
            $nodeListIndex = static::nodeListDeal($nodeListLeveL1,$nodeListLeveL2,$nodeListLevel3);
            User::sessionUserAccess($nodeListIndex);
        }
        if($nodeListIndex[$model][$controller][$action]){
            return true;
        }
        return false;
    }

    //节点数据列表 数据结构处理
    public static function nodeListDeal($nodeListLeveL1,$nodeListLeveL2,$nodeListLevel3){
        $nodeListIndex = array();
        foreach($nodeListLeveL1 as $v1){
            $moudelName = $v1['name'];
            $moudeNodeId = $v1['id'];
            foreach($nodeListLeveL2 as $v2){
                if($moudeNodeId!=$v2['pid']){
                  continue;
                }
                $controllerName = $v2['name'];
                $controllerNodeId = $v2['id'];
                foreach($nodeListLevel3 as $v3){
                    if($controllerNodeId!=$v3['pid']){
                        continue;
                    }
                    $actionName = strtolower($v3['name']);
                    $actionNodeId = $v3['id'];
                    $nodeListIndex[$moudelName][$controllerName][$actionName] = $actionNodeId;
                }
            }
        }
        return $nodeListIndex;
    }

    //根据用户角色所有权限 获取权限node信息
    public static function accessList($rodeId){
        $accessModel = getModel('ThinkAccess');
        $nodeIdsLevel3 = $accessModel->getRow(array('roleId'=>$rodeId),'node_id',true);
        $nodeModel = getModel('ThinkNode');
        $nodeListLevel3 = $nodeModel->getList(array('nodeId'=>$nodeIdsLevel3,'level'=>3),'id,pid,name',true);
        $nodeIdslevel2 = get_array_column( $nodeListLevel3 , 'pid' );
        $nodeListLeveL2 = $nodeModel->getList(array('nodeId'=>$nodeIdslevel2,'level'=>2),'id,pid,name',true);
        $nodeIdslevel1 = get_array_column( $nodeListLeveL2 , 'pid' );
        $nodeListLeveL1 = $nodeModel->getList(array('nodeId'=>$nodeIdslevel1,'level'=>1),'id,pid,name',true);
        return array($nodeListLeveL1,$nodeListLeveL2,$nodeListLevel3);
    }

    //获取用户具有的菜单权限菜单 当前只针对2级
    public static function getAuthMenuList(){
        $userId = User::sessionUserId();
        $rodeId = User::sessionUserRodeId();
        if(!$rodeId&&$userId!=ADMIN_USER_ID){
            return array();
        }
        $menu = User::sessionMenuAccess();
        if(!$menu){
            $menuModel = getModel('ThinkMenu');
            $menuListLevel1 = $menuModel->getList(array('level'=>1,'isShow'=>1));
            //$menuListLevel1 = arrayIndex($menuListLevel1,'id');
            if($userId==ADMIN_USER_ID){//初始帐号Admin
              $menuListLevel2 = $menuModel->getList(array('level'=>2,'isShow'=>1));
            }else{//其他所有帐号 根据权限取2级菜单
              $accessModel = getModel('ThinkAccess');
              $nodeIds = $accessModel->getRow(array('roleId'=>$rodeId),'node_id',true);
              if($nodeIds){
                  $menuListLevel2 = $menuModel->getList(array('level'=>2,'isShow'=>1,'nodeId'=>$nodeIds));
                }
            }
            $menu = static::menuToArr($menuListLevel1,$menuListLevel2);
            User::sessionMenuAccess($menu);
        }
        return $menu;
    }

    //转换菜单栏目结构
    public static function menuToArr($menuListLevel1,$menuListLevel2){
        if(!$menuListLevel1||!$menuListLevel2){
            return array();
        }
        $menuLevel2 = array();
        foreach($menuListLevel2 as $k=>$v){
            $menuLevel2[$v['pid']][] = $v;
        }
        $menuList = array();
        /*foreach($menuLevel2 as $k=>$v){
            $menuList[$k] = $menuListLevel1[$k];
            $menuList[$k]['child'] = $v;
        }*/
        foreach($menuListLevel1 as $k=>$v){
            $id = $v['id'];
            $sub = $menuLevel2[$id];
            if($sub){
                $menuList[$k] = $v;
                $menuList[$k]['child'] = $sub;
            }
        }
        return $menuList;
    }

}