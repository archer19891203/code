public function getIndex()
    {
        $session_member_id = session("member.id");
        if(!$session_member_id) {//登陆判断
            $data['code'] = 400;
            echo json_encode(['data'=>[], 'code'=>400]);exit;
        }
        $prize_arr = array(
            '0' => array('id'=>1,'prize'=>'60天理财体验','v'=>0),
            '1' => array('id'=>2,'prize'=>'20积分','v'=>30,'point'=>20),
            '2' => array('id'=>3,'prize'=>'谢谢参与','v'=>5000),
            '3' => array('id'=>4,'prize'=>'5天理财体验','v'=>200),
            '4' => array('id'=>5,'prize'=>'15天理财体验','v'=>1),
            '5' => array('id'=>6,'prize'=>'5积分','v'=>4500,'point'=>5),
            '6' => array('id'=>7,'prize'=>'7天理财体验','v'=>70),
            '7' => array('id'=>8,'prize'=>'10积分','v'=>200,'point'=>10),
        );
        $create = M('game_record')->where(['mem_id'=>$session_member_id])->select();
        $alltime = 0;
        if($create) {
            foreach ($create as $row) {
                $cr = date('Y-m-d',$row['create_time']);
                $ct = date('Y-m-d',time());
                if($cr == $ct) {
                    $alltime ++;
                }
            }
        }
        if($alltime >= 9) {//设置可玩的次数
            echo json_encode(['data'=>[], 'code'=>402]);exit;
        }
        foreach ($prize_arr as $key => $val) {
            $arr[$val['id']] = $val['v'];
        }
        //剩余积分判断
        $point = M('member')->where(['id'=>$session_member_id])->getField('point');//业务逻辑，可删
        if($point < 5) {//业务逻辑，可删
            $data['code'] = 401;
            echo json_encode(['data'=>[], 'code'=>401]);exit;
        }
        $rid = get_rand($arr); //根据概率获取奖项id  写了一个公共方法 在function.php文件中，
       
        if($alltime != 0) { //业务逻辑，可删
            M('member')->where(['id'=>$session_member_id])->save(['point'=>$point - 5]);
        }
        $title = $prize_arr[$rid-1]['prize']; //中奖项
        //保存中奖纪录
        $data = [
            'mem_id'=>$session_member_id,
            'gid'=>$rid,
            'title'=>$title,
            'create_time'=>time()
        ];
        M('game_record')->add($data);//中奖记录入库
        //下面是中奖后的业务逻辑，根据不同的场景自己修改
        if(in_array($rid, ['1','6','8'])) {
            //积分奖品发放
            $param = [
                'point'=>$prize_arr[$rid-1]['point'],
                'from'=>5,
                'mem_id'=>$session_member_id,
            ];
            $memberModel = new \Api\Model\MemberModel();
            $memberModel->savePoint($param);
            if($alltime != 0) {
                $cur_point = $point + $prize_arr[$rid - 1]['point'] - 5;
            }else{
                $cur_point = $point + $prize_arr[$rid - 1]['point'];
            }
        }else {
            if($alltime != 0) {
                $cur_point = $point - 5;
            }else {
                $cur_point = $point;
            }
        }
	
        echo json_encode(['data'=>['rid'=>$rid, 'point'=>$point - 5, 'cur_point'=>$cur_point, 'alltime'=>$alltime], 'code'=>200]);
    }