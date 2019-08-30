public function getIndex()
    {
        $session_member_id = session("member.id");
        if(!$session_member_id) {//��½�ж�
            $data['code'] = 400;
            echo json_encode(['data'=>[], 'code'=>400]);exit;
        }
        $prize_arr = array(
            '0' => array('id'=>1,'prize'=>'60���������','v'=>0),
            '1' => array('id'=>2,'prize'=>'20����','v'=>30,'point'=>20),
            '2' => array('id'=>3,'prize'=>'лл����','v'=>5000),
            '3' => array('id'=>4,'prize'=>'5���������','v'=>200),
            '4' => array('id'=>5,'prize'=>'15���������','v'=>1),
            '5' => array('id'=>6,'prize'=>'5����','v'=>4500,'point'=>5),
            '6' => array('id'=>7,'prize'=>'7���������','v'=>70),
            '7' => array('id'=>8,'prize'=>'10����','v'=>200,'point'=>10),
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
        if($alltime >= 9) {//���ÿ���Ĵ���
            echo json_encode(['data'=>[], 'code'=>402]);exit;
        }
        foreach ($prize_arr as $key => $val) {
            $arr[$val['id']] = $val['v'];
        }
        //ʣ������ж�
        $point = M('member')->where(['id'=>$session_member_id])->getField('point');//ҵ���߼�����ɾ
        if($point < 5) {//ҵ���߼�����ɾ
            $data['code'] = 401;
            echo json_encode(['data'=>[], 'code'=>401]);exit;
        }
        $rid = get_rand($arr); //���ݸ��ʻ�ȡ����id  д��һ���������� ��function.php�ļ��У�
       
        if($alltime != 0) { //ҵ���߼�����ɾ
            M('member')->where(['id'=>$session_member_id])->save(['point'=>$point - 5]);
        }
        $title = $prize_arr[$rid-1]['prize']; //�н���
        //�����н���¼
        $data = [
            'mem_id'=>$session_member_id,
            'gid'=>$rid,
            'title'=>$title,
            'create_time'=>time()
        ];
        M('game_record')->add($data);//�н���¼���
        //�������н����ҵ���߼������ݲ�ͬ�ĳ����Լ��޸�
        if(in_array($rid, ['1','6','8'])) {
            //���ֽ�Ʒ����
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