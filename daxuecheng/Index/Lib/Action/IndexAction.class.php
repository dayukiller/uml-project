<?php

import("@.ORG.wechat");
import("ORG.Net.Http");

class IndexAction extends Action {
    public function index() {
        #set the options
        $options = array(
            'token' => '微信公众号码token'
        );  
        $weObj = new Wechat($options);
        $weObj->valid();
        $type = $weObj->getRev()->getRevType();
        $revContent = $weObj->getRev();
        $revFrom = $weObj->getRevFrom();
        $revTo = $weObj->getRevTo();
        echo $type;

        #check the info of the user
        $check = $this->checkAccount($revFrom);
        
        switch($type) {
            case Wechat::MSGTYPE_TEXT:
                $keyword = $weObj->getRevContent();
                $keyword = trim($keyword);
                
                #deal with 'boy'
                if($keyword == "1" && $check == "0") {
                    $model = M('info');
                    $whetherExist = $model->where('userAccount="'.$revFrom.'"')->select();

                    if($whetherExist != null && $whetherExist != false) {
                        $data['sex'] = 1;
                        $list = $model->where('userAccount="'.$revFrom.'"')->save($data);
                        
                        if($list != false) {
                            $weObj->text("欢迎你(萌)汉子!\n请回复相应数字选择您所在的学校:\n1.中山大学\n2.广外\n3.星海\n4.华工\n5.华师\n6.广工\n7.广药\n8.广中医\n9.广美\n10.广州大学\n11.大学城其他地方")->reply();
                        }else {
                            $weObj->text('1 error')->reply();
                        }
                    }else if($whetherExist == null) {
                        $data['userAccount'] = $revFrom;
                        $data['sex'] = 1;
                        $list = $model->add($data);
                        
                        if($list != false) {
                            $weObj->text("欢迎你(萌)汉子!\n请回复相应数字选择您所在的学校:\n1.中山大学\n2.广外\n3.星海\n4.华工\n5.华师\n6.广工\n7.广药\n8.广中医\n9.广美\n10.广州大学\n11.大学城其他地方")->reply();
                        }else {
                            $weObj->text('1 error')->reply();
                        }
                    }
                }
                else if($keyword == "0" && $check == "0") {
                    $model = M('info');
                    $whetherExist = $model->where('userAccount="'.$revFrom.'"')->select();

                    if($whetherExist != null && $whetherExist != false) {
                        $data['sex'] = 0;
                        $list = $model->where('userAccount="'.$revFrom.'"')->save($data);
                        
                        if($list != false) {
                            $weObj->text("欢迎你(萌)妹纸!\n请回复相应数字选择您所在的学校:\n1.中山大学\n2.广外\n3.星海\n4.华工\n5.华师\n6.广工\n7.广药\n8.广中医\n9.广美\n10.广州大学\n11.大学城其他地方")->reply();
                        }
                        else {
                            $weObj->text('0 error')->reply();
                        }
                    }
                    else if($whetherExist == null) {
                        $data['userAccount'] = $revFrom;
                        $data['sex'] = 0;
                        $list = $model->add($data);
                        
                        if($list != false) {
                            $weObj->text("欢迎你(萌)妹纸!\n请回复相应数字选择您所在的学校:\n1.中山大学\n2.广外\n3.星海\n4.华工\n5.华师\n6.广工\n7.广药\n8.广中医\n9.广美\n10.广州大学\n11.大学城其他地方")->reply();
                        }else {
                            $weObj->text('0 error')->reply();
                        }
                    }
                }
                else if($keyword == "51" && $check == "4") {
                    #get the sex of the user
                    $model = M('info');
                    $sex = $model->where('userAccount="'.$revFrom.'"')->getField('sex');
                    if($sex == "0") {
                        $list2 = $model->order('rand()')->where('imagePath != "" ')->limit(1)->select();
                    }else {
                        $list2 = $model->order('rand()')->where('imagePath != "" ')->limit(1)->select();
                    }

                    if($list2 != false && $list2 != null) {  
                        $list2 = $list2[0];
                        
                        $anotherAccount = $list2['userAccount'];
                        $anotherImage = $list2['imagePath'];
                        $anotherImage = 'http://121.199.60.94/daxuecheng/Public/Images/'.$anotherAccount.'.jpg';
                        $likeTime = $list2['likeTime'];
                        #test
                        $imageUrl = 'http://121.199.60.94/daxuecheng/index.php/Index/show/image/'.$anotherAccount;

                        #get sex of the user
                        $sex = $list2['sex'];
                        $schoolId = $list2['schoolId'];
                        $schoolName = $this->getSchoolName($schoolId);


                        if($sex == "0") {
                            $title = "她在这里: ".$schoolName;
                            $intro = "她被赞过".$likeTime."次\n\n回复zan或赞给对方点赞\n如果她刚好赞过你,就能得到她的联系方式啦";
                        }
                        elseif($sex == "1") {
                            $title = "他在这里: ".$schoolName;
                            $intro = "他被赞过".$likeTime."次\n\n回复zan或赞给对方点赞\n如果他刚好赞过你,就能得到他的联系方式啦";
                        }
                        //$intro = '回复zan或赞给对方点赞,如果他/她刚好赞过你,就能得到他/她的联系方式啦';

                        $another = array(
                            "0"=>array(
                                'Title'=>$title,
                                'Description'=>$intro,
                                'PicUrl'=>$anotherImage,
                                'Url'=>$imageUrl
                            )
                        );

                        $weObj->news($another)->reply();
                        #add the current talker into the dbs of the current user
                        $model = M('info');
                        $data['talker'] = $anotherAccount;
                        $list = $model->where('userAccount="'.$revFrom.'"')->save($data);
                    }else {
                        $weObj->text('51 error')->reply();
                    }
                }
                else if($keyword == "zan" || $keyword == "赞" && $check == "4") {
                    $model = M('info');
                    $list = $model->where('userAccount="'.$revFrom.'"')->getField('talker');

                    $modelship = M('Likeship');
                    $list_test = $modelship->where('likeFrom="'.$list.'" AND likeTo="'.$revFrom.'"')->select();
                    
                    if($list_test != false && $list_test != null) {
                        $getContact = M('info');
                        $anotherContact = $getContact->where('userAccount="'.$list.'"')->getField('contactAccount');
                        $anotherContact = "TA也对你点赞了哦~~\nTA的微信号码是".$anotherContact."(去掉w+)\n记得添加好友时说明是在荒岛点赞上认识的吆~";
                        $weObj->text($anotherContact)->reply();

                        $data['likeFrom'] = $revFrom;
                        $data['likeTo'] = $list;
                        $modelship->add($data);

                        #increase the like time
                        $likeTime = $getContact->where('userAccount="'.$list.'"')->getField('likeTime');
                        $likeTime = $likeTime + 1;
                        $data['likeTime'] = $likeTime;
                        $getContact->where('userAccount="'.$list.'"')->save($data);
                    
                        #increse the user's show like time
                        $time = $model->where('userAccount="'.$revFrom.'"')->getField('time');
                        $time = $time + 1;
                        $newData['time'] = $time;
                        $model->where('userAccount="'.$revFrom.'"')->save($newData);
                    }else if($list_test == null) {
                        $weObj->text('TA还没有对你点赞~只有TA也对你点赞才能交换联系方式的哦~')->reply();
                        
                        $data['likeFrom'] = $revFrom;
                        $data['likeTo'] = $list;
                        $modelship->add($data);
                        
                        #increase the like time at the same time
                        $getContact = M('info');
                        $likeTime = $getContact->where('userAccount="'.$list.'"')->getField('likeTime');
                        $likeTime = $likeTime + 1;
                        $data['likeTime'] = $likeTime;
                        $getContact->where('userAccount="'.$list.'"')->save($data);

                        #increse the user's show like time
                        $time = $model->where('userAccount="'.$revFrom.'"')->getField('time');
                        $time = $time + 1;
                        $newData['time'] = $time;
                        $model->where('userAccount="'.$revFrom.'"')->save($newData);
                    }else if($list_test == false) {
                        $weObj->text('2 error')->reply();
                    }
                    //$data['likeFrom'] = $revFrom;
                    //$data['likeTo'] = $list;
                }
                else if(stristr($keyword, "w") && $check == "3" ) {
                    $model = M('info');
                    $data['contactAccount'] = $keyword;
                    $list = $model->where('userAccount="'.$revFrom.'"')->save($data);

                    if($list != false) {
                        $weObj->text("个人信息设置成功!\n欢迎使用荒岛点赞!专属大学城的封闭圈子!\n在这里,您可以:\n*1.点赞交友,回复'51'立即开始点赞交友之旅\n*2.寻找点赞'赞友',回复'互相点赞',点赞不能停!\n*3.回复'一起跑',约TA一起跑内环吧\n*4.回复'树洞',查看大学城各高校树洞及周边最新鲜热辣资讯")->reply();
                    }else {
                        $weObj->text('contact error')->reply();
                    }
                }
                else if($keyword == "互相点赞" && $check == "4") {
                    $model = M('info');

                    $list2 = $model->order('rand()')->where('eachzan = 1')->limit(1)->select();
                    #change the eachzan state of the user
                    $zandata['eachzan'] = 1;
                    $model->where('userAccount="'.$revFrom.'"')->save($zandata);

                    if($list2 != false && $list2 != null) {  
                        $list2 = $list2[0];

                        $contactNum = $list2['contactAccount'];
                        $anotherAccount = $list2['userAccount'];
                        $anotherImage = $list2['imagePath'];
                        
                        $anotherImage = 'http://121.199.60.94/daxuecheng/Public/Images/'.$anotherAccount.'.jpg';
                        $schoolId = $list2['schoolId'];
                        $schoolName = $this->getSchoolName($schoolId);
                        #test
                        $likeTime = $list2['likeTime'];
                        $imageUrl = 'http://121.199.60.94/daxuecheng/index.php/Index/show/image/'.$anotherAccount;
                        #get the info of label
                        $sex = $list2['sex'];
                    
                        #get sex of the user
                        if($sex == "0") {
                            $title = "她在".$schoolName.",等你来点赞!";
                            $intro = "她被赞过".$likeTime."次\n\n她的微信号码是".$contactNum."(去掉w+)\n快加她为好友相互点赞吧!\n记得添加好友时说明是在荒岛点赞上认识的吆~";
                        }
                        elseif($sex == "1") {
                            $title = "他在".$schoolName.",等你来点赞!";
                            $intro = "他被赞过".$likeTime."次\n\n他的微信号码是".$contactNum."(去掉w+)\n快加他为好友相互点赞吧!\n记得添加好友时说明是在荒岛点赞上认识的吆~";
                        }
                        
                        //$intro = '回复zan或赞给对方点赞,如果他/她刚好赞过你,就能得到他/她的联系方式啦';
                        $another = array(
                            "0"=>array(
                                'Title'=>$title,
                                'Description'=>$intro,
                                'PicUrl'=>$anotherImage,
                                'Url'=>$imageUrl
                            )
                        );

                        $weObj->news($another)->reply();
                         #increase the like time
                        $likeTime = $model->where('userAccount="'.$anotherAccount.'"')->getField('likeTime');
                        $likeTime = $likeTime + 1;
                        $data['likeTime'] = $likeTime;
                        $model->where('userAccount="'.$anotherAccount.'"')->save($data);
                        
                        #add the current talker into the dbs of the current user
                        $model = M('info');
                        $data['talker'] = $anotherAccount;
                        $list = $model->where('userAccount="'.$revFrom.'"')->save($data);
                    }else {
                        $weObj->text('51 error')->reply();
                    }
                }
                else if($check == "1" && ($keyword == "1" || $keyword =="2" || $keyword =="3" || $keyword=="4" || $keyword =="5" || $keyword =="6" || $keyword =="7" || $keyword =="8" || $keyword =="9" || $keyword =="10" || $keyword =="11") ) {
                    $model = M('info');
                    
                    $data['schoolId'] = $keyword;
                    $list = $model->where('userAccount="'.$revFrom.'"')->save($data);
                        
                    if($list != false) {
                        $weObj->text("学校设置成功,请上传一张您的照片作为您的头像")->reply();
                    }
                    else {
                        $weObj->text('0 error')->reply();
                    }
                }
                //the run function
                else if($keyword == "一起跑" && $check == "4") {
                    $runship = M('run');

                    $model = M('info');
                    $user = $model->where('userAccount="'.$revFrom.'"')->select();
                    $user = $user[0];

                    $sex = $user['sex'];
                    $isrun = $user['isrun'];
                    
                    if($sex == "0") {
                        if($isrun == "0") {
                            $data['isrun'] = 1;
                            $model->where('userAccount="'.$revFrom.'"')->save($data);
                        }
                        //when the user is a girl
                        $runFriend = $model->where('isrun = 1 AND sex = 1')->order('rand()')->limit(1)->select();

                        if($runFriend != false && $runFriend != null) {
                            $runFriend = $runFriend[0];

                            $runapplyer = $runFriend['userAccount'];
                            $applynum = $runship->where('runfrom="'.$runapplyer.'"')->count();
                            $runapplyerId = $runFriend['userAccount'];
                            $runschool = $runFriend['schoolId'];
                            $applyerSchoolName = $this->getSchoolName($runschool);
                            $runimage = $runFriend['imagePath'];
                            $runContact = $runFriend['contactAccount'];

                            $title = "他在".$applyerSchoolName."(*^__^*)想约你一起跑";
                            $intro = "现在有".$applynum."人报名约他一起跑内环\n想和他一起跑内环吗?\n回复'报名'查看他的联系方式";
                            $anotherImage = 'http://121.199.60.94/daxuecheng/Public/Images/'.$runapplyerId.'.jpg';
                            $imageUrl = 'http://121.199.60.94/daxuecheng/index.php/Index/show/image/'.$runapplyerId;

                            $another = array(
                                "0"=>array(
                                    'Title'=>$title,
                                    'Description'=>$intro,
                                    'PicUrl'=>$anotherImage,
                                    'Url'=>$imageUrl
                                )
                            );

                            $weObj->news($another)->reply();

                            $data['talker'] = $runapplyer;
                            $model->where('userAccount="'.$revFrom.'"')->save($data);
                        }
                    }
                    else if($sex == "1"){
                        if($isrun == "0") {
                            $data['isrun'] = 1;
                            $model->where('userAccount="'.$revFrom.'"')->save($data);
                        }
                        //when the user is a boy
                        $runFriend = $model->where('isrun = 1 AND sex = 0')->order('rand()')->limit(1)->select();

                        if($runFriend != false && $runFriend != null) {
                            $runFriend = $runFriend[0];

                            $runapplyer = $runFriend['userAccount'];
                            $applynum = $runship->where('runfrom="'.$runapplyer.'"')->count();
                            $runapplyerId = $runFriend['userAccount'];
                            $runschool = $runFriend['schoolId'];
                            $applyerSchoolName = $this->getSchoolName($runschool);
                            $runimage = $runFriend['imagePath'];
                            $runContact = $runFriend['contactAccount'];

                            $title = "她在".$applyerSchoolName."(*^__^*)想约你一起跑";
                            $intro = "现在有".$applynum."人报名约她一起跑内环\n想和她一起跑内环吗?\n回复'报名'查看她的联系方式";
                            $anotherImage = 'http://121.199.60.94/daxuecheng/Public/Images/'.$runapplyerId.'.jpg';
                            $imageUrl = 'http://121.199.60.94/daxuecheng/index.php/Index/show/image/'.$runapplyerId;

                            $another = array(
                                "0"=>array(
                                    'Title'=>$title,
                                    'Description'=>$intro,
                                    'PicUrl'=>$anotherImage,
                                    'Url'=>$imageUrl
                                )
                            );

                            $weObj->news($another)->reply();

                            $data['talker'] = $runapplyer;
                            $model->where('userAccount="'.$revFrom.'"')->save($data);
                        }
                    }
                }
                else if($keyword == "报名" && $check == "4") {
                    $runship = M('run');

                    $model = M('info');
                    $list = $model->where('userAccount="'.$revFrom.'"')->select();
                    $list=$list[0];
                    $talker = $list['talker'];

                    $test = $model->where('userAccount="'.$talker.'"')->select();
                    $test=$test[0];

                    $sex = $test['sex'];
                    $contact = $test['contactAccount'];
                    if($sex == "1")
                    {
                        $weObj->text("他的微信号码是".$contact."(去掉w+)\n记得添加好友时说明是在荒岛点赞上认识的吆")->reply();
                    }
                    else if($sex == "0") {
                        $weObj->text("她的微信号码是".$contact."(去掉w+)\n记得添加好友时说明是在荒岛点赞上认识的吆")->reply();
                    }

                    $list=$runship->where('runfrom= "'.$talker.'"AND runapply="'.$revFrom.'"')->select();
                    if($list==null){
                        $data['runfrom'] = $talker;
                        $data['runapply'] = $revFrom;
                        $runship->add($data);
                    }
                }
                else if($keyword == "树洞" && $check == "4") {
                    $title = "点击收看大学城树洞君";
                    $intro = "大学城树洞君,汇聚大学城内各高校最新动态,收集各高校树洞精彩,提供独家新鲜热辣点评,幽默搞笑煽情催泪,这里全都有*^◎^*";
                    $anotherImage = "http://121.199.60.94/daxuecheng/Public/Images/zan.jpg";
                    $imageUrl = "http://m.weibo.cn/page/tpl?containerid=1005052194567714_-_WEIBO_SECOND_PROFILE_WEIBO&itemid=&title=%E5%85%A8%E9%83%A8%E5%BE%AE%E5%8D%9A&&rl=3&luicode=10000011&lfid=1005052194567714";
                    $another = array(
                            "0"=>array(
                                'Title'=>$title,
                                'Description'=>$intro,
                                'PicUrl'=>$anotherImage,
                                'Url'=>$imageUrl
                            )
                        );

                    $weObj->news($another)->reply();
                }
                else if($check == "0") {
                    $weObj->text("想遇见喜欢你的TA?\n想约TA一起跑内环?\n还是只想找个人帮你点赞?\n荒岛点赞,属于大学城的封闭圈子!\n请设置您的基本信息,回复1(汉子),0(妹子)")->reply();
                }
                else if($check == "1") {
                    $weObj->text("请回复相应数字选择您所在的学校:\n1.中山大学\n2.广外\n3.星海\n4.华工\n5.华师\n6.广工\n7.广药\n8.广中医\n9.广美\n10.广州大学\n11.大学城其他地方")->reply();
                }                    
                elseif ($check == "2") {
                    # code...
                    $weObj->text("请上传一张您的照片作为您的头像")->reply();
                }
                elseif ($check == "3") {
                    # code...
                    $weObj->text("请回复您的微信号码,以w开头,如w+微信号,记得加'+'号哦")->reply();
                }
                else {
                    $weObj->text("欢迎订阅荒岛点赞,属于大学城的封闭圈子!\n在这里,您可以:\n*1.点赞交友,回复'51'立即开始点赞交友之旅\n*2.寻找点赞'赞友',回复'互相点赞',点赞不能停!\n*3.回复'一起跑',约TA一起跑内环吧\n*4.回复'树洞',查看大学城各高校树洞及周边最新鲜热辣资讯")->reply();
                }
                break;
            
            case Wechat::MSGTYPE_EVENT:
                # to deal with the subscribe event
                $eventContent = $weObj->getRevEvent();
                if($eventContent['event'] == 'subscribe') {
                    $model = M('info');
                    $list = $model->where('userAccount="'.$revFrom.'"')->select();

                    if($list != null && $list != false) {
                        if($check == "0") {
                            $weObj->text("想遇见喜欢你的TA?\n想约TA一起跑内环?\n还是只想找个人帮你点赞?\n荒岛点赞\n属于大学城的封闭圈子!\n请设置您的基本信息,回复1(汉子),0(妹子)")->reply();
                        }
                        else if($check == "1") {
                            $weObj->text("请回复相应数字选择您所在的学校:\n1.中山大学\n2.广外\n3.星海\n4.华工\n5.华师\n6.广工\n7.广药\n8.广中医\n9.广美\n10.广州大学\n11.大学城其他地方")->reply();
                        }                    
                        elseif ($check == "2") {
                            # code...
                            $weObj->text("请上传一张您的照片作为您的头像")->reply();
                        }
                        elseif ($check == "3") {
                            # code...
                            $weObj->text("请回复您的微信号码,以w开头,如w+微信号,记得加'+'号哦")->reply();
                        }
                        else {
                            $weObj->text("想遇见喜欢你的TA?\n想约TA一起跑内环?\n还是只想找个人帮你点赞?\n荒岛点赞\n属于大学城的封闭圈子!\n在这里,您可以:\n*1.点赞交友,回复'51'立即开始点赞交友之旅\n*2.寻找点赞'赞友',回复'互相点赞',点赞不能停!\n*3.回复'一起跑',约TA一起跑内环吧\n*4.回复'树洞',查看大学城各高校树洞及周边最新鲜热辣资讯")->reply();
                        }
                    }
                    else if($list == null) {
                        $data['userAccount'] = $revFrom;
                        $data['likeTime'] = 0;
                        $list2 = $model->add($data);
                        
                        if($list2 != false) {
                            $weObj->text("想遇见喜欢你的TA?\n想约TA一起跑内环?\n还是只想找个人帮你点赞?\n荒岛点赞\n属于大学城的封闭圈子!\n请设置您的基本信息,回复1(汉子),0(妹子)")->reply();
                        }
                        else {
                            #check the completement of the user's infomation 
                            $weObj->text('describe error')->reply();
                        }   
                    }
                    else {
                        $weObj->text('describe error')->reply();
                    }       
                }
                break;

            case Wechat::MSGTYPE_LOCATION: 
                // $locationContent = $weObj->getRevGeo();
                // $locationx = $locationContent['x'];
                // $locationy = $locationContent['y'];
                // $label = $locationContent['label'];

                // //$weObj->text($revFrom)->reply();
                // #store the location info into the db
                // $model = M('Anypaiinfo');
                // $data['locationX'] = $locationx;
                // $data['locationY'] = $locationy;
                // $data['lable'] = $label;
                // $list = $model->where('userAccount="'.$revFrom.'"')->save($data);

                // if($list != false) {
                //     $res = "位置信息保存成功,请上传一张您的照片";
                //     $weObj->text($res)->reply();
                // }
                // else {
                //     $weObj->text('location error')->reply();
                // }
                $weObj->text('位置功能正在玩命开发中!')->reply();
                break;

            case Wechat::MSGTYPE_IMAGE:
                $imageContent = $weObj->getRevPic();
                #download the picture to my server
                $filename = $revFrom;
                $loadUrl = "/var/www/daxuecheng/Public/Images/".$revFrom.".jpg";
                
                Http::curlDownload($imageContent, $loadUrl);
                $model = M('info');
                $data['imagePath'] = $revFrom.'.jpg';
                $list = $model->where('userAccount="'.$revFrom.'"')->save($data);

                if($list != false) {
                    $weObj->text("图片上传成功,请回复您的微信号码,以w开头,如w+微信号,记得加'+'号哦")->reply();
                }
                else {
                    $weObj->text("图片更新成功!\n*1.回复'51'立即开始点赞交友之旅\n*2.寻找点赞'赞友',回复'互相点赞',点赞不能停\n*3.回复'一起跑',约TA一起跑内环吧\n*4.回复'树洞',查看大学城各高校树洞及周边最新鲜热辣资讯")->reply();
                }
                break;
            default:
                $weObj->text("欢迎订阅荒岛点赞!\n专属大学城的封闭圈子!\n在这里,您可以:\n*1.点赞交友,回复'51'立即开始点赞交友之旅\n*2.寻找点赞'赞友',回复'互相点赞',点赞不能停!\n*3.回复'一起跑',约TA一起跑内环吧\n*4.回复'树洞',查看大学城各高校树洞及周边最新鲜热辣资讯")->reply();   
        }
    }

    //show the user image 
    public function show() {
        $subUrl = I('get.image');
        $imageUrl = 'http://121.199.60.94/daxuecheng/Public/Images/'.$subUrl.'.jpg';

        $model = M('info');
        $list = $model->where('userAccount="'.$subUrl.'"')->select();
        $list = $list[0];
        
        $sex = $list['sex'];
        $likeTime = $list['likeTime'];

        $this->assign('imageUrl', $imageUrl);
        $this->assign('sex', $sex);
        $this->assign('likeTime', $likeTime);

        $this->display('show');
    }
    //get the school name accoring to the index
    protected function getSchoolName($schoolId) {
        switch ($schoolId) {
            case '1':
                # code...
                return '中山大学';
                break;

            case '2':
                # code...
                return '广外';
                break;

            case '3':
                # code...
                return '星海';
                break;
            
            case '4':
                # code...
                return '华工';
                break;

            case '5':
                # code...
                return '华师';
                break;

            case '6':
                # code...
                return '广工';
                break;

            case '7':
                # code...
                return '广药';
                break;

            case '8':
                # code...
                return '广中医';
                break;

            case '9':
                # code...
                return '广美';
                break;

            case '10':
                # code...
                return '广州大学';
                break;

            case '11':
                # code...
                return '大学城';
                break;
            default:
                # code...
                break;
        }
    }
    //check the implementation of the user info
    protected function checkAccount($revFrom) {
        $model = M('info');
        $list = $model->where('userAccount="'.$revFrom.'"')->select();
        $list = $list[0];
                
        $sex = $list['sex'];
        $schoolId = $list['schoolId'];
        $imagePath = $list['imagePath']; 
        $contactAccount = $list['contactAccount'];

        if($sex == null) {
            #remain to input sex
            return 0;
        }else if($sex != null && $schoolId == 0 && $imagePath == null && $contactAccount == null) {
            #remain to input location
            return 1;
        }elseif ($sex != null && $schoolId != 0 && $imagePath == null && $contactAccount == null ) {
            #remain to input imagepath
            return 2;
        }else if($sex != null && $schoolId != 0 && $imagePath != null && $contactAccount == null) {
            #remian to input contactAccount
            return 3;
        }else if($sex != null && $schoolId != 0 && $imagePath != null && $contactAccount != null) {
            #remain complete
            return 4;
        }
    }
}