<?php
/**
 * Created by PhpStorm.
 * User: dizzylee
 * Date: 2017/7/8
 * Time: 下午3:42
 */

namespace Home\Model;
use Think\Model;


class DepartmentModel extends BaseadvModel
{
    protected $_validate = array(
        //createNewDepartment
        array('depart_name', '1,20', -24101, self::MUST_VALIDATE,'length',self::DepartmentModel_createDepartment),//部门名不合法
        array('status', 'checkDeny_bool_status', -24102, self::EXISTS_VALIDATE, 'function',self::DepartmentModel_createDepartment),//status不合法
        array('depart_name', 'checkDeny_Depart_name', -24106, self::EXISTS_VALIDATE,'callback',self::DepartmentModel_createDepartment),//部门名重复
        array('remark','0,200',-24105,self::EXISTS_VALIDATE,'length',self::DepartmentModel_createDepartment),//部门备注不合法
        //deleteDepartment'
        array('depart_id','checkDeny_depart_id',-24104,self::MUST_VALIDATE,'callback',self::DepartmentModel_deleteDepartment),
        //editDepartment
        array('depart_id','checkDeny_depart_id',-24104,self::MUST_VALIDATE,'callback',self::DepartmentModel_editDepartment),
        array('depart_name', '1,20', -24101, self::EXISTS_VALIDATE,'length',self::DepartmentModel_editDepartment),//部门名不合法
        array('status', 'checkDeny_bool_status', -24102, self::EXISTS_VALIDATE, 'function',self::DepartmentModel_editDepartment),//status不合法
        array('remark','0,200',-24105,self::EXISTS_VALIDATE,'length',self::DepartmentModel_editDepartment),//部门备注不合法
    );
    /**创建新的部门
     * @param  $depart_name 部门名称
     * @param  $status 是否启用 0不启用 1启用
     * @param  $remark 备注
     * @return mixed
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */
    public function createDepartment($data = null)
    {
        if (!$this->field('depart_name,status,remark')->create($data,self::DepartmentModel_createDepartment))
            throw new \XYException(__METHOD__,$this->getError());
        if (!isset($data['status']))
            $data['status'] = 1;
        $data['admin_uid'] = getAdminUid();
        $data['reg_time'] = date('y-m-d h:m:s');
        $data['update_time'] = date('y-m-d h:m:s');
        $operate = D('Department')->add($data);
        if ($operate === false || $operate === null)
            throw new \XYException(__METHOD__,-8000);
        if ($operate >0)
            return $operate;
    }

    /**
     * @param $depart_id 必填
     * @param $depart_name 部门名字
     * @param $remark 备注
     * @param $status 状态 0或1
     * @return int|string
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */

    public function editDepartment($data = null)
    {
        if (!$this->field('depart_id,depart_name,status,remark')->create($data,self::DepartmentModel_editDepartment))
            throw new \XYException(__METHOD__,$this->getError());
        $map['depart_name'] = $this->depart_name;
        $map['depart_id'] = array('neq',$this->depart_id);
        $queryData = $this->where($map)->find();
        if (is_array($queryData))
           throw new \XYException(__METHOD__,-24106);
        $this->update_time = date('y-m-d h:m:s');
        $operate = $this->save();
        if ($operate === false || $operate === null)
            throw new \XYException(__METHOD__,-8000);
        if ($operate == 0)
            return '未做任何修改';
        if ($operate>0)
            return 1;
    }

    /**
     * 部门查询;
     * @return $queryData
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */
    public function getDepartment()
    {
        $map['admin_uid'] = getAdminUid();
        $map['status'] = array('in','0,1');
        $queryData = D('Department')->where($map)->select();
        if ($queryData === null || $queryData === false)
            throw new \XYException(__METHOD__,-8000);
        return $queryData;
    }

    /**
     * @param $depart_id 主键
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */
    public function deleteDepartment($data = null)
    {
        if (!$this->field('depart_id')->create($data,self::DepartmentModel_deleteDepartment))
            throw new \XYException(__METHOD__,$this->getError());
        $queryData = $this->where(array('depart_id' => $this->depart_id))->find();
        if ($queryData === false || $queryData === null)
            throw new \XYException(__METHOD__,-8000);
        if ($queryData['status'] != 1 && $queryData['status'] != 0)
            throw new \XYException(__METHOD__,-24103);
        $queryData['depart_name'] = $queryData['depart_name'].C('DELETED_MARKER');
        $queryData['status'] = -1;
        $queryData['update_time'] = date('y-m-d h:m:s');
        $operate = D('Department')->where(array('depart_id' => $queryData['depart_id']))->save($queryData);
        if ($operate ===false || $operate === null)
            throw new \XYException(__METHOD__,-8000);
        if ($operate == 0)
            return '未进行任何修改';
        if ($operate > 0)
            return 1;
    }
}