<?php
///////////////////////////////////////////////////////
// 教材列表接口
// by 孙峻峰 v1.0
///////////////////////////////////////////////////////
	//rest接口
	require_once(dirname(__FILE__)."/../rest.php");
	

	class crest extends REST{
		//GET逻辑
		public function doGET(){
			if($this -> vr['pass']){
				$this -> page = explode(",",$this -> urlarr[4]);

				if($this -> page==null or !is_numeric($this -> page[0]) or !is_numeric($this -> page[1])){
					$this -> b["sc"] = 403;
					return;
				}
				$this -> getList();
			}
		}
	
		//POST逻辑
		public function doPOST(){
			$this -> b["sc"] = 405;
		}
	
		//PUT逻辑
		public function doPUT(){
			$this -> b["sc"] = 405;
		}
	
		//DELETE逻辑
		public function doDELETE(){
			$this -> b["sc"] = 405;
		}
		
		//获取教师列表
		public function getList(){
			$get = explode(";",$this -> v($this -> urlarr[5]));
			for($i=0;$i<count($get);$i++){
				$str .= $get[$i].",";
			}
			$str = substr($str,0,-1);
			if(strlen($_REQUEST['condition'])!=0){
				$c = explode(";",$this -> r("condition"));
				for($i=0;$i<count($c);$i++){
					$cc = explode(":",$c[$i]);
					if($cc[0]=="grade_id")
					{
						if ($cc[1]==13 || $cc[1]==14){ $aaa=19 ;} else {$aaa=$cc[1];}

						$condition .= $cc[0]."=".$aaa." and ";

						if ($cc[1]==13)
						  { $condition .=" notes like '选修%' and ";	}
						if ($cc[1]==14)	
						{
						  { $condition .=" notes like '必修%' and ";	}	
						}
					}
					else
					{
						if(is_string($cc[1])){
							$condition .= $cc[0]."='".$cc[1]."' and ";
						}
						else{
							$condition .= $cc[0]."=".$cc[1]." and ";
						}
					}
				}
				$condition = substr($condition,0,-5);
				$this -> db -> sql = "select ".$str." from edu_books where ".$condition." limit ".$this -> page[0].",".$this -> page[1];
			}
			else{
				$this -> db -> sql = "select ".$str." from edu_books limit ".$this -> page[0].",".$this -> page[1];
			}
			
			//$this -> b['sql'] = $this -> db -> sql;
			$this -> db -> Query();
			$this -> b['book'] = $this -> db -> rs;
			$this -> b["sc"] = 200;
		}

	}
	


?>