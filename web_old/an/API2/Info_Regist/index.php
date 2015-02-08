<?php
///////////////////////////////////////////////////////
// 注册信息接口
// by 孙峻峰 v1.0
///////////////////////////////////////////////////////

	require_once(dirname(__FILE__)."/../../core/interface.php");
	require_once(dirname(__FILE__)."/../../include/Fmail.class.php");
	
	class rss extends rs{
		//验证通过处理
		public function onsucess(){
			$h = $this->verifyemail();
			//echo $h['nu'];
			if($h['nu']!=0){
				$this -> arr["sc"] = 400;
			}
			else{
				$this->insertnew();
				
				//发信设置
				$svr = array(
					'smtp' => 'smtp.exmail.qq.com',
					'user' => 'support@hxnetwork.com',
					'pass' => 'hx123456',
					'host' => 'smtp.exmail.qq.com'
				); 
				
				$fmail = new Fmail($svr, false);			
				
				$mail = array(
					'name' => '华夏安业题库',
					'from' => 'support@hxnetwork.com',
					'to' => $_REQUEST['email'],
					'cc' => $_REQUEST['email'],
					'subject' => '华夏安业题库账号激活(重要)',
					'cont' => '<div>欢迎注册华夏安业题库账号</div><div>您可复制以下地址到浏览器用来激活华夏安业题库账号，从而使用完整功能。</div><div>https://edu.hxpad.com/jihuo.php?username='.$_REQUEST['email'].'&code='.$this -> code.'</div><div>如果您未使用此邮箱注册账号，请勿激活。</div>',
					'cont_type' => Fmail::CONT_TYPE_HTML, // html格式
					'apart' => true // 隐藏To、Cc和Bcc
				);
				
				if($fmail->send($mail)) {
					$this -> arr["sc"] = 200;
				}
				else {
					$this -> arr["sc"] = 400;
				}
				$fmail->close();
			}
		}
		//查找是否有同名email
		public function verifyemail(){
			/* if(!preg_match("/^[0-9a-zA-Z_-]+(\.[0-9a-zA-Z_-]+)*@[a-zA-Z0-9_-]+(\.{1}[a-zA-Z0-9_-]+)*\.{1}[a-zA-Z]{2,4}$/i",$_REQUEST['email'])){
				return true;
			} */
			return $this -> db -> Queryif('usr_user',"username='".$_REQUEST['email']."'");	
		}
		//插入新用户
		public function insertnew(){
			$dbstring = $this -> randStr(16) ;
			$token = $this -> randStr(128) ;
			$ip = $this -> get_real_ip();			
			if($_REQUEST['nick']==""){
				$nick = null;
			}
			else{
				$nick = $_REQUEST['nick'];
			}
			
			$code = $this -> randStr(64) ;
			$this->code = $code;
			
			
			$this -> db -> sql = "insert into usr_user (token,last_login_time,reg_time,last_loginlocation,dbstring,username,passwd,nickname,code,usr_type ) values('$token',current_timestamp(),current_timestamp(),'$ip','$dbstring','".$_REQUEST['email']."','".$_REQUEST['pw']."','".$nick."','".$code."',1)";
			//$this -> db -> sql = "";
			$this -> db -> ExecuteSql();
			$userid = $this -> db -> rs ["id"];
			$this -> db -> sql = "insert into usr_student (userid) values('$userid')";
			$this -> db -> ExecuteSql();
			//$this -> arr["dbstring"] = $dbstring;
			$this -> arr["username"] = $_REQUEST['email'];
			$this -> arr["token"] = $token;
		}
	}
	
	
	$rs = new rss("POST",array("email","pw","func"));
	
